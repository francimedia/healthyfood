<?php

namespace Collection;


class Tracking_Log 
{

    protected static $_table_name = 'tracking_log';

    // get items for scanning, oldest scan first.

    public static function getQueuedItems($source, $frequency = 'hourly', $limit = 100, $region_id = false) {

		$query = \DB::select()->from(self::$_table_name);
		
		$query->join('tracking_cycle');
		$query->on('tracking_cycle.object_id', '=', 'tracking_log.object_id');
		
		$query->join('singleton');
		$query->on('tracking_log.object_id', '=', 'singleton.id');

		$query->where('tracking_log.success', 1);
		$query->where('tracking_log.is_latest', 1);
		$query->where('tracking_cycle.frequency', $frequency); 
		$query->where('tracking_log.source', $source); 

		if($region_id) {
			$query->join('venue');
			$query->on('venue.id', '=', 'singleton.id'); 
			$query->where('venue.region_id', $region_id); 			
		}

		switch($frequency) {
			case 'quaterday':				
				$query->where('tracking_log.created_at', '<=', \DB::expr('DATE_SUB(NOW(),INTERVAL 6 HOUR)')); 
				break;
			case 'hourly':				
				$query->where('tracking_log.created_at', '<=', \DB::expr('DATE_SUB(NOW(),INTERVAL 1 HOUR)')); 
				break;
			case 'daily':				
				$query->where('tracking_log.created_at', '<=', \DB::expr('DATE_SUB(NOW(),INTERVAL 1 DAY)')); 
				break;
			case 'weekly':				
				$query->where('tracking_log.created_at', '<=', \DB::expr('DATE_SUB(NOW(),INTERVAL 7 DAY)')); 
				break;
			case 'monthly':				
				$query->where('tracking_log.created_at', '<=', \DB::expr('DATE_SUB(NOW(),INTERVAL 1 MONTH)')); 
				break;
			default:
    			\Cli::error('Failure: Invalid frequency type');
				return;
		}

		if(1) {
			$query->join('venue_record');
			$query->on('venue_record.id', '=', 'singleton.id'); 
			$query->order_by('venue_record.checkin', 'desc'); 
		} else {
			$query->order_by('tracking_log.created_at', 'asc'); 	
		}

		$query->limit($limit); 
		$tracking_logs = $query->execute();    	

 		// echo \DB::last_query();
		// exit;

		return $tracking_logs;

    }  

    public static function getCurrentRateLimit($service = 'foursquare') {
   		if($Log_Ratelimit = \Model_Tracking_Log_Ratelimit::query()->where('service', $service)->where('limit', '!=', 0)->limit(1)->order_by('created_at', 'desc')->get_one() ) {
   			return $Log_Ratelimit->remaining;
   		}
    }

}

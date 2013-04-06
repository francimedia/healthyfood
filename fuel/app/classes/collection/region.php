<?php

namespace Collection;
 
class Region
{
 

    public static function countSearchResults($options = array()) {
    	return self::search($options, true);
    }	

    public static function search($options = array(), $count = false) {

    	$columns = $count ? \DB::expr('COUNT(*) as count') : '*';

    	$query = \DB::select($columns)->from('tracking_region');	 

		// sorting options
		$options['order_by'] = isset($options['order_by']) ?  $options['order_by'] : 'name';
		$options['order_dir'] = isset($options['order_dir']) ?  $options['order_dir'] : 'asc';
		$query->order_by($options['order_by'], $options['order_dir']);

		if($count) { 
			$result = $query->execute();
			$result_arr = $result->current();
			return $result_arr['count']; 
		}

    	// set paging default options
    	$options['per_page'] = isset($options['per_page']) ?  $options['per_page'] : 100;
    	$options['offset'] = isset($options['offset']) ?  $options['offset'] : 0;
 
		$query->limit($options['per_page']);
		$query->offset($options['offset']);
 
		return $query->as_object()->execute();


    }
 
}
<?php

namespace Spider\Instagram;


use Spider\BaseSpider as BaseSpider;

use Fuel\Core\Package as Package;
use Fuel\Core\Config as Config;
use Fuel\Core\Cache as Cache;

class Spider extends BaseSpider
{  
 
	public function __construct() {
		parent::__construct();
	}
 
 	public function updateFoursquareVenue($foursquare_venue_id, $cycles = 5) {
 		if($venue = \Collection\Venue::getVenueByFoursquareId($foursquare_venue_id) ) {
 			return $this->updateVenue($venue->id, $cycles);
 		}
	}
 
 	public function updateVenue($system_venue_id, $cycles = 5) {

		$this->cliOutput('write', 'system_venue_id: ' . $system_venue_id);

		$InstagramClient = new \Instagram\Client;

		// switch($table) {
		// 	case 'venue':
		// first need to get the corresponding instagram place ID
		$instagram_venue_id = \Collection\Venue::getInstagramVenueId($system_venue_id);
		if(!$instagram_venue_id) {
			$foursquare_venue_id = \Collection\Venue::getFoursquareVenueId($system_venue_id);
			// get the ID via API
			$location = $InstagramClient->get('getLocation', $foursquare_venue_id);	
			
			if(isset($location->data[0]->id) && $location->data[0]->id) {
				$instagram_venue_id = \Collection\Venue::saveInstagramVenueJsonToDB($location->data[0], $system_venue_id);							
			}

			if(!$instagram_venue_id) {
				$this->cliOutput('write', 'no instagram_venue_id: ' . $system_venue_id);
				continue;
			}
		}

		// get pictures... 
		$lastId = 0;
		$media = array();
		for($i=0;$i<$cycles;$i++) {
			$response = $InstagramClient->getMediaByLocation($instagram_venue_id, $lastId);	
			if(!isset($response->data)) {
				break;
			}
			$media = array_merge($media, $response->data);
			 
			if(!isset($response->pagination->next_max_id) || $lastId == $response->pagination->next_max_id) {
				break;
			} 

			$lastId = $response->pagination->next_max_id;

		} 

		$this->cliOutput('write', 'instagram_venue_id: ' . ($instagram_venue_id));
		$this->cliOutput('write', 'pictures: ' . count($media));

		foreach ($media as $key => $media) {
			$response = \Collection\Interaction::saveInstagramJsonToDB($media, $system_venue_id);
		} 

		return true;
 	}    	

    // try to update locations smart
    // at the moment just scan at random times, goal: track one location at a similar time. example: 01:00am-02:00am
    public function updateVenues($region_id, $cycles = 5) {    	

	   	if(!$region_id) {
    		return false;
    	}
 
    	$empty_responses = 0;
		
		// to get inital entries, just use the foursquare log
		// $tracking_logs = \Collection\Tracking_Log::getQueuedItems('instagram');
 		$tracking_logs = \Collection\Tracking_Log::getQueuedItems('foursquare', 'hourly', 100, $region_id);

		if($tracking_logs->count() == 0) {
			$this->cliOutput('error', 'Nothing to scan!??? Waiting for 5 minutes.');
			$this->cliOutput('wait', 300);
		}		

		// a bunch of foursquare location IDs
		$groupedObjects = \Collection\Mixed::convertObjectIds($tracking_logs);

		foreach($tracking_logs as $table => $tracking_log) {
			$this->updateVenue($tracking_log['object_id'], $cycles);
		}

		return true;

	}
 

}
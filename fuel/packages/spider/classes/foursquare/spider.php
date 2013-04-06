<?php

namespace Spider\Foursquare;


use Spider\BaseSpider as BaseSpider;

use Fuel\Core\Package as Package;
use Fuel\Core\Config as Config;
use Fuel\Core\Cache as Cache;

class Spider extends BaseSpider
{ 
 
	public function __construct() {
		parent::__construct();
	}


    public function scanRegion($region_id)
    { 
	   	if(!$region_id) {
    		// die('Please provide region id');
    		return false;
    	}
 
    	$Region = \Model_Tracking_Region::find($region_id);
    	
    	if(!$Region) {
    		// die('Region not found');
    		return false;
    	}
 
    	// get all tracking points of this location
    	$Points = \Model_Tracking_Point::query()->where('region_id', $region_id)->where('scanned', '!=', 1)->limit(300)->get();

    	$this->debugMessage('Loaded '.count($Points).' tracking points for region "'.$Region->name.'" from database.');

    	foreach($Points as $Point) {
    		// this script is not for updating, just first time adding points, so just skip if scanned once.
    		if($Point->scanned ) {
    			$this->debugMessage('Skipped '.$Point->lat . ',' . $Point->lng);
    			continue;
    		}

    		$this->searchForLocations($Point->lat, $Point->lng, 200, '', $region_id);
    		
    		// scan complete..
    		$Point->scanned = 1;
    		$Point->save();
    	}

    	return true;

    }

    public function searchForLocations($lat = 40.761821, $lng = -73.974895, $radius = 1000, $query = '', $region_id = false)
    { 

		$FoursquareClient = new \Foursquare\Client;

  		// get a setting of category IDs
  		$categoryId = $this->getVenueCategoryIds('healthy_food');

		// get locations surrounding lat/lng geo point.
		$locations = $FoursquareClient->get('searchLocation', $lat.','.$lng, $radius, 50, $categoryId, $query);

		foreach ($locations->response->venues as $key => $venue) {
			$response = \Collection\Venue::saveVenueJsonToDB($venue, $region_id);
			if(!$response) {
				// skip this venue if it already exists
				$this->debugMessage('Venue already exists: '.$venue->name . ' / ' . $venue->id);
				continue;
			}
			
			$this->debugMessage($response['log']);
			
			// create a default "1 scan per hour" entry to the tracking cycle
			// \Collection\Tracking_Cylce::create($response['system_venue_id'], 'hourly');			

			$Cycle = new \Model_Tracking_Cycle();
			$Cycle->object_id = $response['system_venue_id'];
			$Cycle->frequency = 'hourly';
			// 2do: manage timezone based tracking
			// $Cycle->TZ = 'ET';
			$Cycle->save();				

			// if we didn't get back a stats array, an error occured
			if(!is_array($response['stats'])) {
				$this->CliOutput('error', 'Failure: '.$response['stats']);
			} else {
				$this->debugMessage('Stats: '.implode(', ', $response['stats'])); 			 			
			}


		}
   
    }


    // simple, no logic, just do it for all
    public function updateAllLocations() {
		$FoursquareClient = new \Foursquare\Client;
		$TrackingCycles = \Model_Tracking_Cycle::query()->where('venue_foursquare_id', $venue_foursquare_id)->limit(10)->get();
    }

    public function getCurrentRateLimit() {
   		return \Collection\Tracking_Log::getCurrentRateLimit('foursquare');
    }

    // try to update locations smart
    // at the moment just scan at random times, goal: track one location at a similar time. example: 01:00am-02:00am
    public function updateVenues() {    	

    	$locationsUpdated = 0;
    	$empty_responses = 0;

		$FoursquareClient = new \Foursquare\Client;
		$tracking_logs = \Collection\Tracking_Log::getQueuedItems('foursquare');
 		
		if($tracking_logs->count() == 0) {
			$this->cliOutput('error', 'Nothing to scan!??? Waiting for 5 minutes.');
			$this->cliOutput('wait', 300);
		}		

		// a bunch of foursquare location IDs
		$groupedObjects = \Collection\Mixed::convertObjectIds($tracking_logs);

		foreach($groupedObjects as $table => $groupedObjectIds) {
			switch($table) {
				case 'venue':
					// foursquare: it's possible to make a batch request containing 5 venue IDs
					$ids = array_chunk($groupedObjectIds, 5, true);
					foreach($ids as $venue_ids) {
						$locations = $FoursquareClient->getVenueInfoBatch($venue_ids);
						// print_r($locations);
						// exit;

						if(!isset($locations->response->responses[0]->meta->code) && $locations->response->responses[0]->meta->code == 403) {
							$this->cliOutput('error', 'Failure: rate_limit_exceeded Quota exceeded');
							return;
						}

						if(!isset($locations->response->responses)) {
							$this->cliOutput('error', 'Warning: Empty response (1)');	
							$empty_responses++;
								if($empty_responses > 3) {
									// $this->cliOutput('error', 'Failure: rate_limit_exceeded Quota exceeded');
									// print_r($venue);
									// exit;
								}
							continue;
						}
						foreach ($locations->response->responses as $key => $venue) {
							if(!isset($venue->response->venue)) {
								$this->cliOutput('error', 'Warning: Empty response (2)');	
								$empty_responses++;
								if($empty_responses > 3) {
									// print_r($venue);
									// exit;
									return false;
								}
								continue;
							}
							$system_venue_id = array_search($venue->response->venue->id, $venue_ids);
							if(!$system_venue_id) {
								$this->cliOutput('error', 'Failure: Unable to get venue ID: ' .$venue->response->venue->id);	
								$this->cliOutput('error', 'IDs: ' . print_r($groupedObjects));	
								// $empty_responses++;
								// if($empty_responses > 3) {
									// print_r($venue);
									// exit;
								// }
								continue;
							}

 							$stats = \Collection\Venue::saveVenueStats($system_venue_id, $venue->response->venue, true);
 							\Collection\Venue::createOrUpdateVenueMetaFoursquare($system_venue_id, $venue->response->venue);

 							if(!is_array($stats)) {
 								$this->CliOutput('error', 'Failure: '.$stats);
 								continue;
 							}

							$this->debugMessage('Updated: '.$venue->response->venue->name . ' / ' . $venue->response->venue->id);
							$this->debugMessage('Stats: '.implode(', ', $stats)); 		
							$locationsUpdated++;					
						}
					}
					break;
				default:
					break;
			}
			
		}
	 
	 	$this->emailReport('Locations updated: '. $locationsUpdated);
	 	return true;
		// echo \DB::last_query();

	}

    public function emailReport($message) {
        Package::load('email');        

        // Create an instance
        $email = \Email::forge();

        // Set the from address
        $email->from('report@fashiondashboard.net', 'FD Report');

        // Set the to address
        $email->to('stephanalber@gmx.de', 'Stephan Alber');

        // Set a subject
        $email->subject('Report: '.date('Y/m/d H:i')); 

        // And set the body.
        $email->body($message);

    }


	public function calculateDeltas() {

		$query = \DB::select_array();
        $query->from('record');
        $query->where('datatype', 'absolute');
        $query->where('property', 'NOT IN', array('herenow', 'rating'));
        $query->order_by('object_id');
        $query->order_by('property');
        $query->order_by('created_at', 'asc');
        // $query->limit(50);

        $Results = $query->execute();   

        $last_value = -1;
        $object_id = -1;
        $data = array();
        foreach($Results->as_array() as $key => $row) {
        	if(!$row['object_id'] || !$row['property']) {
        		continue;
        	} 

        	if($object_id != $row['object_id']) {
        		$data = array();
        		$property = $row['property'];
        		$object_id = $row['object_id'];
        		$last_value = -1;
				$this->cliOutput('write', 'object_id: ' . $row['object_id']);
        	}

        	if($property != $row['property']) {
        		$property = $row['property'];
        		$last_value = -1;
        	} 

        	if($last_value != -1) {
				$Record = new \Model_Record();
				// $Record->record_hash = $row['record_hash'].'r';  
				$Record->object_id = $row['object_id'];   
				$Record->property = $property;
				$Record->value = $row['value'] - $last_value; 
				$Record->datatype = 'relative'; 
				$Record->save();  

				$Record->created_at = $row['created_at'];   
				$Record->save();  		
        	} 

        	$last_value = $row['value'];
        }

	}

	// this mapping could be moved to the DB?
 	public function getVenueCategoryIds($category) {

 		$categories = array();

		$categories['shops'][] = '4bf58dd8d48988d103951735' ;

		$categories['sxsw_locations'][] = '4bf58dd8d48988d1ff931735' ;
		$categories['sxsw_locations'][] = '4d954b0ea243a5684a65b473' ;
		$categories['sxsw_locations'][] = '4bf58dd8d48988d1fa931735' ;
		$categories['sxsw_locations'][] = '4d4b7105d754a06376d81259' ;
		$categories['sxsw_locations'][] = '5032792091d4c4b30a586d5c' ;
		$categories['sxsw_locations'][] = '4bf58dd8d48988d1f1931735' ;
		$categories['sxsw_locations'][] = '4bf58dd8d48988d1ed931735' ; 
		$categories['sxsw_locations'][] = '4bf58dd8d48988d171941735' ; 
 

		// Grocery Store
		$categories['healthy_food'][] = '4bf58dd8d48988d118951735' ; 
		// Health Food Store
		$categories['healthy_food'][] = '50aa9e744b90af0d42d5de0e' ; 
		// Farmers Market
		$categories['healthy_food'][] = '4bf58dd8d48988d1fa941735' ; 
 
		// Butcher
		// $categories['healthy_food'][] = '4bf58dd8d48988d11d951735' ; 
 
		// Gourmet Shop
		// $categories['healthy_food'][] = '4bf58dd8d48988d1f5941735' ; 

		$categories['science'][] = '4bf58dd8d48988d191941735' ;
		$categories['science'][] = '4d4b7105d754a06372d81259' ;
		$categories['science'][] = '4bf58dd8d48988d192941735' ;
		$categories['science'][] = '4bf58dd8d48988d181941735' ;
		$categories['science'][] = '4bf58dd8d48988d125941735' ;
		$categories['science'][] = '4bf58dd8d48988d122951735' ;
		
		
  		return implode(',',$categories[$category]);
 	}
 
 
	/*
	clean up db queries

	DELETE FROM venue_meta_common WHERE id IN(SELECT id FROM `venue` WHERE region_id = 5);
	DELETE FROM venue_meta_foursquare WHERE id IN(SELECT id FROM `venue` WHERE region_id = 5);
	DELETE FROM venue_meta_instagram WHERE id IN(SELECT id FROM `venue` WHERE region_id = 5);
	DELETE FROM venue_meta_twitter WHERE id IN(SELECT id FROM `venue` WHERE region_id = 5);
	DELETE FROM venue_record WHERE id IN(SELECT id FROM `venue` WHERE region_id = 5);
	DELETE FROM tracking_log WHERE id IN(SELECT id FROM `venue` WHERE region_id = 5);
	DELETE FROM record WHERE id IN(SELECT id FROM `venue` WHERE region_id = 5);
	DELETE FROM singleton WHERE id IN(SELECT id FROM `venue` WHERE region_id = 5);
	DELETE FROM venue  WHERE region_id = 5;

	*/
}  
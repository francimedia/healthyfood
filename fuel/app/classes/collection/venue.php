<?php

namespace Collection;
 
class Venue
{

	public static function saveVenueJsonToDB($venue, $region_id = NULL) {

		// skip this venue if it already exists
		if(self::venueFoursquareIdExists($venue->id)) {
			return false;
		}

		$response = array();

		// create a new global identifier
		$system_venue_id = \Collection\Singleton::create('venue');
		$response['system_venue_id'] = $system_venue_id;

		// create a base venue entry
		self::createVenue($system_venue_id, $venue, $region_id);
		$response['log'] = 'Added new venue: '.$venue->name . ' / ' . $venue->id;

		// save foursquare relevant data
		self::createVenueMetaFoursquare($system_venue_id, $venue);

		// save general meta data for this venue			
		self::createVenueMetaCommon($system_venue_id, $venue); 

		// write initial stats to DB > $stats array
		$response['stats'] = self::saveVenueStats($system_venue_id, $venue);

		return $response;
	}

    public static function getVenueByFoursquareId($venue_foursquare_id) { 
		$entry = \Model_Venue_Meta_Foursquare::find(array($venue_foursquare_id, 'venue_foursquare_id'));
    }

    public static function venueFoursquareIdExists($venue_foursquare_id) { 
    	return \Model_Venue_Meta_Foursquare::query()->where('venue_foursquare_id', $venue_foursquare_id)->limit(1)->count() > 0 ? true : false;
    }

    public static function getOrCreateVenueRecord($system_venue_id) { 

		$VenueRecord = \Model_Venue_Record::find($system_venue_id);

		if(!$VenueRecord) {
			$VenueRecord = new \Model_Venue_Record();
			$VenueRecord->id = $system_venue_id;  
			$VenueRecord->save();
		}

		return $VenueRecord;

    }

    public static function createVenue($system_venue_id, $venue, $region_id = false) {
		$BaseVenue = new \Model_Venue();
		$BaseVenue->id = $system_venue_id;
		$BaseVenue->name = $venue->name;
		$BaseVenue->lat = $venue->location->lat;
		$BaseVenue->lng = $venue->location->lng;
		$BaseVenue->street = isset($venue->location->address) ? $venue->location->address : '-';
		$BaseVenue->postalCode = isset($venue->location->postalCode) ? $venue->location->postalCode : 0;
		$BaseVenue->city = isset($venue->location->city) ? $venue->location->city : '-';
		$BaseVenue->state = isset($venue->location->state) ? $venue->location->state : '-';
		$BaseVenue->cc = $venue->location->cc;
		$BaseVenue->region_id = $region_id;
		$BaseVenue->save();	    	
    }

    public static function createVenueMetaFoursquare($system_venue_id, $venue) {
		$VenueMetaFoursquare = new \Model_Venue_Meta_Foursquare();
		$VenueMetaFoursquare->id = $system_venue_id;
		$VenueMetaFoursquare->venue_foursquare_id = $venue->id;
		$VenueMetaFoursquare->canonicalUrl = $venue->canonicalUrl; 
		$VenueMetaFoursquare->save();    	
    }

    public static function createVenueMetaCommon($system_venue_id, $venue) {
		$VenueMetaCommon = new \Model_Venue_Meta_Common();
		$VenueMetaCommon->id = $system_venue_id;
		$VenueMetaCommon->save();   	
	}

    public static function saveVenueStats($system_venue_id, $venue, $extended = false) {
 
		// reset latest setting
		$query = \DB::update('tracking_log');
		$query->where('object_id', $system_venue_id);
		$query->where('source', 'foursquare');
		$query->value('is_latest', 0);
		$query->execute(); 

		$stats = array( 
			'checkin' => $venue->stats->checkinsCount, 
			'checkin-unique' => $venue->stats->usersCount, 
			'review' => $venue->stats->tipCount, 
			'like' => $venue->likes->count, 
			'herenow' => isset($venue->herenow) ? $venue->herenow : 0
			// 'instagram_pictures' => 0,
			// 'instagram_likes' => 0,
			// 'instagram_comments' => 0 
		);

		if($extended) {
			$stats['rating'] = isset($venue->rating) ? ($venue->rating * 100) : 0;
			$stats['photos'] = $venue->photos->count;
			$stats['specials'] = $venue->specials->count;
			// $stats['herenow'] = $venue->hereNow->count;
			$stats['mayor'] = $venue->mayor->count;
		}

		$VenueRecord = self::getOrCreateVenueRecord($system_venue_id); 

		foreach($stats as $property => $value) {
			$time_key = date('Y_m_d_H');

			$record_hash = implode('_', array(
				'4q',
				$venue->id,
				$property,
				$time_key
			));

			try {
				$Record = new \Model_Record();
				$Record->record_hash = $record_hash;
				$Record->object_id = $system_venue_id;  
				$Record->property = $property;
				$Record->value = $value; 
				$Record->datatype = 'absolute'; 
				$Record->save(); 

				$VenueRecord->$property = $value;

			}
			catch (\Database_Exception $err) {	
				// catch error. normally there shouldn't be an error, but in the development phase it's possible to have incomplete data, so errors could occur at this place.						
    			return $err;
			}

		}
		
		$VenueRecord->save();   
 
		// keep track of tracking...
		$Log = new \Model_Tracking_Log();
		$Log->object_id = $system_venue_id;  
		$Log->success = 1;
		$Log->is_latest = 1;
		$Log->source = 'foursquare';
		$Log->save();	

		return $stats;

    }	
 
}
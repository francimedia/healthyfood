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
		self::createOrUpdateVenueMetaFoursquare($system_venue_id, $venue);

		// save general meta data for this venue			
		self::createVenueMetaCommon($system_venue_id, $venue); 

		// write initial stats to DB > $stats array
		$response['stats'] = self::saveVenueStats($system_venue_id, $venue);

		return $response;
	}

	public static function saveInstagramVenueJsonToDB($venue, $system_venue_id) {
		// skip this venue if it already exists
		if(self::venueInstagramIdExists($venue->id)) {
			return $venue->id;
		}
		// save instagram relevant data
		self::createVenueMetaInstagram($system_venue_id, $venue);
		return $venue->id;
	}

    public static function getVenueByFoursquareId($venue_foursquare_id) { 
    	return \Model_Venue_Meta_Foursquare::query()->where('venue_foursquare_id', $venue_foursquare_id)->limit(1)->get_one();
    }

    public static function getFoursquareVenueId($venue_id) { 
		if ( $entry = \Model_Venue_Meta_Foursquare::find($venue_id) ) {
			return $entry->venue_foursquare_id;
		}
    }

    public static function getInstagramVenueId($venue_id) { 
		if ( $entry = \Model_Venue_Meta_Instagram::find($venue_id) ) {
			return $entry->venue_instagram_id;
		}
    }

    public static function venueFoursquareIdExists($venue_foursquare_id) { 
    	return \Model_Venue_Meta_Foursquare::query()->where('venue_foursquare_id', $venue_foursquare_id)->limit(1)->count() > 0 ? true : false;
    }

    public static function venueInstagramIdExists($venue_instagram_id) { 
    	return \Model_Venue_Meta_Instagram::query()->where('venue_instagram_id', $venue_instagram_id)->limit(1)->count() > 0 ? true : false;
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

    public static function createOrUpdateVenueMetaFoursquare($system_venue_id, $venue) { 

		$VenueMetaFoursquare = \Model_Venue_Meta_Foursquare::find($system_venue_id);

		if(!$VenueMetaFoursquare) {
			$VenueMetaFoursquare = new \Model_Venue_Meta_Foursquare();
			$VenueMetaFoursquare->id = $system_venue_id;  
			$VenueMetaFoursquare->venue_foursquare_id = $venue->id;
			$VenueMetaFoursquare->canonicalUrl = $venue->canonicalUrl; 
			$VenueMetaFoursquare->save();
		} 
 
		
		if(isset($venue->price)) {
			$VenueMetaFoursquare->price_tier = $venue->price->tier; 
			$VenueMetaFoursquare->price_message = $venue->price->message; 
		}

		if(isset($venue->menu)) {
			$VenueMetaFoursquare->menu_url = $venue->menu->url; 
			$VenueMetaFoursquare->menu_type = $venue->menu->type; 
		} 
		
		$VenueMetaFoursquare->save();    	
    }

    public static function createOrUpdateVenueMetaHealthyfood($system_venue_id, $price_score) { 

		$VenueMetaHealthyFood = \Model_Venue_Meta_Healthyfood::find($system_venue_id);

		if(!$VenueMetaHealthyFood) {
			$VenueMetaHealthyFood = new \Model_Venue_Meta_Healthyfood();
			$VenueMetaHealthyFood->id = $system_venue_id; 
		} 

		$VenueMetaHealthyFood->price_score = $price_score; 
		
		$VenueMetaHealthyFood->save();    	
    }

    public static function calculateVenuePriceScore($system_venue_id) { 

        $query = \DB::select_array();
        $query->from('record');
        $query->select(\DB::expr('AVG(value) as price_score'));

        $query->where('property', 'price');
        $query->where('object_id', $system_venue_id); 
 
        $result = $query->execute()->as_array();

        if(isset($result[0]['price_score'])) {
			self::createOrUpdateVenueMetaHealthyfood($system_venue_id, $result[0]['price_score']);
			return true;
		}

    }

    public static function createVenueMetaInstagram($system_venue_id, $venue) {
		$InstagramVenue = new \Model_Venue_Meta_Instagram();
		$InstagramVenue->id = $system_venue_id;
		$InstagramVenue->venue_instagram_id = $venue->id;
		$InstagramVenue->save();	    	
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
			$stats['price'] = isset($venue->price->tier) ? $venue->price->tier : 0; 
			$stats['photos'] = $venue->photos->count;
			$stats['specials'] = $venue->specials->count;
			// $stats['herenow'] = $venue->hereNow->count;
			$stats['mayor'] = $venue->mayor->count;
		}
		
		$VenueRecord = self::getOrCreateVenueRecord($system_venue_id);  

		foreach($stats as $property => $value) {
			$time_key = date('Y_m_d_H');

			/*
			$record_hash = implode('_', array(
				'4q',
				$venue->id,
				$property,
				$time_key
			));
			*/

			try {
				$Record = new \Model_Record();
				// $Record->record_hash = $record_hash;
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

    public static function countSearchResults($options = array()) {
    	return self::search($options, true);
    }	

    public static function search($options = array(), $count = false) {

    	$columns = $count ? \DB::expr('COUNT(*) as count') : '*';

    	$query = \DB::select($columns)->from('venue_record');	
		$query->join('venue');
		$query->on('venue.id', '=', 'venue_record.id');

		// sorting options
		$options['order_by'] = isset($options['order_by']) ?  $options['order_by'] : 'venue_record.checkin';
		$options['order_dir'] = isset($options['order_dir']) ?  $options['order_dir'] : 'desc';
		$query->order_by($options['order_by'], $options['order_dir']);

		// filters
		$options['filter'] = isset($options['filter']) ?  $options['filter'] : array();

		foreach($options['filter'] as $filter_type => $filter) { 
			switch ($filter_type) {
				case 'regions':
					$query->where('venue.region_id', 'IN', $filter); 
					break;  
				case 'q':
					$query->where('venue.name', 'like', '%'.$filter.'%');
					break;  
			}

		}
 
		if($count) { 
			$result = $query->execute();
			$result_arr = $result->current();
			return $result_arr['count']; 
		}

    	// set paging default options
    	$options['per_page'] = isset($options['per_page']) ?  $options['per_page'] : 10;
    	$options['offset'] = isset($options['offset']) ?  $options['offset'] : 0;
 
		$query->limit($options['per_page']);
		$query->offset($options['offset']);

// print_r($query->execute());
// exit;
		return $query->as_object()->execute();


    }
 
}
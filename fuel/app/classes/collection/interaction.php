<?php

namespace Collection;
 
class Interaction
{
 
	public static function saveInstagramJsonToDB($interaction, $system_venue_id) {

		// skip this interaction if it already exists
		if($Model_Interaction_Meta_Instagram = self::getInteractionInstagramEntity($interaction->id)) {
			$response['stats'] = self::saveInteractionStats($Model_Interaction_Meta_Instagram->id, $interaction, $Model_Interaction_Meta_Instagram);
			return $response;
		}

		$response = array();

		// create a new global identifier
		$system_interaction_id = \Collection\Singleton::create('interaction');
		$response['system_interaction_id'] = $system_interaction_id;

		// track user
		$system_user_id = self::getInternalUserId($interaction);
 
		// create a base interaction entry
		self::createInteraction($system_interaction_id, $system_venue_id, $system_user_id, $interaction, 'picture', 'instagram');
		$response['log'] = 'Added new interaction: ' . $interaction->id;

		// save foursquare relevant data
		self::createInteractionMetaInstagram($system_interaction_id, $interaction);
 
		// save general meta data for this interaction			
		self::createInteractionMetaCommon($system_interaction_id, $interaction); 

		// write initial stats to DB > $stats array
		$response['stats'] = self::saveInteractionStats($system_interaction_id, $interaction);

		return $response;
	} 

    public static function createInteraction($system_interaction_id, $system_venue_id, $system_user_id, $interaction, $interaction_type, $source) {
    	$BaseInteraction = new \Model_Interaction();
		$BaseInteraction->id = $system_interaction_id;
		$BaseInteraction->venue_id = $system_venue_id;
		$BaseInteraction->user_id = $system_user_id;
		// $BaseInteraction->name = $interaction->name; 
		$BaseInteraction->time_created = date('Y-m-d H:i:s', $interaction->created_time);
		$BaseInteraction->interaction_type = $interaction_type;
		$BaseInteraction->source = $source;
		$BaseInteraction->save();	    	
    } 

    public static function createInteractionMetaInstagram($system_interaction_id, $interaction) {
		$BaseInteractionMetaInstagram = new \Model_Interaction_Meta_Instagram();
		$BaseInteractionMetaInstagram->id = $system_interaction_id;
		$BaseInteractionMetaInstagram->interaction_instagram_id = $interaction->id;
		$BaseInteractionMetaInstagram->instagram_user_id = $interaction->user->id;
		$BaseInteractionMetaInstagram->username = $interaction->user->username;
		$BaseInteractionMetaInstagram->image_1 =  $interaction->images->low_resolution->url;
		$BaseInteractionMetaInstagram->image_2 =  $interaction->images->thumbnail->url;
		$BaseInteractionMetaInstagram->image_3 = $interaction->images->standard_resolution->url;
		$BaseInteractionMetaInstagram->caption = isset($interaction->caption->text) ? $interaction->caption->text : '';
		$BaseInteractionMetaInstagram->comments = $interaction->comments->count;
		$BaseInteractionMetaInstagram->link = $interaction->link;
		$BaseInteractionMetaInstagram->likes = $interaction->likes->count;		
 		$BaseInteractionMetaInstagram->save();	    	
    }
 
    public static function createInteractionMetaCommon($system_interaction_id, $interaction) {
		$BaseInteractionMetaCommon = new \Model_Interaction_Meta_Common();
		$BaseInteractionMetaCommon->id = $system_interaction_id;
		$BaseInteractionMetaCommon->lat = $interaction->location->latitude;
		$BaseInteractionMetaCommon->lng = $interaction->location->longitude; 		
 		$BaseInteractionMetaCommon->save();	    	
    }


    public static function getInternalUserId($interaction) {

    	// 2do: prevent duplicate querying
		$query = \DB::select('*')->from('user_meta_instagram');
		
		$query->join('user');
		$query->on('user.id', '=', 'user_meta_instagram.id');

 		$query->where('instagram_user_id', $interaction->user->id);

 		$results = $query->as_object()->execute();
  
 		if($results && isset($results[0])) {
 			return $results[0]->id;
 		}

 		$system_user_id = \Collection\Singleton::create('interaction');
 
		$BaseUser = new \Model_User();
		$BaseUser->id = $system_user_id; 
		$BaseUser->username = $interaction->user->username;
 		$BaseUser->save();	   
 		
		$InstagramUser = new \Model_User_Meta_Instagram();
		$InstagramUser->id = $system_user_id;
		$InstagramUser->username = $interaction->user->username;
		$InstagramUser->instagram_user_id = $interaction->user->id; 
 		$InstagramUser->save();	   
  
 		return $system_user_id;
    }

		 



    public static function saveInteractionStats($system_interaction_id, $interaction, $Model_Interaction_Meta_Instagram = false, $extended = false) {
 
		// reset latest setting
		$query = \DB::update('tracking_log');
		$query->where('object_id', $system_interaction_id);
		$query->where('source', 'instagram');
		$query->value('is_latest', 0);
		$query->execute(); 

		$stats = array( 
			'photos' => 1, // should change all to "s" or "not s"
			'comment' => $interaction->comments->count,
			'like' => $interaction->likes->count 
		);

		if($extended) {
			// $stats['xxx'] = $interaction->xxx;
		}

		// $VenueRecord = self::getOrCreateVenueRecord($system_interaction_id); 

		foreach($stats as $property => $value) {
			$time_key = date('Y_m_d_H');

			/*
			$record_hash = implode('_', array(
				'ig',
				$interaction->id,
				$property,
				$time_key
			));
			*/

			try {
				$Record = new \Model_Record();
				// $Record->record_hash = $record_hash;
				$Record->object_id = $system_interaction_id;  
				$Record->property = $property;
				$Record->value = $value; 
				$Record->datatype = 'absolute'; 
				$Record->save(); 

				// $VenueRecord->$property = $value;

			}
			catch (\Database_Exception $err) {	
				// catch error. normally there shouldn't be an error, but in the development phase it's possible to have incomplete data, so errors could occur at this place.	
				echo $err;
				exit;
    			return $err;
			}

		}
		
		// $VenueRecord->save();   
 
		// keep track of tracking...
		$Log = new \Model_Tracking_Log();
		$Log->object_id = $system_interaction_id;  
		$Log->success = 1;
		$Log->is_latest = 1;
		$Log->source = 'instagram';
		$Log->save();	

		if($Model_Interaction_Meta_Instagram) { 
			$Model_Interaction_Meta_Instagram->comments = $interaction->comments->count;
			$Model_Interaction_Meta_Instagram->link = $interaction->link;
			$Model_Interaction_Meta_Instagram->likes = $interaction->likes->count;		
 			$Model_Interaction_Meta_Instagram->save();	 
 		}


		return $stats;

    }	   

    public static function interactionInstagramIdExists($interaction_instagram_id) { 
    	return \Model_Interaction_Meta_Instagram::query()->where('interaction_instagram_id', $interaction_instagram_id)->limit(1)->count() > 0 ? true : false;
    } 	   

    public static function getInteractionInstagramEntity($interaction_instagram_id) { 
    	return \Model_Interaction_Meta_Instagram::query()->where('interaction_instagram_id', $interaction_instagram_id)->limit(1)->get_one();
    } 

    public static function countSearchResults($options = array()) {
    	return self::search($options, true);
    }	

    public static function search($options = array(), $count = false) {

    	$columns = $count ? \DB::expr('COUNT(*) as count') : '*';

    	$query = \DB::select($columns)->from('interaction');	
		
		$query->join('venue');
		$query->on('venue.id', '=', 'interaction.venue_id');

		$query->join('interaction_meta_instagram');
		$query->on('interaction_meta_instagram.id', '=', 'interaction.id');

		// sorting options
		$options['order_by'] = isset($options['order_by']) ?  $options['order_by'] : 'time_created';
		$options['order_dir'] = isset($options['order_dir']) ?  $options['order_dir'] : 'desc';
		$query->order_by($options['order_by'], $options['order_dir']);

		// filters
		$options['filter'] = isset($options['filter']) ?  $options['filter'] : array();

		foreach($options['filter'] as $filter_type => $filter) { 
			switch ($filter_type) {
				case 'regions':
					$query->where('venue.region_id', 'IN', $filter); 
					break;   
				case 'date_range': 
					switch ($filter) {
						case 'today':				
							$query->where(\DB::expr('DATE(time_created)'), '=', date('Y/m/d')); 				
							break;
						case '24hours':				
							$query->where('time_created', '>=', \DB::expr('DATE_SUB(NOW(),INTERVAL 1 DAY)'));
							break;
						case 'hour':				
							$query->where('time_created', '>=', \DB::expr('DATE_SUB(NOW(),INTERVAL 1 HOUR)')); 
							break;
						case '2hours':				
							$query->where('time_created', '>=', \DB::expr('DATE_SUB(NOW(),INTERVAL 2 HOUR)')); 
							break;
						case 'yesterday':
							$yesterday = mktime(0,0,0,date("m"),date("d")-1,date("Y"));
							$query->where(\DB::expr('DATE(time_created)'), '=', date('Y/m/d', $yesterday)); 				
							break;
						case '2days':  
							$yesterday = mktime(0,0,0,date("m"),date("d")-2,date("Y"));
							$query->where(\DB::expr('DATE(time_created)'), '=', date('Y/m/d', $yesterday)); 				 
							break;
						case 'last2days':  
							$yesterday = mktime(0,0,0,date("m"),date("d")-1,date("Y"));
							$query->where(\DB::expr('DATE(time_created)'), '<=', date('Y/m/d', $yesterday)); 
							$yesterday = mktime(0,0,0,date("m"),date("d")-2,date("Y"));
							$query->where(\DB::expr('DATE(time_created)'), '>=', date('Y/m/d', $yesterday)); 				 
							break;
						case '3days':  
							$yesterday = mktime(0,0,0,date("m"),date("d")-3,date("Y"));
							$query->where(\DB::expr('DATE(time_created)'), '=', date('Y/m/d', $yesterday)); 				 
							break;
						case 'thisweek':				
							$query->where('time_created', '>=', \DB::expr('DATE_SUB(NOW(),INTERVAL 7 DAY)')); 
							break;
						case 'thismonth':				
							$query->where('time_created', '>=', \DB::expr('DATE_SUB(NOW(),INTERVAL 1 MONTH)')); 
							break;
						default: 
							return;
					}				
					break;   
				case 'q':
					if($filter) {
						$query->where('venue.name', 'like', '%'.$filter.'%');
					}
					break;  
				case 'caption':
					$query->where('interaction_meta_instagram.caption', 'like', '%'.$filter.'%');
					break;  
			}
		}
/*
        	'today' => 'Today',
        	'yesterday' => 'Yesterday',
        	'last2days' => 'Last 2 days ago',
        	'2days' => '2 days ago',
        	'3days' => '3 days ago',
        	'thisweek' => 'This week', 
        	'thismonth' => 'This month' 		
 */
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
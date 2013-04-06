<?php

namespace Collection;
 
class Keyword
{

	public static function addKeywordToDB($value) {
		if(self::keywordExists($value)) {
			return false;
		}

		$system_id = \Collection\Singleton::create('keyword');

		$Keyword = new \Model_Keyword();
		$Keyword->id = $system_id;
		$Keyword->value = $value;
		$Keyword->count = 0;
		$Keyword->save();

		// Tracking_Cylce::create($system_id, 'hourly');

		$Cycle = new \Model_Tracking_Cycle();
		$Cycle->object_id = $system_id;
		$Cycle->frequency = 'hourly';
		$Cycle->save();		

		return true;		

	} 

    public static function createStats($keyword) { 

    	if(!$keyword) {
    		return false;
    	}

    	$Keyword = self::getByValue($keyword);

    	if(!$Keyword) {
    		return false;
    	}
  
    	// look for keyword in instagram pictures
    	\Model_Keyword::query()->where('value', $keyword)->limit(1)->count() > 0 ? true : false;


    	// 2do: prevent duplicate querying
		$query = \DB::select('*')->from('interaction_meta_instagram');
		
		$query->join('interaction');
		$query->on('interaction.id', '=', 'interaction_meta_instagram.id');

 		$query->where('caption', 'like', '%'.$keyword.'%');

 		$results = $query->as_object()->execute();
 
		\Cli::write('Results: ' . $results->count()); 

		$keyword_venues = array();
		$keyword_venue_ids = array();

		foreach($results as $result) {
			// get or create keyword <> venue relation 
			$Keyword_Venue = self::getOrCreateVenueRelation($Keyword->id, $result->venue_id);
			
			$keyword_venue_ids[$Keyword_Venue->id] = $Keyword_Venue->id;

			// create a singleton record for each appearance, prevent duplicates
			if ( \Collection\Record::recordExists($Keyword_Venue->id, 'keyword', 'absolute', $result->time_created) ) {
				continue;
			}

			$Record = \Collection\Record::create($Keyword_Venue->id, 'keyword', 'absolute', $result->time_created, 1);

			$keyword_venues[$Keyword_Venue->id] = $Keyword_Venue;
			 
		}

		// update totals
		foreach($keyword_venues as $id => $Keyword_Venue) {
			$Keyword_Venue->count = \Collection\Record::countTotal($Keyword_Venue->id, 'keyword');
			$Keyword_Venue->save();
		}

		$Keyword->count = \Collection\Record::countTotal($keyword_venue_ids, 'keyword');
		$Keyword->save();

 		return true;

    }

    public static function getOrCreateVenueRelation($keyword_id, $venue_id) {
		if ( $entry = \Model_Keyword_Venue::query()->where('keyword_id', $keyword_id)->where('venue_id', $venue_id)->get_one() ) {
			return $entry;
		}    	

		$system_id = \Collection\Singleton::create('keyword'); 

		$entry = new \Model_Keyword_Venue();
		$entry->id = $system_id;
		$entry->keyword_id = $keyword_id;
		$entry->venue_id = $venue_id;
		$entry->count = 0;
		$entry->save();

		return $entry;
    }

    public static function keywordExists($value) { 
    	return \Model_Keyword::query()->where('value', $value)->limit(1)->count() > 0 ? true : false;
    }

    public static function getByValue($value) { 
		if ( $entry = \Model_Keyword::query()->where('value', $value)->get_one() ) {
			return $entry;
		}
    }
  
    public static function getById($id) { 
		if ( $entry = \Model_Keyword::find($id) ) {
			return $entry;
		}
    }

}
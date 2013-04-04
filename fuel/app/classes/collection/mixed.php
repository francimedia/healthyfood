<?php

namespace Collection;

class Mixed 
{

	public static function convertObjectIds($results) {
		$ids = array();
		foreach($results as $row) {
			$ids[$row['object_type']][] = $row['object_id'];
		}

		$object_types = array();
		$object_types['venue']['identifier'] = 'venue_foursquare_id';
		$object_types['venue']['meta_table'] = 'venue_meta_foursquare';
		$object_types['interaction']['identifier'] = 'instagram_picture_id';
		$object_types['interaction']['meta_table'] = 'interaction_meta_instagram';

		$translated_ids = array();
		foreach($object_types as $table => $object_type) {

			$translated_ids[$table] = array();

			if(!isset($ids[$table])) {
				continue;
			}
 
			$query = \DB::select('id', $object_type['identifier'])->from($object_type['meta_table']);
			$query->where('id', 'IN', $ids[$table]);
			$results = $query->execute();


			foreach($results  as $row) {
				$translated_ids[$table][$row['id']] = $row[$object_type['identifier']];
			}

		}

		return $translated_ids;		

	}

}

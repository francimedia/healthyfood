<?php

namespace Spider\Foursquare;


use Spider\BaseSpider as BaseSpider;

use Fuel\Core\Package as Package;
use Fuel\Core\Config as Config;
use Fuel\Core\Cache as Cache;
use Fuel\Core\File as File;
use Fuel\Core\Format as Format;

class SnapSpider extends Spider
{ 

 
	public function __construct() {
		parent::__construct();
	}


    public function run()
    { 
    	$FoursquareClient = new \Foursquare\Client;

    	$file_content = File::read(DOCROOT.'data/NY.csv', true);
	 	$data = Format::forge($file_content, 'csv')->to_array();

	 	$valid_counties = array();
	 	$valid_counties[] = 'BRONX';
	 	$valid_counties[] = 'QUEENS';
	 	$valid_counties[] = 'KINGS';
	 	$valid_counties[] = 'NEW YORK';
	 	$valid_counties[] = 'RICHMOND';


	 	$i=0;
	 	$j=0;
	 	foreach ($data as $key => $value) {
	 		if(!in_array($value['County'], $valid_counties)) {
	 			continue;
	 		}

	 		$i++;

			if($i>300) {
	 			break;
	 		}

	 		$match = $FoursquareClient->getVenueMapping($value['Latitude'], $value['Longitude'], $value['Store_Name']);

	 		if($this->hasMatches($match, &$j)) {
	 			$this->logMatches($value, $match, $matches, $j, $i);
	 			continue;
	 		}

 			$this->CliOutput('write', 'Stripping chars from Store_Name: '.$value['Store_Name']);

 			// remove numbers from store name
 			$value['Store_Name'] = trim(preg_replace("/[0-9]/", "", $value['Store_Name']));
 			$match = $FoursquareClient->getVenueMapping($value['Latitude'], $value['Longitude'], $value['Store_Name']);

	 		if($this->hasMatches($match, &$j)) {
	 			$this->logMatches($value, $match, $matches, $j, $i);
	 			continue;
	 		}

 			$this->CliOutput('error', 'No match for ' . $value['Store_Name']. ' ' . $value['Address']. ' ' . $value['City']. ' ' . $value['Zip5']);
	 
	 	}

		$this->CliOutput('write', 'Matches: '.$j.'/'.$i);
 

	}


	private function printStats($j, $i) {
		$this->CliOutput('write', 'Matches: '.$j.'/'.$i.' ('.round(100*$j/$i).')');
	}	

	private function hasMatches($match, $j) {
	 	if ( $hasMatches = (isset($match->response->venues) && count($match->response->venues) > 0 ) ) {
	 		$j++;
	 	}	 			
		return $hasMatches;
	}

	private function logMatches($value, $match, &$matches, $j, $i) {
		$matches[] = count($match->response->venues);
		$this->CliOutput('write', 'Matches for ' . $value['Store_Name']);
		foreach ($match->response->venues as $venue) {
			$this->CliOutput('write', '- ' . $venue->name);
		}		
	 	$this->printStats($j, $i);
	}
 
 }  
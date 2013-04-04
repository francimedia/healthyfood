<?php
namespace Fuel\Tasks;

use Fuel\Core\Package as Package;  
 		
// php oil refine spider::foursquare_venues  

class Foursquare_venues
{
 

	public function __construct() {
		Package::load('Spider'); 
		$this->spider = new \Spider\Foursquare\Spider;
		$this->spider->setCli(true);
	}

    public function run($region_id)
    { 
	   	if ($this->spider->scanRegion($region_id)) {
	   		\Cli::write('Scan finished.');	
	   	} else {
	   		\Cli::error('No region found / Invalid region id');	
	   	}
    }

    public function searchForLocations($lat = 40.761821, $lng = -73.974895, $radius = 1000, $query = '', $region_id = false)
    { 
	   	$this->spider->searchForLocations($lat, $lng, $radius, $query, $region_id);
    }

    public function updateAllLocations() {
	   	$this->spider->updateAllLocations();
    }

    // run updateLocations as infinite service
    public function updateLocationsService() {
    	while(1) {
    		if($this->spider->getCurrentRateLimit() == 0) {
    			// wait for 15min if we hit the rate limit
    			\Cli::error('Warning: Rate limit hit! Script will pause for 15mins.');	
    			\Cli::wait(1800);
    		}
    		$this->spider->updateLocations();
    		\Cli::wait(1, true);
    	}
    }

    public function updateLocations() {    	
    	$this->spider->updateLocations();
	}
 
	public function calculateDeltas() {	
    	$this->spider->calculateDeltas();
	} 

    public function addTrackingPoints($region_id) {
    	$this->spider->addTrackingPoints($region_id);
    } 
 
}
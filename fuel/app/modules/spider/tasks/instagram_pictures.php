<?php
namespace Fuel\Tasks;

use Fuel\Core\Package as Package;  
 
 		
// php oil refine spider::instagram_pictures  

 

class Instagram_pictures
{
 

	public function __construct() {
		Package::load('Spider'); 
		$this->spider = new \Spider\Instagram\Spider;
		$this->spider->setCli(true);
	}	


    public function run($region_id, $cycles = 5)
    { 
	   	if ($this->spider->updateVenues($region_id, $cycles)) {
	   		\Cli::write('Scan finished.');	
	   	} else {
	   		\Cli::error('No region found / Invalid region id');	
	   	}
    } 


    public function venue($venue_id, $cycles = 25)
    { 
	   	if ($this->spider->updateVenue($venue_id, $cycles)) {
	   		\Cli::write('Scan finished.');	
	   	} else {
	   		\Cli::error('No region found / Invalid region id');	
	   	}
    } 


    public function foursquare_venue($venue_id, $cycles = 25)
    { 
	   	if ($this->spider->updateFoursquareVenue($venue_id, $cycles)) {
	   		\Cli::write('Scan finished.');	
	   	} else {
	   		\Cli::error('No region found / Invalid region id');	
	   	}
    } 
 
}
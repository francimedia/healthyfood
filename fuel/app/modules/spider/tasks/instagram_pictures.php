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

    public function run()
    { 
    	$this->spider->updateLocations();
	}

}
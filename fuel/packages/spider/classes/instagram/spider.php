<?php

namespace Spider\Instagram;

use Fuel\Core\Package as Package;
use Fuel\Core\Config as Config;
use Fuel\Core\Cache as Cache;

class Spider extends BaseSpider
{ 
 
	public function __construct() {
		parent::__construct();
	}
 
 
    // try to update locations smart
    // at the moment just scan at random times, goal: track one location at a similar time. example: 01:00am-02:00am
    public function updateLocations() {    	

    	$empty_responses = 0;

		$InstagramClient = new \Instagram\Client;
		$tracking_logs = \Collection\Tracking_Log::getQueuedItems();
 		
		if($tracking_logs->count() == 0) {
			$this->cliOutput('error', 'Nothing to scan!??? Waiting for 5 minutes.');
			$this->cliOutput('wait', 300);
		}		

	}

}
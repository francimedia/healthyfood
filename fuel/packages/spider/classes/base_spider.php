<?php

namespace Spider;

use Fuel\Core\Package as Package;
use Fuel\Core\Config as Config;
use Fuel\Core\Cache as Cache;

class BaseSpider
{ 

	// this is a command line request?
	public $cli = false;
 	public $debug_messages = true;
 		
	// php oil refine spider::foursquare_venues  


	public function __construct() {
		Package::load('Log'); 
	}

	public function debugMessage($message) {

		if(!$this->debug_messages) {
			return;
		}

		\Log::debug('Foursquare_venues task: ' . $message);
		$this->cliOutput('write', $message);

	}

	public function setDebugMessages($val) {
		$this->debug_messages = $val;
	}

	public function setCli($val) {
		$this->cli = $val;
	}
  
  	public function cliOutput($type, $message) {
  		if(!$this->cli) {
  			return;
  		}
  		\Cli::$type($message);
	}
 
}  
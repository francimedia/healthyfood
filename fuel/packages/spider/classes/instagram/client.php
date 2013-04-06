<?php

namespace Spider\Instagram;

use Fuel\Core\Package as Package;
use Fuel\Core\Config as Config;
use Fuel\Core\Cache as Cache;

class Client
{
  
	protected $baseUrl; 
	protected $curl_http_client; 
	protected $enable_cache = true;
	protected $expire = 60; 

	/**
	 * Magic functions config
	 *
	 * Example API call:
	 * $this->instagram_api->get('searchEvents', 324);
	 * 
	 * 	
	 * example config item:
	 * 		
	 * 		'apiCallName' => array(
	 * 			'path' => 'api-path',
	 * 			'args' => array(				
	 * 				'key1', 		// Description 1
	 * 				'key2', 		// Description 2
	 * 			)
	 * 		)
	 * 
	 */

	protected $apiCalls = array(
		'searchLocation' => array(
			'path' => 'locations/search',
			'args' => array(				
				'lat', 			// Latitude of the center search coordinate. If used, lng is required.		
				'lng', 			// Longitude of the center search coordinate. If used, lat is required	
				'distance', 	// Default is 1000m (distance=1000), max distance is 5000.
			)
		),
		'getLocation' => array(
			'path' => 'locations/search',
			'args' => array(				
				'foursquare_v2_id' 			// Returns a location mapped off of a foursquare v2 api location id. If used, you are not required to use lat and lng.
			)
		),
		/*
		'getLocation' => array(
			'path' => '/media/recent',
			'args' => array(				
				'foursquare_v2_id' 			// Returns a location mapped off of a foursquare v2 api location id. If used, you are not required to use lat and lng.
			)
		),
		*/ 

	); 

	public function __construct()
	{		 
		Package::load('curl_http_client'); 		
		Config::load('instagram');
		$this->curl_http_client = new \curl_http_client(array('debug' => true)); 
		$this->baseUrl = Config::get('instagram_base_url'); 	
	} 

	public function getMedia($type, $id, $max_id = 0, $min_id = 0) { 
		$query = array();
		$query['access_token'] = Config::get('instagram_access_token');

		switch ($type) {
			case 'tags':
				if($max_id) {
					$query['max_tag_id'] = $max_id;
				}
				if($min_id) {
					$query['min_tag_id'] = $min_id;
				}
				break;
			default:
				if($max_id) {
					$query['max_id'] = $max_id;
				}
				if($min_id) {
					$query['min_id'] = $min_id;
				}
				break;
		}

		return $this->runRequest($type.'/'.$id.'/media/recent', $query);		
	}

	public function getMediaByTags($tag, $max_id = 0, $min_id = 0) { 
		return $this->getMedia('tags', $tag, $max_id, $min_id);
	}

	public function getMediaByLocation($id, $max_id = 0, $min_id = 0) { 
		return $this->getMedia('locations', $id, $max_id, $min_id);
	}

	public function clearCache() {
		$this->CI->cache->clean();
	}

	public function setExpire($expire)
	{
		$this->expire = $expire;
	} 


	public function get() 
	{
		$arguments = func_get_args();
		$requestName = array_shift($arguments);
		if(array_key_exists($requestName, $this->apiCalls)) 
		{
			$config = $this->apiCalls[$requestName];
			$query = array();
			$query['access_token'] = Config::get('instagram_access_token');
			
			foreach($arguments as $key => $argument) {
				$query[$config['args'][$key]] = $argument;
			}
			return $this->runRequest($config['path'], $query);
		}
		else 
		{
			die('Invalid API request: ' . $requestName);	
		}
	} 

 	private function runRequest($path, $args = array()) {

		// print_r($args);
		// exit;
		
        $url = $this->baseUrl . $path . '?' . http_build_query($args);

        // echo $url ;
        // exit;

		$key = sha1('Instagram_API'.$url);

		if(!$this->enable_cache) {
			return json_decode($this->_runRequest($url));
		}

		try
		{
		    $data = Cache::get($key);
		    return json_decode($data);

		}
		catch (\CacheNotFoundException $e)
		{
		    /*
		        Catching the CacheNotFoundException exception will catch
		        both CacheNotFoundException and CacheExpiredException.
		        Use this when catching the exception.
		    */
			$rawData = $this->_runRequest($url);
			Cache::set($key, $rawData, ($this->expire * 60));
			return json_decode($rawData);		        
		}

	} 


	private function _runRequest($url) {

 		$this->curl_http_client->include_response_headers(1);	
		$response = $this->curl_http_client->fetch_url($url);	

		$data = $this->curl_http_client->get_header_and_body($response);
		
		// $Log = new \Model_Tracking_Log_Ratelimit();
		// $Log->parseAndSetResponseValues($data, 'foursquare');

		return $data['body'];
	}

}
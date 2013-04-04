<?php

namespace Spider\Eventbrite;

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
	 * $this->Eventbrite_api->get('searchEvents', 324);
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
			'path' => 'venues/search',
			'args' => array(				
				'll', 			// required unless near is provided. Latitude and longitude of the user's location. (Required for query searches). Optional if using intent=global
				'radius', 		// Number of results to return, up to 50.
				'limit', 		// Longitude of the center search coordinate. If used, lat is required
				'categoryId', // categoryId	
				'query' // A search term to be applied against venue names.	
			)
		),
		'getVenueInfo' => array(
			'path' => 'venues',
			'args' => array(				
				'id', 			// 
			)
		),


	); 

	public function __construct()
	{		 
		Package::load('curl_http_client'); 		
		Config::load('eventbrite');
		$this->curl_http_client = new \curl_http_client(array('debug' => true));
		$this->baseUrl = Config::get('eventbrite_base_url');	
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
			$query['oauth_token'] = Config::get('eventbrite_access_token');
			$query['v'] = Config::get('eventbrite_v');
			
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

		$key = sha1('Eventbrite_API'.$url);

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
		
		$Log = new \Model_Tracking_Log_Ratelimit();
		$Log->parseAndSetResponseValues($data, 'eventbrite');

		return $data['body'];
	}



}


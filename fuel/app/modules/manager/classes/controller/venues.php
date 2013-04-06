<?php


namespace Manager;

use Fuel\Core\Package as Package;

class Controller_Venues extends \Controller
{

	/** 
	 * @access  public
	 * @return  Response
	 */
	public function get_add()
	{
		$data = array();
 		$data['Form']  = new \Form();
 		$data['Image']  = new \Image();

		$view = \View::forge('venues/add.twig', $data);  
		
		return \Response::forge($view);
	} 

	/** 
	 * @access  public
	 * @return  Response
	 */
	public function post_add()
	{
		if ( $venue_id = \Input::post('venue_id') ) {

			Package::load('Spider'); 	
			$FoursquareClient = new \Spider\Foursquare\Client;
			$venues = $FoursquareClient->getVenueInfoBatch(array($venue_id), \Input::post('region_id'));
  
 			$venue = isset($venues->response->responses[0]->response->venue) ?
 				$venues->response->responses[0]->response->venue : false;

			if(isset($venue)) {
				$messages = \Collection\Venue::saveVenueJsonToDB($venue);
				$view = \View::forge('venues/add_success.twig', $messages);  
				return \Response::forge($view);
			} else {
				return $this->get_add();
			}
		}
	} 

	// get a twig instance
	// $twig = $view->parser();
 
}

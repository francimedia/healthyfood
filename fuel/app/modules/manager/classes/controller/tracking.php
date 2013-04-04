<?php


namespace Manager;

class Controller_Tracking extends \Controller
{

	/** 
	 * @access  public
	 * @return  Response
	 */
	public function action_index()
	{
		$view = \View::forge('tracking/index.twig'); 
		return \Response::forge($view);
	} 
 
}

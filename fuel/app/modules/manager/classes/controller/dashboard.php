<?php


namespace Manager;

class Controller_Dashboard extends \Controller
{

	/** 
	 * @access  public
	 * @return  Response
	 */
	public function action_index()
	{
		$view = \View::forge('dashboard/index.twig'); 
		return \Response::forge($view);
	} 
 
}

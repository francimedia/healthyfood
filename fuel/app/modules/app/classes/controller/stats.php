<?php


namespace App;

class Controller_Stats extends \Controller
{

	/** 
	 * @access  public
	 * @return  Response
	 */
	public function action_index()
	{
		$view = \View::forge('stats/index.twig'); 
		return \Response::forge($view);
	} 

 
}

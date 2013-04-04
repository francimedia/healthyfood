<?php


namespace App;

class Controller_Welcome extends \Controller
{

	/** 
	 * @access  public
	 * @return  Response
	 */
	public function action_index()
	{
		$view = \View::forge('welcome/index.twig'); 
		return \Response::forge($view);
	} 

 
}

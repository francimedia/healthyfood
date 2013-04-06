<?php


namespace Manager;

use Fuel\Core\Package as Package;

class Controller_Keywords extends \Controller
{

	/** 
	 * @access  public
	 * @return  Response
	 */
	public function get_add()
	{
		$data = array();
 		$data['Form']  = new \Form(); 

		$view = \View::forge('keywords/add.twig', $data);  
		
		return \Response::forge($view);
	} 

	/** 
	 * @access  public
	 * @return  Response
	 */
	public function post_add()
	{
		if ( $keyword = \Input::post('keyword') ) {
			if(\Collection\Keyword::addKeywordToDB($keyword)) {
				$view = \View::forge('keywords/add_success.twig');  
				return \Response::forge($view);
			} 
		}
		return $this->get_add();
	} 

	// get a twig instance
	// $twig = $view->parser();
 
}

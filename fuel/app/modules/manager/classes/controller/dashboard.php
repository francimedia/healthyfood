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
 
		$data = array();
 		$data['Form']  = new \Form(); 
 		$data['Input']  = new \Input(); 

		// $options = array();
		$options = \Input::get();

		$config = array(
			'pagination_url' => \Input::uri().'?'.http_build_query($options),
			'total_items'    => \Collection\Venue::countSearchResults($options), 
			'uri_segment'    => 'page',
			'per_page'    => 100,
			'name' => 'bootstrap'
		);

		$pagination = \Pagination::forge('venues', $config);
 		$data['pagination'] = $pagination->render();

 		// get venues
 		$options['per_page'] = $pagination->per_page;
 		$options['offset'] = $pagination->offset; 
 		$data['venues'] = \Collection\Venue::search($options);

 		// get all regions
		$options = array();
		$data['regions'] = \Collection\Region::search($options);

		$data['datatypes'] = array();
		$data['datatypes'][] = array(
			'id' => "checkins",
			'name' => "Checkins"
		);
		$data['datatypes'][] = array(
			'id' => "Pictures",
			'name' => "Pictures"
		);
		$data['datatypes'][] = array(
			'id' => "Likes",
			'name' => "Likes"
		);
		$data['datatypes'][] = array(
			'id' => "Comments",
			'name' => "Comments"
		); 

		$view = \View::forge('dashboard/index.twig', $data); 
		return \Response::forge($view);
	} 


	/** 
	 * @access  public
	 * @return  Response
	 */
	public function action_chart()
	{
		$ids = \Input::post('ids');
		$_ids = \Input::post('_ids');
		$labels = \Input::post('labels'); 

		$items = array();

		foreach ($ids as $key => $id) {
			$_key = array_search($id,$_ids);
			$items[] = array(
				'id' => $id,
				'name' => $labels[$_key]
			);
		}

		$data = array();
		$data['items'] = json_encode($items);
		$data['ids'] = $ids;
		$data['labels'] = $labels;


		$data['charts'] = array();
		$data['charts'][] = array(
			'id' => "pie",
			'name' => "Pie"
		);
		$data['charts'][] = array(
			'id' => "timeline",
			'name' => "Timeline"
		);  
		
		$view = \View::forge('dashboard/chart.twig', $data); 
		return \Response::forge($view);
	} 

	/** 
	 * @access  public
	 * @return  Response
	 */
	public function action_pictures()
	{ 
 
		$data = array();
 		$data['Form']  = new \Form(); 
 		$data['Input']  = new \Input(); 

		// $options = array();
		$options = \Input::get();

		$config = array(
			'pagination_url' => \Input::uri().'?'.http_build_query($options),
			'total_items'    => \Collection\Interaction::countSearchResults($options), 
			'uri_segment'    => 'page',
			'name' => 'bootstrap',
			'per_page' => 50
		);

		$pagination = \Pagination::forge('venues', $config);
 		$data['pagination'] = $pagination->render();

 		// get venues
 		$options['per_page'] = $pagination->per_page;
 		$options['offset'] = $pagination->offset; 
 		$data['pictures'] = \Collection\Interaction::search($options);
 
 		// get all regions
		$options = array();
		$data['regions'] = \Collection\Region::search($options);
 
		$data['order_by'] = \Form::select('order_by', \Input::get('order_by'), array(
        	'time_created' => 'Date',
        	'likes' => 'Likes',
        	'comments' => 'Comments'
		), array(
			'style' => 'width: 80px'
		));

		$data['order_dir'] = \Form::select('order_dir', \Input::get('order_dir'), array(
        	'desc' => 'Desc',
        	'asc' => 'Asc'
		), array(
			'style' => 'width: 80px'
		));
 
		$data['date_range'] = \Form::select('filter[date_range]', \Input::get('filter.date_range'), array(
        	'today' => 'Today',
        	'yesterday' => 'Yesterday',
        	'last2days' => 'Last 2 days ago',
        	'2days' => '2 days ago',
        	'3days' => '3 days ago',
        	'thisweek' => 'This week', 
        	'thismonth' => 'This month' 
		), array(
			'style' => 'width: 80px'
		));


		$view = \View::forge('dashboard/pictures.twig', $data); 
		return \Response::forge($view);
	} 
 
}

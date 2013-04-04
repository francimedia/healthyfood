<?php


namespace App;

class Controller_Api extends \Controller_Rest
{

    public function get_events()
    {
    	$text = \Input::get('query');
    	$starttime = \Input::get('starttime');
    	$endtime = \Input::get('endtime'); 

    	// $colums

        $query = \DB::select_array();
        $query->from('event');
        $query->join('venue');
		$query->on('event.venue_id', '=', 'venue.id');

        if($starttime) {
            $query->where('starttime', '>=', date('Y-m-d H:i:s', $startdate));
        }

        if($endtime) {
            $query->where('endtime', '<=', date('Y-m-d H:i:s', $startdate));
        }

        $Results = $query->execute();             

		// $this->response->set_header('Content-Type', 'text/json; charset=utf-8');

        return $this->response($Results->as_array());

    }    


}
<?php


namespace App;

class Controller_Api extends \Controller_Rest
{

    public function get_venues()
    {
        $lat = \Input::get('lat');
        $lng = \Input::get('lng') ? \Input::get('lng') : \Input::get('lon'); // different names for JS and PHP: lon >> lng !!! 

        $radius = 0.1;
        $distance = 2;
    	 
    	// $colums

        $query = \DB::select_array();
        $query->from('venue');
        $query->select(
            'name', 
            'street',
            'lat', 
            'lng', 
            \DB::expr('IFNULL( ((ACOS(SIN('.$lat.' * PI() / 180) * '
                .'SIN(lat * PI() / 180) + COS('.$lat.' * PI() / 180) '
                .'* COS(lat * PI() / 180) * COS(('.$lng.' - lng)'
                . ' * PI() / 180)) * 180 / PI()) * 60 * 1.1515) , 999999) as distance'), 
            'rating'); 
        

        // load foursquare info
        $query->join('venue_meta_foursquare','LEFT');
        $query->on('venue.id', '=', 'venue_meta_foursquare.id');

        // load foursquare info
        $query->join('venue_record','LEFT');
        $query->on('venue.id', '=', 'venue_record.id'); 

        $query->where('lat', 'between', array(($lat - $radius), ($lat + $radius)));
        $query->where('lng', 'between', array(($lng - $radius), ($lng + $radius))); 
        $query->having('distance', '<=', $distance);

        $query->order_by('distance', 'asc'); 
        $query->group_by('venue.id'); 
         

        if($lat) {
            //  $query->where('starttime', '>=', date('Y-m-d H:i:s', $startdate));
        }

        if($lng) {
            // $query->where('endtime', '<=', date('Y-m-d H:i:s', $startdate));
        }

        $query->limit(20);

        $Results = $query->execute(); 
        // echo \DB::last_query(); 

        $results = $Results->as_array();

        foreach ($results as $key => $row) {
            $results[$key]['lat'] = (float)($row['lat']); 
            $results[$key]['lon'] = (float)($row['lng']); 
            $results[$key]['rating'] = (float)($row['rating']/100); 
            $results[$key]['distance'] = intval(50*round($row['distance']*1000*3.2808399/50)); 
        } 

		// $this->response->set_header('Content-Type', 'text/json; charset=utf-8');

        return $this->response($results);

    }    


}
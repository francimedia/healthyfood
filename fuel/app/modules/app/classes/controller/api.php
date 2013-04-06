<?php


namespace App;

class Controller_Api extends \Controller_Rest
{

    public function get_venues()
    {
        $lat = \Input::get('lat');
        $lng = \Input::get('lng') ? \Input::get('lng') : \Input::get('lon'); // different names for JS and PHP: lon >> lng !!! 

        $radius = 0.3;
        $distance = 5;
    	 
    	// $colums

        $query = \DB::select_array();
        $query->from('venue');
        $query->select(  
            'venue.id',  
            'venue_foursquare_id', 
            'name', 
            'street',
            'lat', 
            'lng', 
            \DB::expr('IFNULL( price_score , 999) as price_score'), 
            \DB::expr('IFNULL( ((ACOS(SIN('.$lat.' * PI() / 180) * '
                .'SIN(lat * PI() / 180) + COS('.$lat.' * PI() / 180) '
                .'* COS(lat * PI() / 180) * COS(('.$lng.' - lng)'
                . ' * PI() / 180)) * 180 / PI()) * 60 * 1.1515) , 999999) as distance'), 
            'rating'
            ); 
        

        // load foursquare info
        $query->join('venue_meta_foursquare');
        $query->on('venue.id', '=', 'venue_meta_foursquare.id');

        // load foursquare info
        $query->join('venue_record');
        $query->on('venue.id', '=', 'venue_record.id'); 

        // load foursquare info
        $query->join('venue_meta_healthyfood', 'LEFT');
        $query->on('venue.id', '=', 'venue_meta_healthyfood.id'); 

        $query->where('lat', 'between', array(($lat - $radius), ($lat + $radius)));
        $query->where('lng', 'between', array(($lng - $radius), ($lng + $radius))); 
        $query->having('distance', '<=', $distance);

        $query->order_by('price_score', 'asc'); 
        $query->order_by('distance', 'asc'); 

        $query->limit(20);

        $Results = $query->execute(); 
        // echo \DB::last_query(); 

        $results = $Results->as_array();

        $price_scores = array();

        foreach ($results as $key => $row) {
            $results[$key]['lat'] = (float)($row['lat']); 
            $results[$key]['lon'] = (float)($row['lng']); 
            unset($results[$key]['lng']);
            $results[$key]['rating'] = (float)($row['rating']/100); 
            $results[$key]['distance'] = intval(50*round($row['distance']*1000*3.2808399/50)); 


            $results[$key]['distance'] = $row['distance'] * 0.621371 < 0.1 ?
                intval(50*round($row['distance']*1000*3.2808399/50)) . ' ft' :
                (float) number_format($row['distance']* 0.621371,1) . ' mi'; 


            if( $row['price_score'] != 999) {
                $price_scores[] = $row['price_score'];
            }

        } 

        $max = max($price_scores);
        $min = min($price_scores); 

        foreach ($results as $key => $row) {
            $results[$key]['save'] = $row['price_score'] != 999 ? round( 100 - ( 100 * $row['price_score'] / $max)) : 0;
            unset($results[$key]['price_score']);
        }         

		// $this->response->set_header('Content-Type', 'text/json; charset=utf-8');

        return $this->response($results);

    }    


}
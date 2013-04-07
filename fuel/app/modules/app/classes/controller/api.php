<?php


namespace App;

class Controller_Api extends \Controller_Rest
{

    public function get_venues()
    {
        $lat = \Input::get('lat');
        $lng = \Input::get('lng') ? \Input::get('lng') : \Input::get('lon'); // different names for JS and PHP: lon >> lng !!! 

        if(!$lat || !$lng) {
            return $this->sendAPIResponse(null, 400, 'Please provide a value for lat and lon.');
        }

        $radius = 0.3;
        $distance = 5;

        $query = $this->baseVenueQuery();
        $this->addVenueSelectFields($query, $lat, $lng);  

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
            $this->formatVenueForOutput($results[$key]);
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

        return $this->sendAPIResponse(array('venues' => $results));

    }    


    public function get_venue()
    {

        $id = \Input::get('id');
        $venue_foursquare_id = \Input::get('venue_foursquare_id');

        $lat = \Input::get('lat');
        $lng = \Input::get('lng') ? \Input::get('lng') : \Input::get('lon'); // different names for JS and PHP: lon >> lng !!! 

        $query = $this->baseVenueQuery();
        $this->addVenueSelectFields($query, $lat, $lng);  

        if($venue_foursquare_id) {
            $query->where('venue_foursquare_id', $venue_foursquare_id);    
        } else {
            $query->where('venue.id', $id);    
        }

        $query->limit(1);

        $Results = $query->execute(); 
        // echo \DB::last_query(); 

        $result = current($Results->as_array());

        if($result) {
            unset($result['price_score']);
            $this->formatVenueForOutput($result);
            return $this->sendAPIResponse(array('venue' => $result));
        } 

        // $this->response->set_header('Content-Type', 'text/json; charset=utf-8');

        $error = $venue_foursquare_id ? 
            'Value ' . $venue_foursquare_id . ' is invalid for venue_foursquare_id' :
            'Value ' . $id . ' is invalid for venue id';

        return $this->sendAPIResponse(null, 400, $error);

    }   

    private function sendAPIResponse($response, $code = 200, $error_message = false) {
        $data = array();
        $data['response'] = $response;
        $data['meta']['code'] = $code;
        if($error_message) {
            $data['meta']['error_message'] = $error_message;
        }
        return $this->response($data);
    } 

    private function formatVenueForOutput(&$result) {
        $result['lat'] = (float)($result['lat']); 
        $result['lon'] = (float)($result['lng']); 
        unset($result['lng']);
        $result['rating'] = (float)($result['rating']/100); 

        if(isset($result['distance'])) {
            $result['distance'] = $result['distance'] * 0.621371 < 0.1 ?
                intval(50*round($result['distance']*1000*3.2808399/50)) . ' ft' :
                (float) number_format($result['distance']* 0.621371,1) . ' mi';  
        }
    }

    private function addVenueSelectFields(&$query, $lat = null, $lng = null) {
        if($lat && $lng) {
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
        }     
        else { 
            $query->select(  
                'venue.id',  
                'venue_foursquare_id', 
                'name', 
                'street',
                'lat', 
                'lng', 
                \DB::expr('IFNULL( price_score , 999) as price_score'), 
                'rating'
            ); 
        }
    }

    private function baseVenueQuery() {
        
        $query = \DB::select_array();
        $query->from('venue');

        // load foursquare info
        $query->join('venue_meta_foursquare');
        $query->on('venue.id', '=', 'venue_meta_foursquare.id');

        // load foursquare info
        $query->join('venue_record');
        $query->on('venue.id', '=', 'venue_record.id'); 

        // load foursquare info
        $query->join('venue_meta_healthyfood', 'LEFT');
        $query->on('venue.id', '=', 'venue_meta_healthyfood.id'); 

        return $query;

    }

    public function action_price() {
        $_POST['venue_id'] = \Input::get('venue_id');
        $_POST['price'] = \Input::get('price'); 
        return $this->post_price();
    }

    public function post_price() {
        $system_venue_id = \Input::post('venue_id');
        $price = intval(\Input::post('price')); 
    
        if(!$price || !$system_venue_id) {
            return $this->sendAPIResponse(null, 400, 'Please provide a value both for venue_id and price!');
        }

        $Venue = \Model_Venue::find($system_venue_id);

        if(!$Venue) {
            return $this->sendAPIResponse(null, 400, 'Unkown venue / invalid venue_id!');
        } 


        try {

            $Record = new \Model_Record();
            // $Record->record_hash = $record_hash;
            $Record->object_id = $system_venue_id;  
            $Record->property = 'price';
            $Record->value = $price; 
            $Record->datatype = 'absolute'; 
            $Record->save(); 

            if ( \Collection\Venue::calculateVenuePriceScore($system_venue_id) ) {
                return $this->sendAPIResponse(array(
                    'success' => true
                ));
            }

            return $this->sendAPIResponse(null, 400, 'Unkown error'); 

        }
        catch (\Database_Exception $err) {  
            // catch error. normally there shouldn't be an error, but in the development phase it's possible to have incomplete data, so errors could occur at this place.                      
            return $this->sendAPIResponse(null, 400, $err);
        }

    }


}
<?php


namespace Manager;

class Controller_Api extends \Controller_Rest
{

    public function get_region_list()
    {

    	$text = \Input::get('query');

    	if(!$text) {
    		return $this->response(array());
    	}


    	$Results = \Model_Tracking_Region::query()->where('name', 'like', '%'.$text.'%')->limit(10)->get();

    	$response = array();

    	foreach($Results as $Result) {
    		$response[] = $Result->name;
    	}  

        return $this->response($response);
    }

    public function post_save_tracking_points()
    {

    	$locations = \Input::post('locations');
    	$region_name = \Input::post('region');

		$Tracking_Region = \Model_Tracking_Region::query()->where('name', $region_name)->get_one();

        if(!$Tracking_Region) {
            $Tracking_Region = new \Model_Tracking_Region();
            $Tracking_Region->lat = $locations[0]['lat'];
            $Tracking_Region->lng = $locations[0]['lng'];
            $Tracking_Region->radius = 1;
            $Tracking_Region->name = $region_name;
            $Tracking_Region->save();
        }

		foreach($locations as $location) {
    		$Tracking_Point = new \Model_Tracking_Point();
    		$Tracking_Point->lat = $location['lat'];
    		$Tracking_Point->lng = $location['lng'];
    		$Tracking_Point->region_id = $Tracking_Region->id;
            $Tracking_Point->scanned = 0;
    		$Tracking_Point->save();
    	}  

        return $this->response(array('success' => 1));
    }


    public function get_venues()
    {

        $text = \Input::get('query');

        if(!$text) {
            return $this->response(array());
        }
 

        $result = \DB::select()->from('venue')->join('roles','LEFT')->on('roles.id', '=', 'users.role_id');

        $Results = \Model_Venue::query()->where('region_id', 1)->limit(100)->get();

        $response = array();

        foreach($Results as $Result) {
            $response[] = $Result->name;
        }  

        return $this->response($response);
    }


    public function get_tracking_points()
    {

        $region_id = \Input::get('region_id');

        // ...
    }

    public function get_records()
    {

        $object_ids = explode(',', \Input::get('ids'));
        $properties = explode(',', \Input::get('properties'));
        $timespan = \Input::get('timespan');
        
        // datatype relative or absolute
        $datatype = \Input::get('datatype') ? \Input::get('datatype') : 'absolute';

        $startdate = strtotime(\Input::get('start'));
        $enddate = strtotime(\Input::get('end'));

    $startdate = strtotime('2013-03-07 00:00:00');
        $enddate = strtotime('2013-03-20 00:00:00');
  
        $colums = array();
        $colums[] = \DB::expr('MAX( value ) as v');
        $colums[] = 'object_id';
 

        switch($timespan) {
            case 'hour':
                $group_date_format = 'DATE_FORMAT(created_at, \'%Y%j%H\')';
                // $colums[] = \DB::expr('DATE_FORMAT(created_at, \'%Y-%m-%d %H\') as t');
                $colums[] = \DB::expr('DATE_FORMAT(created_at, \'%Y-%m-%d %H:%i:%s\') as t');
                break;
            case 'day':
                $group_date_format = 'DATE(created_at)';
                $colums[] = \DB::expr('DATE(created_at) as t');
                break;
        }

        $response = array();

        foreach($properties as $property) {

            $query = \DB::select_array($colums);
            $query->from('record');
            // $query->join('roles','LEFT')->on('roles.id', '=', 'users.role_id');
            $query->where('object_id', 'IN', $object_ids);
            $query->where('datatype', $datatype);
            $query->where('property', $property);
            $query->limit(100);
            $query->order_by('created_at', 'asc');

            $query->group_by(\DB::expr($group_date_format));
            $query->group_by('object_id');

            if($startdate) {
                $query->where('created_at', '>=', date('Y-m-d H:i:s', $startdate));
            }

            if($enddate) {
                $query->where('created_at', '<=', date('Y-m-d H:i:s', $enddate));
            }

            $Results = $query->execute();   
            // $response[$property] = array();
            $response  = array();

            foreach($Results->as_array() as $key => $row) {
                // $response[$property][$row['object_id']][$row['t']] = intval($row['v']);
                $response[] = array(
                    strtotime($row['t']) * 1000,
                    intval($row['v'])
                );
            }

        }

        return $this->response($response);

    }

    public function get_pictures()   
    { 
          header('Access-Control-Allow-Origin: *'); 
 
 
        $options = array();
        $options['order_by'] = 'likes';
        $options['order_dir'] = 'desc';
        $options['filter'] = array();
        $options['filter']['date_range'] = 'thisweek'; 
  
         // get pics
        $options['per_page'] = 500;
        $options['offset'] = 0; 
        $data['pictures'] = \Collection\Interaction::search($options);
 
        return $this->response($data);

    } 

/*
    {
    "type": "FeatureCollection",
        "features": [
        {
            "type": "Feature",
            "geometry": {"type": "Point", "coordinates": [-78.0, 0.5]},
            "properties": {
                "title": "Hello, World"
            }
        }
    ]
}
*/


}
<?php

namespace Collection;

class Record 
{

    public static function recordExists($object_id, $property, $datatype, $created_at) { 
    	$query = \Model_Record::query();
    	$query->where('object_id', $object_id);
    	$query->where('property', $property);
    	$query->where('datatype', $datatype);
    	$query->where('created_at', $created_at);
    	$query->limit(1); 
		return $query->count() > 0 ? true : false;  
    }

    public static function countTotal($object_id, $property) { 
    	$query = \Model_Record::query();
    	if(is_array($object_id)) {
    		$query->where('object_id', 'IN', $object_id);
    	} else {
    		$query->where('object_id', $object_id);	
    	} 
    	$query->where('property', $property);
    	$query->where('datatype', 'absolute'); 
		return $query->count();  
    } 

    public static function create($object_id, $property, $datatype, $created_at, $value) { 
    	$Record = new \Model_Record();
    	$Record->object_id = $object_id;
    	$Record->property = $property;
    	$Record->datatype = $datatype;
    	$Record->value = $value;
    	$Record->save(); 

    	$Record->created_at = $created_at;
    	$Record->save(); 

    	return $Record;
    } 

    public static function createFromArray($data) { 
    	$Record = new \Model_Record();
    	$Record->object_id = $data['object_id'];
    	$Record->property = $data['property'];
    	$Record->datatype = $data['datatype'];
    	$Record->value = $data['value'];
    	$Record->save();  

    	$Record->created_at = $data['created_at'];
    	$Record->save();  

    	return $Record;
    }


}

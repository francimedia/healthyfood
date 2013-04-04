<?php

namespace Collection;

class Singleton 
{

	public static function create($object_type) {
		$Singleton = new \Model_Singleton();
		$Singleton->object_type = $object_type;
		$Singleton->save();	
		return $Singleton->id;
	}

}

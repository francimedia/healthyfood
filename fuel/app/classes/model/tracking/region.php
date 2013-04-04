<?php

class Model_Tracking_Region extends \Orm\Model
{

    protected static $_table_name = 'tracking_region';

	protected static $_properties = array(
		'id',
		'name',
		'lat',
		'lng',
		'radius',
	);

	public static function validate($factory)
	{
		$val = Validation::forge($factory);
 
		return $val;
	}	

}

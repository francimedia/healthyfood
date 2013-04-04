<?php

class Model_Tracking_Point extends \Orm\Model
{


    protected static $_table_name = 'tracking_point';

	protected static $_properties = array(
		'id',
		'lat',
		'lng',
		'scanned',
		'region_id',
		'created_at',
		'updated_at',
	);

	protected static $_observers = array(
		'Orm\Observer_CreatedAt' => array(
			'events' => array('before_insert'),
			'mysql_timestamp' => true,
		),
		'Orm\Observer_UpdatedAt' => array(
			'events' => array('before_save'),
			'mysql_timestamp' => true,
		),
	);
}

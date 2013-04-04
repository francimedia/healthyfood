<?php

class Model_Tracking_Log extends \Orm\Model
{

    protected static $_table_name = 'tracking_log';

	protected static $_properties = array(
		'id',
		'object_id',
		'success',
		'is_latest',
		'source',
		'created_at' 
	);

	protected static $_observers = array(
		'Orm\Observer_CreatedAt' => array(
			'events' => array('before_insert'),
			'mysql_timestamp' => true,
		) 
	);
}

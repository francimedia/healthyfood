<?php


class Model_Record extends \Orm\Model
{

    protected static $_table_name = 'record';

	protected static $_properties = array(
		'id',
		// 'record_hash', // this column should be removed later, but good for dev. phase to identify duplicate tracking etc.
		'object_id',
		'property',
		'value',
		'datatype',
		'created_at' 
	);

	protected static $_observers = array(
		'Orm\Observer_CreatedAt' => array(
			'events' => array('before_insert'),
			'mysql_timestamp' => true,
		) 
	);
}

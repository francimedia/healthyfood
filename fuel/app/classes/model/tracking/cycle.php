<?php

class Model_Tracking_Cycle extends \Orm\Model
{

    protected static $_table_name = 'tracking_cycle';

	protected static $_properties = array(
		'id',
		'object_id',
		'frequency',
		'TZ',
	);

}

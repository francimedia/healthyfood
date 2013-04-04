<?php

class Model_Interaction extends \Orm\Model
{
	protected static $_properties = array(
		'id',
		'time_created',
		'location_id',
		'interaction_type',
		'source',
	);

}

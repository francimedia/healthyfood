<?php

// ALTER TABLE  `interaction` CHANGE  `location_id`  `venue_id` INT( 12 ) NULL DEFAULT NULL

class Model_Interaction extends \Orm\Model
{
    protected static $_table_name = 'interaction';
    
	protected static $_properties = array(
		'id',
		'time_created',
		'venue_id',
		'interaction_type',
		'source',
	);

}

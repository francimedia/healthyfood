<?php

class Model_Event extends \Orm\Model
{
	protected static $_properties = array(
		'id',
		'title',
		'starttime',
		'endtime',
		'desc',
		'venue_id',
	);

}

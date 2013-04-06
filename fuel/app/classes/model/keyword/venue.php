<?php

class Model_Keyword_Venue extends \Orm\Model
{

    protected static $_table_name = 'keyword_venue';

	protected static $_properties = array(
		'id',
		'venue_id',
		'keyword_id',
		'count',
	);

}

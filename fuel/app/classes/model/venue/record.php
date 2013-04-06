<?php

class Model_Venue_Record extends \Orm\Model
{

    protected static $_table_name = 'venue_record';

	protected static $_properties = array(
		'id',
		'checkin',
		'checkin_unique',
		'comment',
		'like',
		'review',
		'image', // instagram pic count? tbd
		'photos', // foursquare pic count?
		'specials',
		'herenow',
		'mayor',
		'rating',
		'price',
	);
 
}

<?php

class Model_Venue_Meta_Foursquare extends \Orm\Model
{

    protected static $_table_name = 'venue_meta_foursquare';

	protected static $_properties = array(
		'id',
		'venue_foursquare_id',
		'canonicalUrl',
	);

}

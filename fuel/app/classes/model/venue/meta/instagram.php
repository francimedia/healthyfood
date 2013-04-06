<?php

class Model_Venue_Meta_Instagram extends \Orm\Model
{
    protected static $_table_name = 'venue_meta_instagram';

	protected static $_properties = array(
		'id',
		'venue_instagram_id',
		'canonicalUrl',
	);

}

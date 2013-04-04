<?php

class Model_Venue extends \Orm\Model
{

    protected static $_table_name = 'venue';

	protected static $_properties = array(
		'id',
		'lat',
		'lng',
		'name',
		'street',
		'postalCode',
		'city',
		'state',
		'cc',
		'region_id'
	);

}

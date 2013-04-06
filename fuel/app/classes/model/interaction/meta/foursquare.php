<?php

class Model_Interaction_Meta_Foursquare extends \Orm\Model
{

    protected static $_table_name = 'interaction_meta_foursquare';

	protected static $_properties = array(
		'id',
		'foursquare_user_id',
		'username',
	);

}

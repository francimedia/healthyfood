<?php

class Model_User extends \Orm\Model
{

    protected static $_table_name = 'user'; 

	protected static $_properties = array(
		'id',
		'username',
		'first_name',
		'last_name',
		'picture_url',
	);

}

<?php

class Model_User_Meta_Instagram extends \Orm\Model
{


    protected static $_table_name = 'user_meta_instagram'; 

	protected static $_properties = array(
		'id',
		'instagram_user_id',
		'username',
	);

}

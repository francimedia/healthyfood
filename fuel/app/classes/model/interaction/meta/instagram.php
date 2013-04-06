<?php

// ALTER TABLE  `interaction_meta_instagram` CHANGE  `instagram_picture_id`  `interaction_instagram_id` VARCHAR( 48 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL

class Model_Interaction_Meta_Instagram extends \Orm\Model
{
	
    protected static $_table_name = 'interaction_meta_instagram';

	protected static $_properties = array(
		'id',
		'interaction_instagram_id',
		'instagram_user_id',
		'username',
		'image_1',
		'image_2',
		'image_3',
		'link',
		'caption',
		'likes',
		'comments',
	);

}

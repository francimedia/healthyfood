<?php

namespace Fuel\Migrations;

class Create_venue_meta_healthyfoods
{
	public function up()
	{
		\DBUtil::create_table('venue_meta_healthyfoods', array(
			'id' => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true),
			'price_score' => array('constraint' => 3, 'type' => 'int'),

		), array('id'));
	}

	public function down()
	{
		\DBUtil::drop_table('venue_meta_healthyfoods');
	}
}
<?php

namespace Fuel\Migrations;

class Create_events
{
	public function up()
	{
		\DBUtil::create_table('events', array(
			'id' => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true),
			'title' => array('constraint' => 255, 'type' => 'varchar'),
			'starttime' => array('type' => 'timestamp'),
			'endtime' => array('type' => 'timestamp'),
			'desc' => array('type' => 'text'),
			'venue_id' => array('constraint' => 12, 'type' => 'int'),

		), array('id'));
	}

	public function down()
	{
		\DBUtil::drop_table('events');
	}
}
<?php

namespace Collection;


class Tracking_Cylce 
{

	public static function create($system_venue_id, $frequency = 'hourly') {
		// add hourly tracking by default at the moment.
		$Cycle = new \Model_Tracking_Cycle();
		$Cycle->object_id = $system_venue_id;
		$Cycle->frequency = $frequency;
		// 2do: manage timezone based tracking
		// $Cycle->TZ = 'ET';
		$Cycle->save();				
	}
	
}
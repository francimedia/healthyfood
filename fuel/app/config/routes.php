<?php
return array(
	'_root_'  => 'app/welcome/index',  // The default route
	'_404_'   => 'app/welcome/404',    // The main 404 route
	
	'hello(/:name)?' => array('welcome/hello', 'name' => 'hello'),
);
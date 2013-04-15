<?php

/**
 * Alias the Log namespace to global so we can overload the Log class
 */
Autoloader::add_core_namespace('Spider');

/**
 * Inform the autoloader where to find what...
 */

/**
 * v1.x style classes.
 */
Autoloader::add_classes(array(
	'Spider\\BaseSpider'                    => __DIR__.'/classes/base_spider.php',

	'Spider\\Foursquare\\Client'                    => __DIR__.'/classes/foursquare/client.php',
	'Spider\\Foursquare\\Spider'                    => __DIR__.'/classes/foursquare/spider.php',
	'Spider\\Foursquare\\SnapSpider'                    => __DIR__.'/classes/foursquare/snapspider.php',

	'Spider\\Instagram\\Client'                    => __DIR__.'/classes/instagram/client.php',
	'Spider\\Instagram\\Spider'                    => __DIR__.'/classes/instagram/spider.php',

));



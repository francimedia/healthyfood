<?php
/**
 * Fuel is a fast, lightweight, community driven PHP5 framework.
 *
 * @package    Fuel
 * @version    1.5
 * @author     Fuel Development Team
 * @license    MIT License
 * @copyright  2010 - 2013 Fuel Development Team
 * @link       http://fuelphp.com
 */

/**
 * If you want to override the default configuration, add the keys you
 * want to change here, and assign new values to them.
 */

return array(
	/**
	 * To enable you to split up your application into modules which can be
	 * routed by the first uri segment you have to define their basepaths
	 * here.
	 */
	'module_paths' => array(
	    APPPATH.'modules'.DS,		// path to application modules
	    APPPATH.'..'.DS.'globalmods'.DS	// path to our global modules
	),
	'always_load' => array(
	    'packages' => array(
	        'orm',
	        'parser'
	    ),
	),	
	'profiling'  => false
);

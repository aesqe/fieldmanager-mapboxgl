<?php
/*
	Plugin Name: Fieldmanager Mapbox GL
	Plugin URI: https://github.com/aesqe/fm-mapboxgl
	Description: A Mapbox GL field type for Fieldmanager
	Version: 0.1.1
	Author: Bruno "Aesqe" Babic
	Author URI: http://skyphe.org
*/

if ( ! defined('ABSPATH') ) {
	exit;
}

define('FM_MAPBOXGL_VERSION', '0.1.0');
define('FM_MAPBOXGL_PATH', plugin_dir_path(__FILE__));
define('FM_MAPBOXGL_URL', plugin_dir_url(__FILE__) );

function fm_mapboxgl_init ()
{
	if( defined('FM_VERSION') ) {
		require_once(__DIR__ . '/php/class-fieldmanager-mapboxgl.php');
	}
}
add_action('plugins_loaded', 'fm_mapboxgl_init');

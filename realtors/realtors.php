<?php

/**
 * Plugin Name: Realtors
 * Description: This will generate realtors search form and search results along with shortcodes. This will call an api with get request and parse json response.
 * Plugin URI: http://tier5.us
 * Version: 1.0.0
 * Author: Tier5
 * Author URI: http://tier5.us
 * License: GPLv2
 */

define('REALTORS_ROOT_DIR', dirname(__FILE__));
define('REALTORS_API_DIR', REALTORS_ROOT_DIR.'/api');
define('REALTORS_INCLUDES_DIR', REALTORS_ROOT_DIR.'/includes');
define('REALTORS_SHORTCODES_DIR', REALTORS_ROOT_DIR.'/shortcodes');
define('REALTORS_TEAMPLATE_DIR', REALTORS_ROOT_DIR.'/templates');
define('REALTORS_ASSETS_DIR', REALTORS_ROOT_DIR.'/assets');

define('REALTORS_URI', plugins_url('realtors'));
define('REALTORS_ASSETS_URI', REALTORS_URI.'/assets');

require_once REALTORS_INCLUDES_DIR.'/load.php';
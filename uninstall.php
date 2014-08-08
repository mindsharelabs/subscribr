<?php
/**
 * uninstall.php
 *
 * @created   7/25/14 9:44 AM
 * @author    Mindshare Studios, Inc.
 * @copyright Copyright (c) 2014
 * @link      http://www.mindsharelabs.com/documentation/
 *
 */

//if uninstall not called from WordPress exit
if(!defined('WP_UNINSTALL_PLUGIN')) {
	exit();
}

if(!defined('SUBSCRIBR_OPTIONS')) {
	define('SUBSCRIBR_OPTIONS', 'subscribr_options');
}

$option_name = SUBSCRIBR_OPTIONS;
delete_option($option_name);

// For site options in multisite
//delete_site_option($option_name);

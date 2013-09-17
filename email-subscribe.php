<?php
/*
Plugin Name: Email Subscribe
Plugin URI: http://mindsharelabs.com/products/
Description: Allows WordPress users to subscribe to email notifications for new posts, pages, and custom types, filterable by taxonomies.
Version: 0.1
Author: Mindshare Studios, Inc.
Author URI: http://mind.sh/are/
License: GNU General Public License
License URI: LICENSE
Text Domain: email-subscribe
Domain Path: /lang
*/

/**
 *
 * @author    Mindshare Studios, Inc.
 * @copyright Copyright (c) 2013
 * @link      http://www.mindsharelabs.com/documentation/
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 3, as
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * ToDo List:
 *
 * @todo      - internationalize
 * @todo      - add options page
 * @todo      - add default email template file (or several)
 * @todo      - add plugin_action_links
 *
 * Changelog:
 *
 * 0.1 - Initial release
 *
 */

if(!defined('ES_MIN_WP_VERSION')) {
	define('ES_MIN_WP_VERSION', '3.4');
}

if(!defined('ES_PLUGIN_NAME')) {
	define('ES_PLUGIN_NAME', 'Email Subscribe');
}

if(!defined('ES_PLUGIN_SLUG')) {
	define('ES_PLUGIN_SLUG', dirname(plugin_basename(__FILE__))); // email-subscribe
}

if(!defined('ES_DIR_PATH')) {
	define('ES_DIR_PATH', plugin_dir_path(__FILE__));
}

if(!defined('ES_DIR_URL')) {
	define('ES_DIR_URL', trailingslashit(plugins_url(NULL, __FILE__)));
}

if(!defined('ES_OPTIONS')) {
	define('ES_OPTIONS', 'email_subscribe_options');
}

// check WordPress version
global $wp_version;
if(version_compare($wp_version, ES_MIN_WP_VERSION, "<")) {
	exit(ES_PLUGIN_NAME.' requires WordPress '.ES_MIN_WP_VERSION.' or newer.');
}

// deny direct access
if(!function_exists('add_action')) {
	header('Status: 403 Forbidden');
	header('HTTP/1.1 403 Forbidden');
	exit();
}

if(!class_exists("EmailSubscribe")) :

	/**
	 * Class EmailSubscribe
	 */
	class EmailSubscribe {

		/**
		 * The plugin version number.
		 *
		 * @var string
		 */
		private $version = '0.1';

		/**
		 * Initialize the plugin. Set up actions / filters.
		 *
		 */
		public function __construct() {

			// i8n
			add_action('plugins_loaded', array($this, 'load_textdomain'));
			
			// action links
			add_filter('plugin_action_links', array($this, 'plugin_action_links'), 10, 2);

			// action to send emails
			//add_action('wp_insert_post', array($this, 'user_query'));

			// actions to add fields to the user profile, register form and edit profile
			add_action('show_user_profile', array($this, 'user_profile_fields'));
			add_action('edit_user_profile', array($this, 'user_profile_fields'));
			add_action('register_form', array($this, 'user_profile_fields'));

			// actions to store updated preferences in the user meta table
			add_action('personal_options_update', array($this, 'update_user_meta'));
			add_action('edit_user_profile_update', array($this, 'update_user_meta'));

			$this->options_init();
		}

		/**
		 * Returns the class name and version.
		 *
		 * @return string
		 */
		public function __toString() {
			return get_class($this).' '.$this->version;
		}

		/**
		 * Register the plugin text domain for translation
		 *
		 */
		public function load_textdomain() {
			load_plugin_textdomain('email-subscribe', FALSE, ES_PLUGIN_SLUG);
		}

		/**
		 *
		 * Add settings link to plugins page
		 *
		 * @param $links
		 * @param $file
		 *
		 * @return array
		 */
		public function plugin_action_links($links, $file) {
			if($file == plugin_basename(__FILE__)) {
				$settingslink = '<a href="options-general.php?page='.ES_PLUGIN_SLUG.'-settings" title="'.__('Email Subscribe Settings','email-subscribe').'">'.__('Settings','email-subscribe').'</a>';
				array_unshift($links, $settingslink);
			}
			return $links;
		}

		/**
		 * Check saved options, perform related actions
		 *
		 */
		public function options_init() {
			// load existing options
			include_once('controllers/options-init.php');
			$this->options = new es_options($this);

			// load the options framework
			include_once('lib/mindshare-options-framework/mindshare-options-framework.php');
			include_once('views/options-page.php');

		}

		/**
		 * Displays the custom user fields on the registration and profile screens.
		 *
		 * @param $user
		 */
		public function user_profile_fields($user) {
			include_once('views/profile_fields.php');
		}

		/**
		 * @param $user_id
		 *
		 * @return bool
		 */
		public function update_user_meta($user_id) {

			if(!current_user_can('edit_user', $user_id)) {
				return FALSE;
			}

			update_user_meta($user_id, 'address', $_POST['address']);

		}

		/**
		 * @param $post_id
		 */
		public function user_query($post_id) {

			if(wp_is_post_revision($post_id) == FALSE) {
				if($_POST['post_type'] == "post" && $_POST['post_status'] == "publish") { // modify this to set the post type, or remove to allow all post types
					$post = get_post($post_id);
					// quit if post has been published already
					if($post->post_date != $post->post_modified) {
						return;
					}
					$this->notification_send($post_id);
					return;
				}
			}
			if(!current_user_can('edit_post', $post_id)) {
				return;
			}
		}

		/**
		 * @param $post_id
		 */
		public function notification_send($post_id) {

			$template_files = $this->locate_theme_templates();

			// test for user defined PHP email templates in the 'email-subscribe' folder in the current theme (or child theme)
			if(locate_template($template_files)) {
				// a template was found, so we'll try to use it

			} else {
				// use the default template
			}



			//wp_mail($to, $subject, $message, $headers, $attachments);
		}

		/**
		 * Scans the current theme for template files. Based on ES_file_dir_array.
		 *
		 * @param null   $dir
		 * @param string $exts
		 *
		 * @return array
		 */
		public function locate_theme_templates($dir = NULL, $exts = 'php') {
			if(!isset($dir)) {
				// wp-content/themes/__active_theme__/email-subscribe
				$dir = trailingslashit(get_template_directory()).ES_PLUGIN_SLUG;
			}
			$files = array();
			$i = -1;
			$handle = opendir($dir);
			$exts = explode(',', strtolower($exts));
			while(FALSE !== ($file = readdir($handle))) {
				foreach($exts as $ext) {
					if(preg_match('/\.'.$ext.'$/i', $file, $test)) {
						$files[] = str_replace($_SERVER['DOCUMENT_ROOT'], '', $dir.$file);
						++$i;
					}
				}
			}
			closedir($handle);
			return $files;
		}
	}
endif;

$email_subscribe = new EmailSubscribe;

<?php
/*
Plugin Name: Subscribr
Plugin URI: http://mindsharelabs.com/products/
Description: Allows WordPress users to subscribe to email notifications for new posts, pages, and custom types, filterable by taxonomies.
Version: 0.1
Author: Mindshare Studios, Inc.
Author URI: http://mind.sh/are/
License: GNU General Public License
License URI: LICENSE
Text Domain: subscribr
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
 * @todo      - finish default email template file
 * @todo      - add double opt in
 * @todo      - add opt out option for individual posts
 * @todo      - finish internationalizing
 * @todo      - add merge fields
 * @todo      - add email editor(s) to options page
 * @todo      - add html/plain text options
 * @todo      - add scheduling options / digest mode
 * @todo      - add analytics options... talk to Bryce about this
 * @todo      - add minimum role option for notifications
 *
 * Premium features:
 *
 * @todo      - add integration with MailChimp/Mandrill
 * @todo      - add integration with Constant Contact
 * @todo      - add integration with Aweber
 * @todo      - add CSV subscriber export
 * @todo      - SMS text messages
 * @todo      - notification on site (like Facebook)
 * @todo      - add subscriber management to settings
 * @todo      - add integration with 3rd-party SMTP servers and/or advanced SMTP settings
 *
 * Developer Notes:
 *
 * Made one minor CSS change to make Chosen work well on WordPress user profiles,
 * this will need to be retested if we upgrade Chosen.
 *
 * Changelog:
 *
 * 0.1 - Initial release
 *
 */

if(!defined('SUBSCRIBR_MIN_WP_VERSION')) {
	define('SUBSCRIBR_MIN_WP_VERSION', '3.5');
}

if(!defined('SUBSCRIBR_PLUGIN_NAME')) {
	define('SUBSCRIBR_PLUGIN_NAME', 'Subscribr');
}

if(!defined('SUBSCRIBR_PLUGIN_SLUG')) {
	define('SUBSCRIBR_PLUGIN_SLUG', dirname(plugin_basename(__FILE__))); // subscribr
}

if(!defined('SUBSCRIBR_DIR_PATH')) {
	define('SUBSCRIBR_DIR_PATH', plugin_dir_path(__FILE__));
}

if(!defined('SUBSCRIBR_DIR_URL')) {
	define('SUBSCRIBR_DIR_URL', trailingslashit(plugins_url(NULL, __FILE__)));
}

if(!defined('SUBSCRIBR_OPTIONS')) {
	define('SUBSCRIBR_OPTIONS', 'subscribr_options');
}

// check WordPress version
global $wp_version;
if(version_compare($wp_version, SUBSCRIBR_MIN_WP_VERSION, "<")) {
	exit(SUBSCRIBR_PLUGIN_NAME.' requires WordPress '.SUBSCRIBR_MIN_WP_VERSION.' or newer.');
}

// deny direct access
if(!function_exists('add_action')) {
	header('Status: 403 Forbidden');
	header('HTTP/1.1 403 Forbidden');
	exit();
}

if(!class_exists("Subscribr")) :

	/**
	 * Class Subscribr
	 */
	class Subscribr {

		/**
		 * The plugin version number.
		 *
		 * @var string
		 */
		private $version = '0.1';

		/**
		 * @var $options - holds all plugin options
		 */
		protected $options;

		/**
		 * Initialize the plugin. Set up actions / filters.
		 *
		 */
		public function __construct() {

			// i8n
			add_action('plugins_loaded', array($this, 'load_textdomain'));

			// setup the options page
			add_action('init', array($this, 'options_init'));

			// load scripts, etc
			add_action('wp_print_scripts', array($this, 'print_scripts'));
			add_action('admin_head', array($this, 'head_scripts'));
			add_action('wp_head', array($this, 'head_scripts'));

			// action links
			add_filter('plugin_action_links', array($this, 'plugin_action_links'), 10, 2);

			// action to send emails
			add_action('publish_post', array($this, 'user_query'));


			//$this->notification_send(2); debugging
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
			load_plugin_textdomain('subscribr', FALSE, SUBSCRIBR_PLUGIN_SLUG);
		}

		/**
		 * Enqueues plugin CSS/JS.
		 *
		 */
		public function print_scripts() {

			if($this->do_scripts()) {

				// register scripts
				$scripts = array();

				$scripts[] = array(
					'handle' => 'chosen-js',
					'src'    => SUBSCRIBR_DIR_URL.'lib/chosen/chosen.jquery.min.js',
					'deps'   => array('jquery')
				);

				/*$scripts[] = array(
					'handle' => 'subscribr',
					'src'    => SUBSCRIBR_DIR_URL.'js/main.js',
					'deps'   => array('jquery')
				);*/

				foreach($scripts as $script) {
					wp_enqueue_script($script['handle'], $script['src'], $script['deps'], $this->version);
				}

				// register styles
				$styles = array(
					'chosen-css'    => SUBSCRIBR_DIR_URL.'lib/chosen/chosen.min.css',
					'subscribr-css' => SUBSCRIBR_DIR_URL.'css/subscribr.min.css',
				);

				foreach($styles as $k => $v) {
					wp_enqueue_style($k, $v, FALSE, $this->version);
				}
			}
		}

		/**
		 * Outputs JS into the HEAD
		 *
		 */
		public function head_scripts() {
			if($this->do_scripts()) {
				?>
				<script type="text/javascript">
					jQuery.noConflict();
					jQuery(document).ready(function() {
						emailSubscribeInit();
					});

					function emailSubscribeInit() {
						jQuery('.chosen-select').chosen({
							search_contains:           true,
							width:                     '100%',
							placeholder_text_multiple: 'Select Email Subscriptions',
							no_results_text:           'No results'
						});
					}
				</script>
			<?php
			}
		}

		public function do_scripts() {
			// only enqueue if we're on the register screen, user profile, or Theme_My_Login pages (and the options are enabled)
			if(($this->is_register() && $this->get_option('show_on_register')) || ($this->is_profile() && $this->get_option('show_on_profile')) || (class_exists('Theme_My_Login'))) {
				return TRUE;
			} else {
				return FALSE;
			}
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
				$settingslink = '<a href="options-general.php?page='.SUBSCRIBR_PLUGIN_SLUG.'-settings" title="'.__('Email Subscribe Settings', 'subscribr').'">'.__('Settings', 'subscribr').'</a>';
				array_unshift($links, $settingslink);
			}
			return $links;
		}

		/**
		 * Check saved options, perform related actions
		 *
		 */
		public function options_init() {
			// load the options framework
			include_once('lib/mindshare-options-framework/mindshare-options-framework.php');
			include_once('views/options-page.php');

			// load existing options
			include_once('controllers/options-init.php');
			$this->options = get_option(SUBSCRIBR_OPTIONS);
			new subscribr_options($this->options);

		}

		/**
		 * Displays the custom user fields on the registration and profile screens.
		 *
		 * @param $user
		 */
		public function user_profile_fields($user) {
			include_once('views/profile-fields.php');
		}

		/**
		 * @param $user_id
		 *
		 * @return bool
		 */
		public function update_user_meta($user_id) {

			if(!(current_user_can('edit_user', $user_id) || $_POST["wp-submit"] == "Register")) { // @todo add nonce?
				return FALSE;
			}

			if(array_key_exists('subscribr-terms', $_POST)) {
				$subscribr_terms = array();

				// delete any invalid terms the user may have typed in manually
				foreach($_POST['subscribr-terms'] as $term) {
					$term_result = term_exists($term);
					if($term_result !== 0 && $term_result !== NULL) {
						$subscribr_terms[] = $term;
					}
				}
			} else {
				// no terms were selected
				$subscribr_terms = FALSE;
			}

			if(array_key_exists('subscribr-pause', $_POST) && $_POST['subscribr-pause'] == 1) {
				// the user is pausing
				$subscribr_pause = 1;
			} else {
				$subscribr_pause = 0;
			}

			if(array_key_exists('subscribr-unsubscribe', $_POST) && $_POST['subscribr-unsubscribe'] == 1) {
				// the user is unsubscribing
				$subscribr_unsubscribe = 1;
				$subscribr_terms = FALSE; // remove existing notifications
				$subscribr_pause = 0;
			} else {
				$subscribr_unsubscribe = 0;
			}

			update_user_meta($user_id, 'subscribr-terms', $subscribr_terms);
			update_user_meta($user_id, 'subscribr-pause', $subscribr_pause);
			update_user_meta($user_id, 'subscribr-unsubscribe', $subscribr_unsubscribe);
		}

		/**
		 *
		 * When a new post is saved find all users with matching notification preferences.
		 *
		 * @param $post_id
		 */
		public function user_query($post_id) {
			
			if(!wp_is_post_revision($post_id)) {

				echo '<pre>'; var_dump(get_post($post_id)); echo '</pre>'; die;


				do_action('subscribr_pre_user_query');

				// email notifications
				if($this->get_option('enabled_email_notifications')) {

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

				do_action('subscribr_post_user_query');
			}
		}

		/**
		 * @param $post_id
		 */
		public function notification_send($post_id) {

			$template_files = $this->locate_theme_templates();

			// test for user defined PHP email templates in the 'subscribr' folder in the current theme (or child theme)
			if(locate_template($template_files)) {
				// a template was found, so we'll try to use it

			} else {
				// use the default template
			}
			//wp_mail($to, $subject, $message, $headers, $attachments);
		}

		/**
		 * Scans the current theme for template files. Based on mapi_file_dir_array().
		 *
		 * @param null   $dir
		 * @param string $exts
		 *
		 * @return array
		 */
		public function locate_theme_templates($dir = NULL, $exts = 'php') {
			if(!isset($dir)) {
				// e.g. wp-content/themes/__ACTIVE_THEME__/subscribr
				$dir = trailingslashit(get_template_directory()).SUBSCRIBR_PLUGIN_SLUG;
			}

			if(file_exists($dir)) {
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
			} else {
				return FALSE; // template folder was not found
			}
		}

		/**
		 * Determine what taxonomies are enabled for email notification, if any.
		 *
		 */
		public function get_enabled_taxonomies() {

			$enabled_terms = $this->get_option('enabled_terms');
			$all_taxonomies = $this->get_default_taxonomies();

			if($this->get_option('enable_all_terms')) {
				// return all available taxonomies
				return $all_taxonomies;
			} elseif($enabled_terms) {
				$enabled_taxonomies = array();

				// this bit gets nasty because, surprisingly, there is no
				// WP function to lookup a taxonomy from just a `term_id`
				// all WP term related functions have `taxonomy` as a required param
				// in this case we don't know the taxonomy so we have to look it up

				// loop through user enabled terms
				foreach($enabled_terms as $term) {

					// loop through all taxonomies
					foreach($all_taxonomies as $tax) {

						// check if the term exists in each taxonomy
						$term_result = term_exists($term, $tax);
						if(!empty($term_result) && !is_a($term_result, 'WP_Error')) {

							// if so, add it to our array
							$term_meta = get_term($term_result['term_id'], $tax, ARRAY_A);
							$enabled_taxonomies[] = $term_meta['taxonomy'];
						}
					}
				}

				$enabled_taxonomies = array_unique($enabled_taxonomies);

				// return all user enabled taxonomies
				return $enabled_taxonomies;
			} else {
				// no terms are enabled, exit now
				return FALSE;
			}
		}

		/**
		 * @return array
		 */
		public function get_default_taxonomies() {
			$taxonomies = get_taxonomies();
			$disabled_taxonomies = array('nav_menu', 'post_format', 'link_category');
			$taxonomies = array_diff($taxonomies, $disabled_taxonomies);
			return $taxonomies;
		}

		/**
		 *
		 * Retrieve an option from the options array.
		 *
		 * @param null $name
		 *
		 * @return string
		 */
		public function get_option($name = NULL) {
			if(empty($name)) {
				return FALSE;
			}

			if($this->options && array_key_exists($name, $this->options)) {

				// check if the option is a URL
				if(stristr($name, 'uri')) {
					return html_entity_decode($this->options[$name]);
				} else {
					return $this->options[$name];
				}
			} else {
				return FALSE;
			}
		}

		/**
		 * @return bool
		 */
		public function is_profile() {
			return in_array($GLOBALS['pagenow'], array('profile.php'));
		}

		/**
		 * @return bool
		 */
		public function is_register() {
			return in_array($GLOBALS['pagenow'], array('wp-register.php'));
		}
	}
endif;

$subscribr = new Subscribr;



<?php
/*
Plugin Name: Subscribr
Plugin URI: http://mindsharelabs.com/products/
Description: Allows WordPress users to subscribe to email notifications for new posts, pages, and custom types, filterable by taxonomies.
Version: 0.1.1
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
 * @todo      - add option to separate diff taxonomies on profile update
 * @todo      - add widget
 * @todo      - add option to post notifications for update as well as new posts
 * @todo      - add html/plain text options
 * @todo      - add scheduling options / digest mode
 * @todo      - add analytics options
 * @todo      - add minimum role option for notifications
 * @todo      - add double opt-in
 *
 * Premium features:
 *
 * @todo      - SMS text messages
 * @todo      - add integration with 3rd-party SMTP servers and/or advanced SMTP settings
 * @todo      - add integration with MailChimp/Mandrill
 * @todo      - add integration with Constant Contact
 * @todo      - add integration with Aweber
 * @todo      - notification on site (like Facebook)
 * @todo      - add subscriber management to settings
 * @todo      - add CSV subscriber export
 * @todo      - add list management for Roles, use-case wholsale / retail
 *
 * Developer Notes:
 *
 * Made one minor CSS change to make Chosen work well on WordPress user profiles,
 * this will need to be retested if we upgrade Chosen.
 *
 * Changelog:
 *
 * 0.1.1 - Minor updates, fixed date_format, fix for only one notification getting sent
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
		private $version = '0.1.1';

		/**
		 * @var $options - holds all plugin options
		 */
		public $options;

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

			// add meta box
			if(is_admin()) {
				add_action('subscribr_post_defaults', array($this, 'add_out_out_meta_box'));
			}
		}

		/**
		 * Returns the class name and version.
		 *
		 * @return string
		 */
		public function __toString() {
			return get_class($this).' '.$this->get_version();
		}

		/**
		 * Returns the plugin version number.
		 *
		 * @return string
		 */
		public function get_version() {
			return $this->version;
		}

		/**
		 * Register the plugin text domain for translation
		 *
		 */
		public function load_textdomain() {
			load_plugin_textdomain('subscribr', FALSE, SUBSCRIBR_PLUGIN_SLUG);
		}

		public function add_out_out_meta_box() {

			include_once('views/meta-box.php');
			new opt_out_meta_box($this->options);
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

				$scripts[] = array(
					'handle' => 'subscribr',
					'src'    => SUBSCRIBR_DIR_URL.'js/main.js',
					'deps'   => array('jquery')
				);

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
					function emailSubscribeInit() {
						jQuery('.chosen-select').chosen({
							search_contains:           true,
							width:                     '100%',
							placeholder_text_multiple: '<?php echo apply_filters('subscribr_terms_search_placeholder', sprintf(__('Select or search for %s', 'subscribr'), $this->get_option('notifications_label'))); ?>',
							no_results_text:           '<?php echo apply_filters('subscribr_terms_search_no_results', __('No results', 'subscribr')); ?>'
						});
					}
				</script>
			<?php
			}
		}

		public function do_scripts() {
			// only enqueue if we're on the register screen, user profile, or Theme_My_Login pages (and the options are enabled)
			if(($this->is_register() && $this->get_option('show_on_register')) || ($this->is_profile() && $this->get_option('show_on_profile')) || (class_exists('Theme_My_Login')) || ($this->is_user_edit() && $this->get_option('show_on_profile'))) {
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
			$this->options = get_option(SUBSCRIBR_OPTIONS);
			include_once('controllers/options-init.php');
			new subscribr_options($this->options);
		}

		/**
		 * Displays the custom user fields on the registration and profile screens.
		 *
		 * @param $user
		 */
		public function user_profile_fields($user) {

			// determine what taxonomies are enabled for email notification, if any
			$enabled_taxonomies = $this->get_enabled_taxonomies();

			if(!is_array($enabled_taxonomies)) {
				// no terms are enabled, exit now
				return;
			}

			$subscribed_terms = get_user_meta($user->ID, 'subscribr-terms', TRUE);
			$subscribr_pause = get_user_meta($user->ID, 'subscribr-pause', TRUE);
			$subscribr_unsubscribe = get_user_meta($user->ID, 'subscribr-unsubscribe', TRUE);
			$notifications_label = $this->get_option('notifications_label');

			include_once('views/profile-fields.php');
		}

		/**
		 * @param $user_id
		 *
		 * @return bool
		 */
		public function update_user_meta($user_id) {

			// Check if our nonce is set and valid
			if(!(current_user_can('edit_user', $user_id) || (!isset($_POST['subscribr_update_user_meta_nonce']) || !wp_verify_nonce($_POST['subscribr_update_user_meta_nonce'], 'subscribr_update_user_meta')))) {
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
		public function queue_notifications($post_id) {

			// different WP hooks will send either the post ID or the actual post object, so we need to test for both cases
			if(is_a($post_id, 'WP_Post')) {
				$post_id = $post_id->ID;
			}

			if(!isset($_POST['subscribr_opt_out'])) {
				if(!wp_is_post_revision($post_id)) {

					$post = get_post($post_id);

					// quit if post has been published already
					if($post->post_date != $post->post_modified) {
						return;
					}

					// query users with active notification preferences
					$active_user_ids = new WP_User_Query(
						array(
							 'fields'     => 'id',
							 //'fields' => 'all_with_meta',
							 // check for any subscribed terms
							 'meta_query' => array(
								 array(
									 'key'     => 'subscribr-terms',
									 'value'   => '',
									 'compare' => '!='
								 ),
								 // make sure notifications are not disabled or paused
								 array(
									 'key'     => 'subscribr-pause',
									 'value'   => 1,
									 'compare' => '!='
								 ),
								 array(
									 'key'     => 'subscribr-unsubscribe',
									 'value'   => 1,
									 'compare' => '!='
								 )
							 )
						)
					);

					// grab the terms (as an array instead of an object)
					$post_terms = json_decode(json_encode(wp_get_object_terms($post_id, $this->get_enabled_taxonomies())), TRUE);

					// array to hold matched users
					$notify_user_ids = array();

					// 1. loop through the subscribed users
					foreach($active_user_ids->get_results() as $user_id) {
						$user_id = intval($user_id); // data type correction
						$subscribr_terms = get_user_meta($user_id, 'subscribr-terms', TRUE);
						if(is_array($subscribr_terms)) {

							// 2. loop through the subscribed terms
							foreach($subscribr_terms as $term) {

								// 3. loop through the post terms to test for a match
								foreach($post_terms as $post_term) {
									if($post_term['slug'] == $term) {

										// 4. we've got a match, add the user to the notify array
										$notify_user_ids[] = $user_id;
									}
								}
							}
						}
					}

					// remove duplicates so we don't send mail more than once!
					$notify_user_ids = array_unique($notify_user_ids, SORT_NUMERIC);

					if(!empty($notify_user_ids)) {

						foreach($notify_user_ids as $user_id) {

							do_action('subscribr_pre_user_query', $post, $user_id); // likely the best spot to plugin other types of notifications (SMS, etc)

							// email notifications
							if($this->get_option('enable_mail_notifications')) {

								// test for public post statuses, this allows for custom statuses as well as the default 'publish'
								$post_status = get_post_status_object(get_post_status($post_id));
								if($post_status->public) {
									$this->notification_send($post_id, $user_id);
								}
							}

							// add other notification methods here
							do_action('subscribr_post_user_query');
						}
					} else {

						// no matches
						do_action('subscribr_empty_user_query');
					}
				}
			}
		}

		/**
		 * @param $post_id
		 */
		public function notification_send($post_id, $user_id) {

			// 1. grab the appropriate message template
			$template_files = $this->locate_theme_templates();

			// test for user defined PHP email templates in the 'subscribr' folder in the current theme (or child theme)
			if(locate_template($template_files)) {
				// a custom template was found
				//@todo
			} else {
				// use the default template
				//@todo
			}

			// 2. get users details and send the message
			$user = get_user_by('id', $user_id);
			$to_name = apply_filters('subsribr_to_name', $user->data->display_name);
			$to_email = apply_filters('subscribr_to_email', $user->data->user_email);
			$to = $to_name.' <'.$to_email.'>';

			$from_name = apply_filters('subsribr_from_name', $this->get_option('from_name'));
			$from_email = apply_filters('subscribr_from_email', $this->get_option('from_email'));
			$from = $from_name.' <'.$from_email.'>';

			$mail_subject = $this->get_option('mail_subject');
			$mail_subject = $this->merge_user_vars($mail_subject, $post_id, $user_id);
			$mail_subject = apply_filters('subscribr_mail_subject', $mail_subject);

			$headers[] = 'From: '.$from;
			//$headers[] = 'Content-type: text/html'; @todo

			$message = $this->get_option('email_body');
			$message = $this->merge_user_vars($message, $post_id, $user_id);
			$message = apply_filters('subsribr_email_body', $message);

			wp_mail($to, $mail_subject, $message, $headers);
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

				// 1. loop through user enabled terms
				foreach($enabled_terms as $term) {

					// 2. loop through all taxonomies
					foreach($all_taxonomies as $tax) {

						// 3. check if the term exists in each taxonomy
						$term_result = term_exists($term, $tax);
						if(!empty($term_result) && !is_a($term_result, 'WP_Error')) {

							// 4. if so, add it to our array
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
			$disabled_taxonomies = array('post_status', 'nav_menu', 'post_format', 'link_category');
			$disabled_taxonomies = apply_filters('subscribr_disabled_taxonomies', $disabled_taxonomies);

			$taxonomies = array_diff($taxonomies, $disabled_taxonomies);
			return $taxonomies;
		}

		/**
		 * Replaces certain user and blog variables in $input string.
		 *
		 * Based on code from the Theme My Login plugin.
		 *
		 * @since  0.1
		 * @access public
		 *
		 *
		 * @param string     $input_str          The input string
		 * @param int|string $post_id            The post ID
		 * @param int|string $user_id            User ID to replace user specific variables
		 * @param array      $replacements       Misc variables => values replacements
		 *
		 * @return string The $input string with variables replaced
		 */
		public function merge_user_vars($input_str, $post_id = 0, $user_id = '', $replacements = array()) {
			$defaults = array(
				'%post_title%'          => get_the_title($post_id),
				'%post_date%'           => date(get_option('date_format'), strtotime(get_post($post_id)->post_date)),
				'%post_excerpt%'        => wp_trim_words(get_post($post_id)->post_content, $num_words = 55, $more = NULL),
				'%permalink%'           => get_permalink($post_id),
				'%site_name%'           => get_bloginfo('name'),
				'%site_url%'            => get_home_url(),
				'%notification_label%'  => self::get_option('notification_label'),
				'%notifications_label%' => self::get_option('notifications_label'),
				'%profile_url%'         => admin_url('profile.php'),
				'%user_ip%'             => $_SERVER['REMOTE_ADDR']
			);
			$replacements = wp_parse_args($replacements, $defaults);

			// Get user data
			$user = FALSE;
			if($user_id) {
				$user = get_user_by('id', $user_id);
			}

			// Get all matches ($matches[0] will be '%value%'; $matches[1] will be 'value')
			preg_match_all('/%([a-zA-Z0-9-_]*)%/', $input_str, $matches);

			// Iterate through matches
			foreach($matches[0] as $key => $match) {
				if(!isset($replacements[$match])) {
					if($user && isset($user->{$matches[1][$key]})) {
						// Replacement from WP_User object
						$replacements[$match] = $user->{$matches[1][$key]};
					} else {
						// Replacement from get_bloginfo()
						$replacements[$match] = get_bloginfo($matches[1][$key]);
					}
				}
			}

			// Allow replacements to be filtered
			$replacements = apply_filters('subscribr_replace_vars', $replacements, $user_id);

			if(empty($replacements)) {
				return $input_str;
			}

			// Get search values
			$search = array_keys($replacements);

			// Get replacement values
			$replace = array_values($replacements);

			return str_replace($search, $replace, $input_str);
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
		public function is_user_edit() {
			return in_array($GLOBALS['pagenow'], array('user-edit.php'));
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



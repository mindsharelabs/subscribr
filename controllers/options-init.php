<?php
/**
 * options-init.php
 *
 * @created   9/17/13 3:55 PM
 * @author    Mindshare Studios, Inc.
 * @copyright Copyright (c) 2013
 * @link      http://www.mindsharelabs.com/documentation/
 *
 */

if(!class_exists('subscribr_options')) :
	class subscribr_options extends Subscribr {

		/**
		 * @var        $options - holds all plugin options
		 */
		protected $options;

		public function __construct($options) {

			$this->options = $options;

			// no options have been saved yet, so we'll start with an empty array
			if(!is_array($this->options)) {
				$this->options = array();
			}
			$this->set_options();
			$this->apply_options();
		}

		/**
		 * Setup default options
		 *
		 * Technically this is done by the Mindshare Options Framework, but we want to
		 * make sure we have the correct defaults even if a user never visits the settings
		 * page.
		 */
		public function set_options() {

			$option_changed = FALSE;

			if(!array_key_exists('enable_all_terms', $this->options)) {
				$this->options['enable_all_terms'] = apply_filters('subscribr_default_enable_all_terms', TRUE);
				$option_changed = TRUE;
			}
			if(!array_key_exists('enabled_terms', $this->options)) {
				$this->options['enabled_terms'] = apply_filters('subscribr_default_taxonomies', FALSE);
				$option_changed = TRUE;
			}
			if(!array_key_exists('from_name', $this->options)) {
				$this->options['from_name'] = apply_filters('subscribr_default_from_name', get_bloginfo('name'));
				$option_changed = TRUE;
			}
			if(!array_key_exists('from_email', $this->options)) {
				$this->options['from_email'] = apply_filters('subscribr_default_email', get_option('admin_email'));
				$option_changed = TRUE;
			}

			if($option_changed) {
				$this->save_options();
			}
		}

		/**
		 * Setup default options
		 *
		 * Technically this is done by the Mindshare Options Framework, but we want to
		 * make sure we have the correct defaults even if a user never visits the settings
		 * page.
		 */
		public function apply_options() {

			if($this->get_option('show_on_profile')) {
				// actions to add fields to the user profile, register form and edit profile
				add_action('show_user_profile', array($this, 'user_profile_fields'));
				add_action('edit_user_profile', array($this, 'user_profile_fields'));
				// actions to store updated preferences in the user meta table
				add_action('personal_options_update', array($this, 'update_user_meta'));
				add_action('edit_user_profile_update', array($this, 'update_user_meta'));
			}

			if($this->get_option('show_on_register')) {
				add_action('register_form', array($this, 'user_profile_fields'));
				add_action('user_register', array($this, 'update_user_meta'));
			}
		}

		/**
		 * Saves the options to the DB
		 *
		 */
		public function save_options() {
			update_option(SUBSCRIBR_OPTIONS, $this->options);
		}


	}
endif;

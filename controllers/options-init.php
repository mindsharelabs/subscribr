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

if(!class_exists('es_options')) :
	class es_options {

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
				$this->options['enable_all_terms'] = apply_filters('es_default_enable_all_terms', TRUE);
				$option_changed = TRUE;
			}
			if(!array_key_exists('enabled_terms', $this->options)) {
				$this->options['enabled_terms'] = apply_filters('es_default_taxonomies', FALSE);
				$option_changed = TRUE;
			}
			if(!array_key_exists('from_name', $this->options)) {
				$this->options['from_name'] = apply_filters('es_default_from_name', get_bloginfo('name'));
				$option_changed = TRUE;
			}
			if(!array_key_exists('from_email', $this->options)) {
				$this->options['from_email'] = apply_filters('es_default_email', get_option('admin_email'));
				$option_changed = TRUE;
			}

			if($option_changed) {
				$this->save_options();
			}
		}

		/**
		 * Saves the options to the DB
		 *
		 */
		public function save_options() {
			update_option(ES_OPTIONS, $this->options);
		}
	}
endif;

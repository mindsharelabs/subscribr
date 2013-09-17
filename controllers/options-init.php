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

		public function __construct() {
			$this->options = get_option(ES_OPTIONS);

			// no options have been saved yet, so we'll start with anj empty array
			if(!is_array($this->options)) {
				$this->options =  array();
			}
			$this->set_options();
		}

		/**
		 * Setup default options
		 */
		public function set_options() {

			if(@$this->options['option_name']) {
				// do something
			}
		}
	}
endif;

<?php
/**
 * LLMS Course Landing Page - Loader.
 *
 * @package LCL
 * @since 1.0.0
 */

defined( 'ABSPATH' ) or exit;

if ( ! class_exists( 'LCL_Loader' ) ) {

	/**
	 * Loader Class for LCL
	 */
	class LCL_Loader {

		/**
		 * Member Variable
		 *
		 * @var instance
		 */
		private static $instance = null;

		/**
		 * Initiator
		 */
		public static function get_instance() {

			if ( ! defined( 'LLMS_VERSION' ) ) {
				return false;
			}

			if ( ! isset( self::$instance ) ) {
				self::$instance = new self;
			}

			return self::$instance;
		}

		/**
		 * Constructor
		 */
		private function __construct() {
			$this->includes();
		}

		/**
		 * Include required files.
		 *
		 * @return void
		 */
		private function includes() {

			// Load the metabbox class only in admin.
			require_once LCLP_DIR . 'admin/class-lcl-admin.php';
			require_once LCLP_DIR . 'classes/class-lcl.php';
		}
	}
}

add_action( 'plugins_loaded', 'LCL_Loader::get_instance' );


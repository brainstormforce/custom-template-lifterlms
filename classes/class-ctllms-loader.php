<?php
/**
 * Custom Template for LifterLMS - Loader.
 *
 * @package Custom Template for LifterLMS
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

if ( ! class_exists( 'CTLLMS_Loader' ) ) {

	/**
	 * Loader Class for CTLLMS
	 */
	class CTLLMS_Loader {

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

			if ( ! defined( 'CTLLMS_VER' ) ) {
				return false;
			}

			if ( ! isset( self::$instance ) ) {
				self::$instance = new self();
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
			require_once CTLLMS_DIR . 'admin/class-ctllms-admin.php';
			require_once CTLLMS_DIR . 'classes/class-ctllms.php';
		}
	}
}

add_action( 'plugins_loaded', 'CTLLMS_Loader::get_instance' );


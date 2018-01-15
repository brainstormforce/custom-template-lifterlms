<?php
/**
 * 
 */

defined( 'ABSPATH' ) or exit;

/**
 * Loader Class for LCL
 */
class LCL_Loader {

	private static $_instance = null;

	public static function instance() {

		if ( ! defined( 'LLMS_VERSION' ) ) {
			return false;
		}

		if ( ! isset( self::$_instance ) ) {
			self::$_instance = new self;
		}

		return self::$_instance;
	}

	private function __construct() {
		$this->includes();
	}

	private function includes() {

		// Load the metabbox class only in admin.
		if ( is_admin() ) {
			require_once LCLP_DIR . 'admin/class-lcl-metabox.php';
		}

		require_once LCLP_DIR . 'classes/class-lcl.php';
	}
}

add_action( 'plugins_loaded', 'LCL_Loader::instance' );


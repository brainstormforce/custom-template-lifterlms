<?php
/**
 * 
 */

defined( 'ABSPATH' ) or exit;

/**
 * Loader Class for LCL
 */
class LCL {

	private static $_instance = null;

	public static function instance() {
		if ( ! isset( self::$_instance ) ) {
			self::$_instance = new self;
		}

		return self::$_instance;
	}

	private function __construct() {
		add_action( 'wp', array( $this, 'override_template_include' ), 999 );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 999 );
		add_filter( 'astra_page_layout', array( $this, 'astra_page_layout' ) );
		add_filter( 'astra_get_content_layout', array( $this, 'content_layout' ) );
		add_filter( 'astra_the_title_enabled', array( $this, 'page_title' ) );
	}

	public function astra_page_layout( $sidebar ) {

		if ( self::use_template() ) {
			$sidebar = 'no-sidebar';
		}

		return $sidebar;
	}

	public function content_layout( $layout ) {

		if ( self::use_template() ) {
			$layout = 'page-builder';
		}

		return $layout;
	}

	public function page_title( $status ) {

		if ( self::use_template() ) {
			$status = false;
		}

		return $status;
	}

	public static function use_template() {
		// Don't override the template if the post type is not `course`
		if ( 'course' !== get_post_type() ) {
			return false;
		}
		
		if ( is_user_logged_in() && llms_is_user_enrolled( get_current_user_id(), get_the_id() ) ) {
			return false;
		}

		$template = get_post_meta( get_the_id(), 'course_template', true );
		if ( '' == $template ) {
			return false;
		}

		return true;
	}

	public function enqueue_scripts() {

		// Don't override the template if the post type is not `course`
		if ( 'course' !== get_post_type() ) {
			return false;
		}
		
		if ( is_user_logged_in() && llms_is_user_enrolled( get_current_user_id(), get_the_id() ) ) {
			return false;
		}

		if ( class_exists( '\Elementor\Post_CSS_File' ) ) {

			$template = get_post_meta( get_the_id(), 'course_template', true );

			if ( self::is_elementor_activated( $template ) ) {

				$css_file = new \Elementor\Post_CSS_File( $template );
				$css_file->enqueue();
			}
		}
	}

	public function override_template_include() {

		// Don't override the template if the post type is not `course`
		if ( 'course' !== get_post_type() ) {
			return false;
		}
		
		if ( is_user_logged_in() && llms_is_user_enrolled( get_current_user_id(), get_the_id() ) ) {
			return false;
		}

		add_filter( 'the_content', array( $this, 'render' ) );
	}

	public function render( $content ) {
		$template = get_post_meta( get_the_id(), 'course_template', true );

		if ( self::is_elementor_activated( $template ) ) {
			return Elementor\Plugin::instance()->frontend->get_builder_content_for_display( $template );
		}

		return $content;
	}

	/**
	 * Check is elementor activated.
	 *
	 * @param int $id Post/Page Id.
	 * @return boolean
	 */
	public static function is_elementor_activated( $id ) {

		if ( ! class_exists( '\Elementor\Plugin' ) ) {
			return false;
		}

		if ( version_compare( ELEMENTOR_VERSION, '1.5.0', '<' ) ) {
			return ( 'builder' === Elementor\Plugin::$instance->db->get_edit_mode( $id ) );
		} else {
			return Elementor\Plugin::$instance->db->is_built_with_elementor( $id );
		}

		return false;
	}

}

LCL::instance();


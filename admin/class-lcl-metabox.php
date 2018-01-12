<?php
/**
 * 
 */

defined( 'ABSPATH' ) or exit;

/**
 * Loader Class for LCL
 */
class LCL_Metabbox {

	private static $_instance = null;

	public static function instance() {
		if ( ! isset( self::$_instance ) ) {
			self::$_instance = new self;
		}

		return self::$_instance;
	}

	private function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'lcl_register_metabox' ) );
		add_action( 'save_post', array( $this, 'ehf_save_meta' ) );
	}

	public function lcl_register_metabox() {
		add_meta_box(
			'ehf-meta-box', __( 'Elementor Header Footer options', 'header-footer-elementor' ), array(
				$this,
				'efh_metabox_render',
			), 'course', 'normal', 'high'
		);
	}

	/**
	 * Render Meta field.
	 *
	 * @param  POST $post Currennt post object which is being displayed.
	 */
	function efh_metabox_render( $post ) {
		$values            = get_post_custom( $post->ID );
		$course_template   = isset( $values['course_template'] ) ? esc_attr( $values['course_template'][0] ) : '';

		// We'll use this nonce field later on when saving.
		wp_nonce_field( 'ehf_meta_nounce', 'ehf_meta_nounce' );

		$args = array(
			'selected'         => $course_template,
			'name'             => 'course_template',
			'show_option_none' => "Don't Override the template",
		);

		self::wp_dropdown_pages( $args );

	}

	/**
	 * Save meta field.
	 *
	 * @param  POST $post_id Currennt post object which is being displayed.
	 *
	 * @return Void
	 */
	public function ehf_save_meta( $post_id ) {

		// Bail if we're doing an auto save.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// if our nonce isn't there, or we can't verify it, bail.
		if ( ! isset( $_POST['ehf_meta_nounce'] ) || ! wp_verify_nonce( $_POST['ehf_meta_nounce'], 'ehf_meta_nounce' ) ) {
			return;
		}

		// if our current user can't edit this post, bail.
		if ( ! current_user_can( 'edit_posts' ) ) {
			return;
		}

		if ( isset( $_POST['course_template'] ) ) {
			update_post_meta( $post_id, 'course_template', esc_attr( $_POST['course_template'] ) );
		}

	}

	/**
	 * Generates a dropdown list of WordPress pages and Beaver Builder templates, echos the generated HTMl markup.
	 *
	 * @param  Array $args Parameters for the select field.
	 *
	 *         $args[name] => 'name' of the select field, this will be used as the key to be saved in database.
	 *         $args[selected] => default value of the select field.
	 *         $args[show_option_none] => Value of the option 'none'.
	 */
	public static function wp_dropdown_pages( $args ) {
		$all_posts = array();
		$atts = array(
			'post_type'      => array(
				'fl-builder-template',
				'page',
				'elementor_library'
			),
			'posts_per_page' => 200,
			'cache_results'  => true,
		);
		$query = new WP_Query( $atts );
		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				$title = get_the_title();
				$id    = get_the_id();
				$all_posts[ get_post_type() ][ $id ] = $title;
			}
		}
		echo '<select name="' . $args['name'] . '">';
		echo '<option value="">' . $args['show_option_none'] . '</option>';
		foreach ( $all_posts as $post_type => $posts ) {
			echo '<optgroup label="' . ucwords( str_replace( '-', ' ', $post_type ) ) . '">';
			foreach ( $posts as $id => $post_name ) {
				echo '<option value="' . $id . '" ' . selected( $id, $args['selected'] ) . ' >' . $post_name . '</option>';
			}
			echo '</optgroup>';
		}
		echo '</select>';
	}

}

LCL_Metabbox::instance();


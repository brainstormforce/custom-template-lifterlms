<?php
/**
 * LLMS Course Landing Page - Admin.
 *
 * @package LCL
 * @since 1.0.0
 */

if ( ! class_exists( 'LCL_Admin' ) ) {

	/**
	 * LLMS Course Landing Page Initialization
	 *
	 * @since 1.0.0
	 */
	class LCL_Admin {


		/**
		 * Member Variable
		 *
		 * @var instance
		 */
		private static $instance;

		/**
		 * Initiator
		 */
		public static function get_instance() {
			if ( ! isset( self::$instance ) ) {
				self::$instance = new self;
			}
			return self::$instance;
		}

		/**
		 * Constructor
		 */
		public function __construct() {

			add_action( 'init', array( $this, 'llms_course_mading_page_post_type' ) );
			add_filter( 'post_updated_messages', array( $this, 'custom_post_type_post_update_messages' ) );

			// Actions.
			add_filter( 'fl_builder_post_types', array( $this, 'bb_builder_compatibility' ), 10, 1 );
			add_filter( 'llms_metabox_fields_lifterlms_course_options', array( $this, 'course_settings_fields' ) );
			add_action( 'save_post', array( $this, 'save_course_landing_page' ) );
		}

		/**
		 * Register custom landing page tab with the LLMS Course metabox
		 *
		 * @param    array $fields  existing fields.
		 * @return   array
		 * @since    1.0.0
		 * @version  1.0.0
		 */
		public function course_settings_fields( $fields ) {

			global $post;

			$all_posts = array();

			$atts = array(
				'post_type'      => 'llms-course-landing',
				'posts_per_page' => 500,
				'fields'         => 'ids',
			);

			$posts = new WP_Query( $atts );

			if ( isset( $posts->posts ) ) {
				foreach ( $posts->posts as $key => $id ) {
					$all_posts[] = array(
						'key'   => $id,
						'title' => get_the_title( $id ),
					);
				}
			}

			$selected = get_post_meta( get_the_ID(), 'course_template', true );

			$fields[] = array(
				'title'  => __( 'Landing Page', 'lifterlms-landing' ),
				'fields' => array(
					array(
						'class'    => '',
						'desc'     => __( 'Add landing page for Non-enrolled Student.', 'lifterlms-landing' ),
						'id'       => 'course_template',
						'type'     => 'select',
						'label'    => __( 'Course Landing Page', 'lifterlms-landing' ),
						'value'    => $all_posts,
						'selected' => $selected,
					),
				),
			);

			return $fields;
		}

		/**
		 * Create LLMS Course Landing Page custom post type
		 *
		 * @return void
		 */
		function llms_course_mading_page_post_type() {

			$labels = array(
				'name'          => esc_html_x( 'LLMS Course Landing Pages', 'llms course landing page general name', 'lifterlms-landing' ),
				'singular_name' => esc_html_x( 'LLMS Course Landing Page', 'llms course landing page singular name', 'lifterlms-landing' ),
				'search_items'  => esc_html__( 'Search LLMS Course Landing Pages', 'lifterlms-landing' ),
				'all_items'     => esc_html__( 'All LLMS Course Landing Pages', 'lifterlms-landing' ),
				'edit_item'     => esc_html__( 'Edit LLMS Course Landing Page', 'lifterlms-landing' ),
				'view_item'     => esc_html__( 'View LLMS Course Landing Page', 'lifterlms-landing' ),
				'add_new'       => esc_html__( 'Add New', 'lifterlms-landing' ),
				'update_item'   => esc_html__( 'Update LLMS Course Landing Page', 'lifterlms-landing' ),
				'add_new_item'  => esc_html__( 'Add New', 'lifterlms-landing' ),
				'new_item_name' => esc_html__( 'New LLMS Course Landing Page Name', 'lifterlms-landing' ),
			);
			$args   = array(
				'labels'              => $labels,
				'public'              => true,
				'show_ui'             => true,
				'query_var'           => true,
				'can_export'          => true,
				'show_in_menu'        => true,
				'show_in_admin_bar'   => true,
				'exclude_from_search' => true,
				'supports'            => apply_filters( 'llms_course_landing_supports', array( 'title', 'editor', 'elementor' ) ),
			);

			register_post_type( 'llms-course-landing', apply_filters( 'llms_course_landing_post_type_args', $args ) );
		}

		/**
		 * Add Update messages for any custom post type
		 *
		 * @param array $messages Array of default messages.
		 * @return array
		 */
		function custom_post_type_post_update_messages( $messages ) {

			$custom_post_type = get_post_type( get_the_ID() );

			if ( 'llms-course-landing' == $custom_post_type ) {

				$obj                           = get_post_type_object( $custom_post_type );
				$singular_name                 = $obj->labels->singular_name;
				$messages[ $custom_post_type ] = array(
					0  => '', // Unused. Messages start at index 1.
					/* translators: %s: singular custom post type name */
					1  => sprintf( __( '%s updated.', 'lifterlms-landing' ), $singular_name ),
					/* translators: %s: singular custom post type name */
					2  => sprintf( __( 'Custom %s updated.', 'lifterlms-landing' ), $singular_name ),
					/* translators: %s: singular custom post type name */
					3  => sprintf( __( 'Custom %s deleted.', 'lifterlms-landing' ), $singular_name ),
					/* translators: %s: singular custom post type name */
					4  => sprintf( __( '%s updated.', 'lifterlms-landing' ), $singular_name ),
					/* translators: %1$s: singular custom post type name ,%2$s: date and time of the revision */
					5  => isset( $_GET['revision'] ) ? sprintf( __( '%1$s restored to revision from %2$s', 'lifterlms-landing' ), $singular_name, wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
					/* translators: %s: singular custom post type name */
					6  => sprintf( __( '%s published.', 'lifterlms-landing' ), $singular_name ),
					/* translators: %s: singular custom post type name */
					7  => sprintf( __( '%s saved.', 'lifterlms-landing' ), $singular_name ),
					/* translators: %s: singular custom post type name */
					8  => sprintf( __( '%s submitted.', 'lifterlms-landing' ), $singular_name ),
					/* translators: %s: singular custom post type name */
					9  => sprintf( __( '%s scheduled for.', 'lifterlms-landing' ), $singular_name ),
					/* translators: %s: singular custom post type name */
					10 => sprintf( __( '%s draft updated.', 'lifterlms-landing' ), $singular_name ),
				);
			}

			return $messages;
		}

		/**
		 * Add page builder support to Advanced hook.
		 *
		 * @param array $value Array of post types.
		 * @return array
		 */
		function bb_builder_compatibility( $value ) {

			$value[] = 'llms-course-landing';

			return $value;
		}

		/**
		 * Save Course Landing Page Id.
		 *
		 * @return void
		 */
		public function save_course_landing_page() {

			$landing_page_id = ( isset( $_POST['course_template'] ) ) ? $_POST['course_template'] : '';

			if ( isset( $_POST['post_ID'] ) ) {
				update_post_meta( $_POST['post_ID'], 'course_template', $landing_page_id );
			}
		}
	}
} // End if().

/**
 *  Kicking this off by calling 'get_instance()' method
 */
LCL_Admin::get_instance();

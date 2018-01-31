<?php
/**
 * Custom Template for LifterLMS Courses - Admin.
 *
 * @package Custom Template for LifterLMS Courses
 * @since 1.0.0
 */

if ( ! class_exists( 'CTLLMS_Admin' ) ) {

	/**
	 * Course Custom Template Initialization
	 *
	 * @since 1.0.0
	 */
	class CTLLMS_Admin {


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

			add_action( 'init', array( $this, 'llms_course_landing_page_post_type' ) );
			add_filter( 'post_updated_messages', array( $this, 'custom_post_type_post_update_messages' ) );

			add_action( 'admin_menu', array( $this, 'display_admin_menu' ) );
			add_action( 'parent_file', array( $this, 'active_admin_menu' ) );

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
				'post_type'      => 'bsf-custom-template',
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

			$selected    = get_post_meta( get_the_ID(), 'course_template', true );
			$description = sprintf(
				/* translators: 1: anchor start, 2: anchor close */
				__( 'The selected custom template will replace default LifterLMS course template for non-enrolled students. <br> If you have not done already, add new custom templates from %1$shere%2$s.', 'custom-template-lifterlms' ),
				'<a href="' . admin_url( 'post-new.php?post_type=bsf-custom-template' ) . '">',
				'</a>'
			);

			$fields[] = array(
				'title'  => __( 'Custom Template', 'custom-template-lifterlms' ),
				'fields' => array(
					array(
						'class'    => '',
						'desc'     => $description,
						'id'       => 'course_template',
						'type'     => 'select',
						'label'    => __( 'Select Custom Template for this course', 'custom-template-lifterlms' ),
						'value'    => $all_posts,
						'selected' => $selected,
					),
				),
			);

			return $fields;
		}

		/**
		 * Admin Menu.
		 *
		 * @return void
		 */
		public function display_admin_menu() {

			add_submenu_page(
				'edit.php?post_type=course',
				__( 'Custom Templates', 'custom-template-lifterlms' ),
				__( 'Custom Templates', 'custom-template-lifterlms' ),
				'manage_lifterlms',
				'edit.php?post_type=bsf-custom-template'
			);
		}

		/**
		 * Set Active Admin menu
		 *
		 * @return string
		 */
		public function active_admin_menu() {

			global $parent_file, $current_screen, $submenu_file, $pagenow;

			if ( ( 'post-new.php' == $pagenow || 'post.php' == $pagenow ) && 'bsf-custom-template' == $current_screen->post_type ) :
				$submenu_file = 'edit.php?post_type=bsf-custom-template';
				$parent_file  = 'edit.php?post_type=course';
			endif;

			return $parent_file;
		}

		/**
		 * Create Course Custom Template custom post type
		 *
		 * @return void
		 */
		function llms_course_landing_page_post_type() {

			$labels = array(
				'name'          => esc_html_x( 'Course Custom Templates', 'llms course landing page general name', 'custom-template-lifterlms' ),
				'singular_name' => esc_html_x( 'Course Custom Template', 'llms course landing page singular name', 'custom-template-lifterlms' ),
				'search_items'  => esc_html__( 'Search Course Custom Templates', 'custom-template-lifterlms' ),
				'all_items'     => esc_html__( 'All Course Custom Templates', 'custom-template-lifterlms' ),
				'edit_item'     => esc_html__( 'Edit Course Custom Template', 'custom-template-lifterlms' ),
				'view_item'     => esc_html__( 'View Course Custom Template', 'custom-template-lifterlms' ),
				'add_new'       => esc_html__( 'Add New', 'custom-template-lifterlms' ),
				'update_item'   => esc_html__( 'Update Course Custom Template', 'custom-template-lifterlms' ),
				'add_new_item'  => esc_html__( 'Add New', 'custom-template-lifterlms' ),
				'new_item_name' => esc_html__( 'New Course Custom Template Name', 'custom-template-lifterlms' ),
			);
			$args   = array(
				'labels'              => $labels,
				'public'              => true,
				'show_ui'             => true,
				'query_var'           => true,
				'can_export'          => true,
				'show_in_menu'        => false,
				'show_in_admin_bar'   => true,
				'exclude_from_search' => true,
				'supports'            => apply_filters( 'llms_course_landing_supports', array( 'title', 'editor', 'elementor' ) ),
			);

			register_post_type( 'bsf-custom-template', apply_filters( 'llms_course_landing_post_type_args', $args ) );
		}

		/**
		 * Add Update messages for any custom post type
		 *
		 * @param array $messages Array of default messages.
		 * @return array
		 */
		function custom_post_type_post_update_messages( $messages ) {

			$custom_post_type = get_post_type( get_the_ID() );

			if ( 'bsf-custom-template' == $custom_post_type ) {

				$obj                           = get_post_type_object( $custom_post_type );
				$singular_name                 = $obj->labels->singular_name;
				$messages[ $custom_post_type ] = array(
					0  => '', // Unused. Messages start at index 1.
					/* translators: %s: singular custom post type name */
					1  => sprintf( __( '%s updated.', 'custom-template-lifterlms' ), $singular_name ),
					/* translators: %s: singular custom post type name */
					2  => sprintf( __( 'Custom %s updated.', 'custom-template-lifterlms' ), $singular_name ),
					/* translators: %s: singular custom post type name */
					3  => sprintf( __( 'Custom %s deleted.', 'custom-template-lifterlms' ), $singular_name ),
					/* translators: %s: singular custom post type name */
					4  => sprintf( __( '%s updated.', 'custom-template-lifterlms' ), $singular_name ),
					/* translators: %1$s: singular custom post type name ,%2$s: date and time of the revision */
					5  => isset( $_GET['revision'] ) ? sprintf( __( '%1$s restored to revision from %2$s', 'custom-template-lifterlms' ), $singular_name, wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
					/* translators: %s: singular custom post type name */
					6  => sprintf( __( '%s published.', 'custom-template-lifterlms' ), $singular_name ),
					/* translators: %s: singular custom post type name */
					7  => sprintf( __( '%s saved.', 'custom-template-lifterlms' ), $singular_name ),
					/* translators: %s: singular custom post type name */
					8  => sprintf( __( '%s submitted.', 'custom-template-lifterlms' ), $singular_name ),
					/* translators: %s: singular custom post type name */
					9  => sprintf( __( '%s scheduled for.', 'custom-template-lifterlms' ), $singular_name ),
					/* translators: %s: singular custom post type name */
					10 => sprintf( __( '%s draft updated.', 'custom-template-lifterlms' ), $singular_name ),
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

			$value[] = 'bsf-custom-template';

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
CTLLMS_Admin::get_instance();

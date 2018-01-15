<?php
/**
 * Plugin Name:     Lifterlms Course Lading Page
 * Plugin URI:      PLUGIN SITE HERE
 * Description:     PLUGIN DESCRIPTION HERE
 * Author:          YOUR NAME HERE
 * Author URI:      YOUR SITE HERE
 * Text Domain:     lifterlms-course-lading-page
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         Lifterlms_Course_Lading_Page
 */

define( 'LCLP_VER', '1.0.8' );
define( 'LCLP_DIR', plugin_dir_path( __FILE__ ) );
define( 'LCLP_URL', plugins_url( '/', __FILE__ ) );
define( 'LCLP_PATH', plugin_basename( __FILE__ ) );

require_once LCLP_DIR . 'classes/class-lcl-loader.php';
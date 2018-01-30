<?php
/**
 * Plugin Name:     Custom Template for LifterLMS Courses
 * Plugin URI:      PLUGIN SITE HERE
 * Description:     PLUGIN DESCRIPTION HERE
 * Author:          YOUR NAME HERE
 * Author URI:      YOUR SITE HERE
 * Text Domain:     custom-template-lifterlms
 * Domain Path:     /languages
 * Version:         1.0.0
 *
 * @package         Custom Template for LifterLMS Courses
 */

define( 'LCLP_VER', '1.0.0' );
define( 'LCLP_DIR', plugin_dir_path( __FILE__ ) );
define( 'LCLP_URL', plugins_url( '/', __FILE__ ) );
define( 'LCLP_PATH', plugin_basename( __FILE__ ) );

require_once LCLP_DIR . 'classes/class-lcl-loader.php';

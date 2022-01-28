<?php
/**
 * Plugin Name:     Custom Template for LifterLMS
 * Plugin URI:      https://github.com/brainstormforce/custom-template-lifterlms
 * Description:     This plugin will help you replace default LifterLMS course template for non-enrolled students with a custom template. You can design the custom template with any page builder of your choice.
 * Author:          brainstormforce
 * Author URI:      https://www.brainstormforce.com/
 * Text Domain:     custom-template-lifterlms
 * Domain Path:     /languages
 * Version:         1.0.4
 *
 * @package         Custom Template for LifterLMS
 */

define( 'CTLLMS_VER', '1.0.4' );
define( 'CTLLMS_FILE', __FILE__ );
define( 'CTLLMS_DIR', plugin_dir_path( __FILE__ ) );
define( 'CTLLMS_URL', plugins_url( '/', __FILE__ ) );
define( 'CTLLMS_PATH', plugin_basename( __FILE__ ) );

require_once CTLLMS_DIR . 'classes/class-ctllms-loader.php';

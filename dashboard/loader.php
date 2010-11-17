<?php
/**
 * BP Tasty theme dashboard loader
 *
 * @author Marshall Sorenson <marshall.sorenson@gmail.com>
 * @link http://bp-tricks.com/
 * @copyright Copyright &copy; 2010 Marshall Sorenson
 * @license http://www.gnu.org/licenses/gpl.html GPLv2 or later
 * @package dashboard
 * @subpackage loader
 * @since 1.0
 */

define( 'BP_TASTY_ADMIN_TPLS_DIR', BP_TASTY_ADMIN_DIR . '/templates' );

//
// Files
//
require_once( BP_TASTY_ADMIN_DIR . '/menu.php' );
require_once( BP_TASTY_ADMIN_DIR . '/cpanel.php' );

//
// Actions
//
add_action( 'admin_menu', 'bp_tasty_dashboard_setup' );
add_action( 'admin_menu', 'bp_tasty_dashboard_setup_menu' );
add_action( 'bp_tasty_dashboard_cpanel_content', 'bp_tasty_dashboard_cpanel_options_content' );

//
// Functions
//

/**
 * Handle setup of the control panel environment
 */
function bp_tasty_dashboard_setup()
{
	// pie options renderer setup
	BP_Tasty_Options_Renderer::setup( BP_TASTY_PIE_URL );

	// enqueue styles
    wp_enqueue_style( 'bp-tasty-dashboard', BP_TASTY_ADMIN_URL . '/assets/css/cpanel.css', false, BP_TASTY_VERSION, 'screen' );

	// enqueue script
	wp_enqueue_script( 'bp-tasty-dashboard', BP_TASTY_ADMIN_URL . '/assets/js/dashboard.js', false, BP_TASTY_VERSION );
}

/**
 * Load a dashboard template relative to the template dir root
 *
 * @param string $rel_path
 */
function bp_tasty_dashboard_load_template( $rel_path )
{
	include( BP_TASTY_ADMIN_TPLS_DIR . DIRECTORY_SEPARATOR . $rel_path );
}

/**
 * Return path to a dashboard image
 */
function bp_tasty_dashboard_image( $name )
{
	return BP_TASTY_ADMIN_URL . '/assets/images/' . $name;
}

?>

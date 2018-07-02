<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://wordpressguru.net
 * @since             1.0.0
 * @package           Code_Assessment
 *
 * @wordpress-plugin
 * Plugin Name:       Code Assessment
 * Plugin URI:        http://wordpressguru.net
 * Description:       Develop a plugin that utilizes the WordPress REST API to pull a list of most recently published posts from 3 to 5 other WordPress sites and display that list as a Dashboard widget in the WordPress admin..
 * Version:           1.0.0
 * Author:            Carlos Reyes
 * Author URI:        http://wordpressguru.net
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       code-assessment
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'Code_Assessment', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-code-assessment-activator.php
 */
function activate_code_assessment() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-code-assessment-activator.php';
	Code_Assessment_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-code-assessment-deactivator.php
 */
function deactivate_code_assessment() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-code-assessment-deactivator.php';
	Code_Assessment_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_code_assessment' );
register_deactivation_hook( __FILE__, 'deactivate_code_assessment' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-code-assessment.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_code_assessment() {

	$plugin = new Code_Assessment();
	$plugin->run();

}
run_code_assessment();

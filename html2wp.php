<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @since             1.0.0
 * @package           HTML2WP
 *
 * @wordpress-plugin
 * Plugin Name:       HTML2WP
 * Description:       Import your HTML files into WP posts or pages. 
 * Version:           1.0.0
 * Author:            Ars
 * Text Domain:       html2wp
 * Domain Path:       /languages
 * License:           GPL-2.0+
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Update it as you release new versions.
 */
define( 'HTML2WP_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-html2wp-activator.php
 */
function activate_html2wp() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-html2wp-activator.php';
	HTML2WP_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-html2wp-deactivator.php
 */
function deactivate_html2wp() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-html2wp-deactivator.php';
	HTML2WP_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_html2wp' );
register_deactivation_hook( __FILE__, 'deactivate_html2wp' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-html2wp.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_html2wp() {

	$plugin = new HTML2WP();
	$plugin->run();

}
run_html2wp();

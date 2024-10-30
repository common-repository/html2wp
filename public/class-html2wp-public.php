<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @since      1.0.0
 *
 * @package    HTML2WP
 * @subpackage HTML2WP/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    HTML2WP
 * @subpackage HTML2WP/public
 * @author     Ars
 */
class HTML2WP_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $html2wp    The ID of this plugin.
	 */
	private $html2wp;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $html2wp       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $html2wp, $version ) {

		$this->html2wp = $html2wp;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in HTML2WP_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The HTML2WP_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->html2wp, plugin_dir_url( __FILE__ ) . 'css/html2wp-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in HTML2WP_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The HTML2WP_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->html2wp, plugin_dir_url( __FILE__ ) . 'js/html2wp-public.js', array( 'jquery' ), $this->version, false );

	}

}

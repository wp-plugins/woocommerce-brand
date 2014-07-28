<?php
/*
Plugin Name: Woocomerce Brands
Plugin URI: http://proword.net/Woocommerce_Brands/
Description: Woocommerce Brands Plugin. After Install and active this plugin you'll have some shortcode and some widget for display your brands in fornt-end website.
Author: Proword
Version: 0.1
Author URI: http://proword.net/
 */

/**
 * Check if WooCommerce is active
 **/
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
	
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

final class woo_brands{

	 public function __construct() {

		$this->includes();
		add_action( 'widgets_init', array( $this, 'include_widgets' ) );
		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'action_links' ) );		
		add_action( 'wp_enqueue_scripts' , array( $this, 'eb_add_scripts' ) );
		register_activation_hook( __FILE__ , array( $this,'woo_brands_install' ));
	 }

	public function woo_brands_install() {
		update_option( 'pw_woocommerce_brands_text_single','yes');
	}

	private function includes() {
		include_once( 'calsses/taxonomies.php' );
		include_once( 'calsses/setting-tabs.php' );
		include_once( 'calsses/class-wc-brands.php' );
	}
	public function include_widgets() {
		include_once( 'calsses/widget.php' );
	}

	public function action_links( $links ) {
		return array_merge( array(
			'<a href="' . admin_url( 'admin.php?page=wc-settings&tab=pw_woocommerce_brands' ) . '">' . __( 'Settings', 'woocommerce-brands' ) . '</a>',
			'<a href="' . esc_url( apply_filters( 'woocommerce_docs_url', 'http://proword.net/Woocommerce_Brands/documentation/', 'woocommerce' ) ) . '">' . __( 'Docs', 'woocommerce-brands' ) . '</a>',

		), $links );
	}
	
	public function eb_add_scripts(){
			
		wp_register_style('woob-front-end-style', WP_PLUGIN_URL.'/woo-brands/css/front-style.css');
		wp_enqueue_style('woob-front-end-style');
		/* Dropdown css */
		wp_register_style('woob-dropdown-style',  WP_PLUGIN_URL.'/woo-brands/css/msdropdown/dd.css');
		wp_enqueue_style('woob-dropdown-style');
		
		/* Drop Down Js */
		wp_register_script('woob-dropdown-script', WP_PLUGIN_URL.'/woo-brands/js/msdropdown/jquery.dd.js',array( 'jquery' ));

	}	
}
new woo_brands();
}
?>

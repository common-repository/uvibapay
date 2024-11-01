<?php 


/*

Plugin Name: UvibaPay
Plugin URI:  pay.uviba.com
Description: Accept Payments Online via pay.uviba.com
Author: Uviba LLC
version: 1.3.6

*/
defined('ABSPATH') or die('You don\'t have permission to access this file');

if(!function_exists('add_action')){
	die('You don\'t have permission to access this file because add_action does not exist');
}
// Make sure WooCommerce is active
if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) return;
define('woo_UvibaPayments_IMG', WP_PLUGIN_URL . "/" . plugin_basename(dirname(__FILE__)) . '/public/images/');

define("customwordpresspayplugin_plugin_folder", "UvibaPay");

add_action( 'plugins_loaded', 'init_WC_Gateway_CustomWPressPlugin',0 );

function add_your_gateway_class( $methods ) {
    $methods[] = 'WC_Gateway_CustomWPressPlugin'; 
    return $methods;
}

add_filter( 'woocommerce_payment_gateways', 'add_your_gateway_class' );



add_action('admin_menu','woocommerce_customwordpresspayplugin_addMenu');
function woocommerce_customwordpresspayplugin_addMenu(){
	//add_menu_page(page_title,menu_title,capability,'url');
	add_menu_page("Uviba Payment",'Uviba Payment',4,"settings","UvibaPayMenu");

}
function UvibaPayMenu(){
	
header("Location:".'admin.php?page=wc-settings&tab=checkout&section=customwordpresspayplugin');
}

function init_WC_Gateway_CustomWPressPlugin(){
		if ( !class_exists( 'WC_Payment_Gateway' ) ) return;

	    include_once __DIR__.'/classes/WC_Gateway_CustomWPressPlugin.php';

}

/**
* 'Settings' link on plugin page
**/
add_filter( 'plugin_action_links', 'customwordpresspayplugin_add_action_plugin', 10, 5 );
function customwordpresspayplugin_add_action_plugin( $actions, $plugin_file ) {
	static $plugin;

	if (!isset($plugin))
		$plugin = plugin_basename(__FILE__);
	if ($plugin == $plugin_file) {

			$settings = array('settings' => '<a href="admin.php?page=wc-settings&tab=checkout&section=customwordpresspayplugin">' . __('Settings') . '</a>');
		
    			$actions = array_merge($settings, $actions);
			
		}
		
		return $actions;
}//END-settings_add_action_link

function woocommerce_add_gateway_customwordpresspayplugin_gateway($methods) {
		$methods[] = 'WC_Gateway_CustomWPressPlugin';
		return $methods;
	}//END-wc_add_gateway
	
	add_filter('woocommerce_payment_gateways', 'woocommerce_add_gateway_customwordpresspayplugin_gateway' );


	function add_filter_once( $tag, $function_to_add, $priority = 10, $accepted_args = 1 ) {
	global $_gambitFiltersRan;

	if ( ! isset( $_gambitFiltersRan ) ) {
		$_gambitFiltersRan = array();
	}

	// Since references to $this produces a unique id, just use the class for identification purposes
	$idxFunc = $function_to_add;
	if ( is_array( $function_to_add ) ) {
		$idxFunc[0] = get_class( $function_to_add[0] );
	}
	$idx = _wp_filter_build_unique_id( $tag, $idxFunc, $priority );

	if ( ! in_array( $idx, $_gambitFiltersRan ) ) {
		add_filter( $tag, $function_to_add, $priority, $accepted_args );
	}

	$_gambitFiltersRan[] = $idx;

	return true;
}
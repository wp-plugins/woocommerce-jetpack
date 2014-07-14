<?php
/*
Plugin Name: WooCommerce Jetpack
Plugin URI: http://woojetpack.com
Description: Supercharge your WooCommerce site with these awesome powerful features.
Version: 1.0.6
Author: Algoritmika Ltd.
Author URI: http://www.algoritmika.com
Copyright: © 2014 Algoritmika Ltd.
License: GNU General Public License v3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) return; // Check if WooCommerce is active

if ( ! class_exists( 'WC_Jetpack' ) ) :

/**
 * Main WC_Jetpack Class
 *
 * @class WC_Jetpack
 */
final class WC_Jetpack {

	/**
	 * @var WC_Jetpack The single instance of the class
	 */
	protected static $_instance = null;

	/**
	 * Main WC_Jetpack Instance
	 *
	 * Ensures only one instance of WC_Jetpack is loaded or can be loaded.
	 *
	 * @static
	 * @see WCJ()
	 * @return WC_Jetpack - Main instance
	 */
	public static function instance() {
	
		if ( is_null( self::$_instance ) ) {
		
			self::$_instance = new self();
		}
		
		return self::$_instance;
	}

	/**
	 * Cloning is forbidden.
	 *
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'woocommerce' ), '3.9.1' );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'woocommerce' ), '3.9.1' );
	}	

	/**
	 * WC_Jetpack Constructor.
	 * @access public
	 */		
	public function __construct() {	
		
		// Include required files
		$this->includes();
		
		// HOOKS
		//register_activation_hook( __FILE__, array( $this, 'install' ) );
		//add_action( 'admin_init', array( $this, 'install' ) );
		add_action( 'init', array( $this, 'init' ), 0 );
		
		add_filter( 'wcjpc_filter', array( $this, 'wcjpc' ), 100 );
		
		// Settings	
		if ( is_admin() ) { 		
		
			add_filter( 'woocommerce_get_settings_pages', array( $this, 'add_wcj_settings_tab' ) );
			add_filter( 'get_wc_jetpack_plus_message', array( $this, 'display_get_wcj_plus_message' ), 100, 2 );
		}
		
		// Loaded action
		do_action( 'wcj_loaded' );
	}
	
	/**
	 * wcjpc.
	 */	
	public function wcjpc( $value ) {
	
		return $value;
	}
	
	public function display_get_wcj_plus_message( $value, $message_type ) {
	
		switch ( $message_type ) {
		
			case 'global':
				return	'<div class="updated">
								<p class="main"><strong>' . __( 'Install WooCommerce Jetpack Plus to unlock all features', 'woocommerce-jetpack' ) . '</strong></p>
								<span>' . sprintf( __('Some settings fields are locked and you will need %s to modify all locked fields.', 'woocommerce-jetpack'), '<a href="http://woojetpack.com/plus/">WooCommerce Jetpack Plus</a>' ) . '</span>					
								<p><a href="http://woojetpack.com/plus/" target="_blank" class="button button-primary">' . sprintf( __( 'Buy now just for %s', 'woocommerce-jetpack' ), '$9' ). '</a> <a href="http://woojetpack.com" target="_blank" class="button">'. sprintf( __( 'Visit %s', 'woocommerce-jetpack' ), 'woojetpack.com' ) . '</a></p>
						</div>';
		
			case 'desc':
				return __( 'Get <a href="http://woojetpack.com/plus/" target="_blank">WooCommerce Jetpack Plus</a> to change value.', 'woocommerce-jetpack' );
				
			case 'desc_below':
				return __( 'Get <a href="http://woojetpack.com/plus/" target="_blank">WooCommerce Jetpack Plus</a> to change values below.', 'woocommerce-jetpack' );				
				
			case 'desc_no_link':
				return __( 'Get WooCommerce Jetpack Plus to change value.', 'woocommerce-jetpack' );

			case 'readonly':
				return array( 'readonly' => 'readonly' );
				
			case 'disabled':
				return array( 'disabled' => 'disabled' );
				
			case 'readonly_string':
				return 'readonly';
				
			case 'disabled_string':
				return 'disabled';
		}
		
		return $value;
	}
	
	/**
	 * Add options.
	 *
	function install() {
	
		$settings = array();
		$settings[] = include( 'includes/class-wcj-currencies.php' );
		$settings[] = include( 'includes/class-wcj-price-labels.php' );		
		$settings[] = include( 'includes/class-wcj-sorting.php' );
		$settings[] = include( 'includes/class-wcj-product-info.php' );		
		
		foreach ( $settings as $section ) {
			foreach ( $section->get_settings() as $value ) {
				if ( isset( $value['default'] ) && isset( $value['id'] ) ) {
					$autoload = isset( $value['autoload'] ) ? (bool) $value['autoload'] : true;
					add_option( $value['id'], $value['default'], '', ( $autoload ? 'yes' : 'no' ) );
				}
			}
		}
	}

	/**
	 * Include required core files used in admin and on the frontend.
	 */
	private function includes() {
	
		$settings = array();
	
		$settings[] = include_once( 'includes/class-wcj-price-labels.php' );
		$settings[] = include_once( 'includes/class-wcj-call-for-price.php' );
		$settings[] = include_once( 'includes/class-wcj-currencies.php' );		
		$settings[] = include_once( 'includes/class-wcj-sorting.php' );
		$settings[] = include_once( 'includes/class-wcj-old-slugs.php' );
		$settings[] = include_once( 'includes/class-wcj-add-to-cart.php' );
		$settings[] = include_once( 'includes/class-wcj-order-numbers.php' );
		$settings[] = include_once( 'includes/class-wcj-pdf-invoices.php' );
		//$settings[] = include_once( 'includes/class-wcj-product-info.php' );
		
		// Add options
		if ( is_admin() ) {
			foreach ( $settings as $section ) {
				foreach ( $section->get_settings() as $value ) {
					if ( isset( $value['default'] ) && isset( $value['id'] ) ) {
						$autoload = isset( $value['autoload'] ) ? (bool) $value['autoload'] : true;
						add_option( $value['id'], $value['default'], '', ( $autoload ? 'yes' : 'no' ) );
					}
				}
			}		
		}
	}

	/**
	 * Add Jetpack settings tab to WooCommerce settings.
	 */	
	public function add_wcj_settings_tab( $settings ) {
	
		$settings[] = include( 'includes/admin/settings/class-wc-settings-jetpack.php' );
		
		return $settings;
	}


	/**
	 * Init WC_Jetpack when WordPress initialises.
	 */
	public function init() {
	
		// Before init action
		do_action( 'before_wcj_init' );
		
		// Set up localisation
		//$this->load_plugin_textdomain();
		load_plugin_textdomain( 'woocommerce-jetpack',  false, dirname( plugin_basename( __FILE__ ) ) . '/langs/' );
				
		// Init action
		do_action( 'wcj_init' );
	}
}

endif;

/**
 * Returns the main instance of WC_Jetpack to prevent the need to use globals.
 *
 * @return WC_Jetpack
 */
function WCJ() {

	return WC_Jetpack::instance();
}

WCJ();

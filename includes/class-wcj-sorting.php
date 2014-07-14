<?php
/**
 * WooCommerce Jetpack Sorting
 *
 * The WooCommerce Jetpack Sorting class.
 *
 * @class 		WCJ_Sorting
 * @version		1.0.0
 * @category	Class
 * @author 		Algoritmika Ltd.
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'WCJ_Sorting' ) ) :

class WCJ_Sorting {
	
	/**
	 * WCJ_Sorting Constructor.
	 * @access public
	 */
	public function __construct() {
	
		// HOOKS
		
		// Main hooks
		if ( get_option( 'wcj_sorting_enabled' ) == 'yes' ) {
		
			add_filter( 'woocommerce_get_catalog_ordering_args', array( $this, 'custom_woocommerce_get_catalog_ordering_args' ), 100 ); // Sorting
			add_filter( 'woocommerce_catalog_orderby', array( $this, 'custom_woocommerce_catalog_orderby' ), 100 ); // Front end
			add_filter( 'woocommerce_default_catalog_orderby_options', array( $this, 'custom_woocommerce_catalog_orderby' ), 100 ); // Back end (default sorting)
			add_action( 'init', array( $this, 'custom_init' ), 100 ); // Remove sorting
		}
		
		// Settings hooks
		add_filter( 'wcj_settings_sections', array( $this, 'settings_section' ) ); // Add section to WooCommerce > Settings > Jetpack
		add_filter( 'wcj_settings_sorting', array( $this, 'get_settings' ), 100 ); // Add the settings
		if ( get_option( 'wcj_sorting_enabled' ) == 'yes' ) 
			add_filter( 'woocommerce_product_settings', array( $this, 'add_remove_sorting_checkbox' ), 100 ); // Add 'Remove All Sorting' checkbox to WooCommerce > Settings > Products
		add_filter( 'wcj_features_status', array( $this, 'add_enabled_option' ), 100 );	// Add Enable option to Jetpack Settings Dashboard
	}

	/**
	 * add_enabled_option.
	 */
	public function add_enabled_option( $settings ) {
	
		$all_settings = $this->get_settings();
		$settings[] = $all_settings[1];
		
		return $settings;
	}	
	
	/*
	 * Add Remove All Sorting checkbox to WooCommerce > Settings > Products.
	 */
	function add_remove_sorting_checkbox( $settings ) {

		$updated_settings = array();

		foreach ( $settings as $section ) {	  
			
			if ( isset( $section['id'] ) && 'woocommerce_cart_redirect_after_add' == $section['id'] ) {

				$updated_settings[] = array(
					'title' 	=> __( 'Remove All Sorting', 'woocommerce-jetpack' ),				
					'desc_tip'	=> apply_filters( 'get_wc_jetpack_plus_message', '', 'desc' ),
					'id'		=> 'wcj_sorting_remove_all_enabled',
					'type'		=> 'checkbox',
					'default'	=> 'no',
					'desc'		=> __( 'Completely remove sorting from the shop front end', 'woocommerce-jetpack' ),
					'custom_attributes'	=> apply_filters( 'get_wc_jetpack_plus_message', '', 'disabled' ),
				);
			}
			
			$updated_settings[] = $section;
		}
	  
		return $updated_settings;
	}
	
	/*
	 * Custom Init - remove all sorting action
	 */	
	function custom_init() {
	
		if ( get_option( 'wcj_sorting_remove_all_enabled' ) ) 
			do_action( 'wcj_sorting_remove_action' );
	}		
	
	/*
	 * Add new sorting options to Front End and to Back End (in WooCommerce > Settings > Products > Default Product Sorting).
	 */
	function custom_woocommerce_catalog_orderby( $sortby ) {
		
		if ( get_option( 'wcj_sorting_by_name_asc_enabled' ) == 'yes' )
			$sortby['title_asc'] = get_option( 'wcj_sorting_by_name_asc_text' );
			
		if ( get_option( 'wcj_sorting_by_name_desc_enabled' ) == 'yes' )
			$sortby['title_desc'] = get_option( 'wcj_sorting_by_name_desc_text' );

		if ( get_option( 'wcj_sorting_by_sku_asc_enabled' ) == 'yes' )
			$sortby['sku_asc'] = get_option( 'wcj_sorting_by_sku_asc_text' );
			
		if ( 'yes' == get_option( 'wcj_sorting_by_sku_desc_enabled' ) )
			$sortby['sku_desc'] = get_option( 'wcj_sorting_by_sku_desc_text' );
			
		return $sortby;
	}
	
	/*
	 * Add new sorting options to WooCommerce sorting.
	 */
	function custom_woocommerce_get_catalog_ordering_args( $args ) {
	
		global $woocommerce;
		// Get ordering from query string unless defined
		$orderby_value = isset( $_GET['orderby'] ) ? woocommerce_clean( $_GET['orderby'] ) : apply_filters( 'woocommerce_default_catalog_orderby', get_option( 'woocommerce_default_catalog_orderby' ) );
		// Get order + orderby args from string
		$orderby_value = explode( '-', $orderby_value );
		$orderby       = esc_attr( $orderby_value[0] );

		switch ( $orderby ) :
			case 'title_asc' :
				$args['orderby'] = 'title';
				$args['order'] = 'asc';
				$args['meta_key'] = '';
			break;			
			case 'title_desc' :
				$args['orderby'] = 'title';
				$args['order'] = 'desc';
				$args['meta_key'] = '';
			break;
			case 'sku_asc' :
				$args['orderby'] = 'meta_value';
				$args['order'] = 'asc';
				$args['meta_key'] = '_sku';
			break;			
			case 'sku_desc' :
				$args['orderby'] = 'meta_value';
				$args['order'] = 'desc';
				$args['meta_key'] = '_sku';
			break;
		endswitch;
			
		return $args;				
	}	
	
	/*
	 * Add the settings.
	 */
	function get_settings() {

		$settings = array(
		
			array( 'title' 	=> __( 'Sorting Options', 'woocommerce-jetpack' ), 'type' => 'title', 'desc' => '', 'id' => 'wcj_sorting_options' ),
			
			array(
				'title' 	=> __( 'Sorting', 'woocommerce-jetpack' ),
				'desc' 		=> __( 'Enable the Sorting feature', 'woocommerce-jetpack' ),
				'desc_tip'	=> __( 'Add more sorting options or remove all sorting including default.', 'woocommerce-jetpack' ),
				'id' 		=> 'wcj_sorting_enabled',
				'default'	=> 'yes',
				'type' 		=> 'checkbox'
			),
			
			array( 'type' 	=> 'sectionend', 'id' => 'wcj_sorting_options' ),
		
			array( 'title' 	=> __( 'Remove All Sorting', 'woocommerce-jetpack' ), 'type' => 'title', 'desc' => '', 'id' => 'wcj_remove_all_sorting_options' ),
			
			array(
				'title' 	=> __( 'Remove All Sorting', 'woocommerce-jetpack' ),
				'desc' 		=> __( 'Remove all sorting (including WooCommerce default)', 'woocommerce-jetpack' ),
				'desc_tip' 	=> apply_filters( 'get_wc_jetpack_plus_message', '', 'desc' ),
				'id' 		=> 'wcj_sorting_remove_all_enabled',
				'default'	=> 'no',
				'type' 		=> 'checkbox',
				'custom_attributes'
							=> apply_filters( 'get_wc_jetpack_plus_message', '', 'disabled' ),
			),

			array( 'type' 	=> 'sectionend', 'id' => 'wcj_remove_all_sorting_options' ),			

			array( 'title'	=> __( 'Add More Sorting', 'woocommerce-jetpack' ), 'type' => 'title', 'desc' => '', 'id' => 'wcj_more_sorting_options' ),			

			array(
				'title' 	=> __( 'Sort by Name - Asc', 'woocommerce-jetpack' ),
				'desc' 		=> __( 'Text visible at front end', 'woocommerce-jetpack' ),
				'id' 		=> 'wcj_sorting_by_name_asc_text',
				'default'	=> 'Sort: A to Z',
				'type' 		=> 'text',
				'css'		=> 'min-width:300px;',
			),
			
			array(
				'title' 	=> __( 'Sort by Name - Asc', 'woocommerce-jetpack' ),
				'desc' 		=> __( 'Enable', 'woocommerce-jetpack' ),
				'desc_tip' 	=> __( 'Check to enable.', 'woocommerce-jetpack' ),
				'id' 		=> 'wcj_sorting_by_name_asc_enabled',
				'default'	=> 'yes',
				'type' 		=> 'checkbox',
			),

			array(
				'title' 	=> __( 'Sort by Name - Desc', 'woocommerce-jetpack' ),
				'desc' 		=> __( 'Text visible at front end', 'woocommerce-jetpack' ),
				'id' 		=> 'wcj_sorting_by_name_desc_text',
				'default'	=> 'Sort: Z to A',
				'type' 		=> 'text',
				'css'		=> 'min-width:300px;',
			),
			
			array(
				'title' 	=> __( 'Sort by Name - Desc', 'woocommerce-jetpack' ),
				'desc' 		=> __( 'Enable', 'woocommerce-jetpack' ),
				'desc_tip' 	=> __( 'Check to enable.', 'woocommerce-jetpack' ),				
				'id' 		=> 'wcj_sorting_by_name_desc_enabled',
				'default'	=> 'yes',
				'type' 		=> 'checkbox',
			),

			array(
				'title' 	=> __( 'Sort by SKU - Asc', 'woocommerce-jetpack' ),
				'desc' 		=> __( 'Text visible at front end', 'woocommerce-jetpack' ),
				'id' 		=> 'wcj_sorting_by_sku_asc_text',
				'default'	=> 'Sort: SKU (asc)',
				'type' 		=> 'text',
				'css'		=> 'min-width:300px;',
			),
			
			array(
				'title' 	=> __( 'Sort by SKU - Asc', 'woocommerce-jetpack' ),
				'desc' 		=> __( 'Enable', 'woocommerce-jetpack' ),
				'desc_tip' 	=> __( 'Check to enable.', 'woocommerce-jetpack' ),				
				'id' 		=> 'wcj_sorting_by_sku_asc_enabled',
				'default'	=> 'yes',
				'type' 		=> 'checkbox',
			),

			array(
				'title' 	=> __( 'Sort by SKU - Desc', 'woocommerce-jetpack' ),
				'desc' 		=> __( 'Text visible at front end', 'woocommerce-jetpack' ),
				'id' 		=> 'wcj_sorting_by_sku_desc_text',
				'default'	=> 'Sort: SKU (desc)',
				'type' 		=> 'text',
				'css'		=> 'min-width:300px;',
			),
			
			array(
				'title' 	=> __( 'Sort by SKU - Desc', 'woocommerce-jetpack' ),
				'desc' 		=> __( 'Enable', 'woocommerce-jetpack' ),
				'desc_tip' 	=> __( 'Check to enable.', 'woocommerce-jetpack' ),				
				'id' 		=> 'wcj_sorting_by_sku_desc_enabled',
				'default'	=> 'yes',
				'type' 		=> 'checkbox',
			),
			
			array( 'type' => 'sectionend', 'id' => 'wcj_more_sorting_options' ),
		);
		
		return $settings;
	}
	
	/*
	 * Add settings section to WooCommerce > Settings > Jetpack.
	 */	
	function settings_section( $sections ) {
	
		$sections['sorting'] = 'Sorting';
		
		return $sections;
	}	
}

endif;

return new WCJ_Sorting();

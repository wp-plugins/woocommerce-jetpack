<?php
/**
 * WooCommerce Jetpack Product Info
 *
 * The WooCommerce Jetpack Product Info class.
 *
 * @class 		WCJ_Product_Info
 * @version		1.0.0
 * @package		WC_Jetpack/Classes
 * @category	Class
 * @author 		Algoritmika Ltd.
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'WCJ_Product_Info' ) ) :

class WCJ_Product_Info {
	
	/**
	 * Constructor.
	 */	
	public function __construct() {
	
		// Main hooks
		if ( 'yes' == get_option( 'wcj_product_info_enabled' ) ) {
		
			add_filter( 'woocommerce_product_tabs', array( $this, 'customize_product_tabs' ), 98 );	
			
			if ( get_option( 'wcj_product_info_related_products_enable' ) == 'yes' ) {
				add_filter( 'woocommerce_related_products_args', array( $this, 'related_products_limit' ), 100 );
				add_filter( 'woocommerce_output_related_products_args', array( $this, 'related_products_limit_args' ), 100 );
			}
		}
		
		// Settings hooks
		add_filter( 'wcj_settings_sections', array( $this, 'settings_section' ) );
		add_filter( 'wcj_settings_product_info', array( $this, 'get_settings' ), 100 );
		add_filter( 'wcj_features_status', array( $this, 'add_enabled_option' ), 100 );
	}

	/**
	 * Return feature's enable/disable option.
	 */
	public function add_enabled_option( $settings ) {
	
		$all_settings = $this->get_settings();
		$settings[] = $all_settings[1];
		
		return $settings;
	}
	
	/**
	 * Change number of related products on product page.
	 */ 
	function related_products_limit_args( $args ) {
			
		$args['posts_per_page'] = get_option( 'wcj_product_info_related_products_num' );
		$args['orderby'] = get_option( 'wcj_product_info_related_products_orderby' );
		$args['columns'] = get_option( 'wcj_product_info_related_products_columns' );
				
		return $args;
	}	
	
	/**
	 * Change number of related products on product page.
	 */ 
	function related_products_limit( $args ) {
			
		$args['posts_per_page'] = get_option( 'wcj_product_info_related_products_num' );
		$args['orderby'] = get_option( 'wcj_product_info_related_products_orderby' );
		if ( get_option( 'wcj_product_info_related_products_orderby' ) != 'rand' ) $args['order'] = get_option( 'wcj_product_info_related_products_order' );
				
		return $args;
	}

	/**
	 * Customize the product tabs.
	 */
	function customize_product_tabs( $tabs ) {
	 
		// Unset
		if ( get_option( 'wcj_product_info_product_tabs_description_disable' ) === 'yes' ) 
			unset( $tabs['description'] );
		if ( get_option( 'wcj_product_info_product_tabs_reviews_disable' ) === 'yes' ) 
			unset( $tabs['reviews'] );
		if ( get_option( 'wcj_product_info_product_tabs_additional_information_disable' ) === 'yes' ) 
			unset( $tabs['additional_information'] );
		
		// Priority and Title
		if ( isset( $tabs['description'] ) ) { 
			$tabs['description']['priority'] = apply_filters( 'wcj_get_option_filter', 10, get_option( 'wcj_product_info_product_tabs_description_priority' ) );
			if ( get_option( 'wcj_product_info_product_tabs_description_title' ) !== '' ) 
				$tabs['description']['title'] = get_option( 'wcj_product_info_product_tabs_description_title' );
		}
		if ( isset( $tabs['reviews'] ) ) { 
			$tabs['reviews']['priority'] = apply_filters( 'wcj_get_option_filter', 20, get_option( 'wcj_product_info_product_tabs_reviews_priority' ) );
			if ( get_option( 'wcj_product_info_product_tabs_reviews_title' ) !== '' )
				$tabs['reviews']['title'] = get_option( 'wcj_product_info_product_tabs_reviews_title' );
		}
		if ( isset( $tabs['additional_information'] ) ) {
			$tabs['additional_information']['priority'] = apply_filters( 'wcj_get_option_filter', 30, get_option( 'wcj_product_info_product_tabs_additional_information_priority' ) );
			if ( get_option( 'wcj_product_info_product_tabs_additional_information_title' ) !== '' )
				$tabs['additional_information']['title'] = get_option( 'wcj_product_info_product_tabs_additional_information_title' );
		}
	 
		return $tabs;
	}	
	
	/**
	 * Get settings.
	 */	
	function get_settings() {

		$settings = array(
		
			// Product Info Options
			array( 'title' 	=> __( 'Product Info Options', 'woocommerce-jetpack' ), 'type' => 'title', 'desc' => '', 'id' => 'wcj_product_info_options' ),
			
			array(
				'title' 	=> __( 'Product Info', 'woocommerce-jetpack' ),
				'desc' 		=> __( 'Enable the Product Info feature', 'woocommerce-jetpack' ),
				'desc_tip'	=> __( 'Customize single product tabs, change related products number.', 'woocommerce-jetpack' ),
				'id' 		=> 'wcj_product_info_enabled',
				'default'	=> 'yes',
				'type' 		=> 'checkbox'
			),
			
			array( 'type' 	=> 'sectionend', 'id' => 'wcj_product_info_options' ),		
		
			// Product Tabs Options
			array( 'title' 	=> __( 'Product Tabs Options', 'woocommerce-jetpack' ), 'type' => 'title', 'desc' => 'This section lets you customize single product tabs.', 'id' => 'wcj_product_info_product_tabs_options' ),
		
			array(
				'title' 	=> __( 'Description Tab', 'woocommerce-jetpack' ),
				'desc' 		=> __( 'Remove tab from product page', 'woocommerce-jetpack' ),
				'id' 		=> 'wcj_product_info_product_tabs_description_disable',
				'default'	=> 'no',
				'type' 		=> 'checkbox'
			),
			
			array(
				'title'    => __( 'Priority (i.e. Order)', 'woocommerce-jetpack' ),
				'id'       => 'wcj_product_info_product_tabs_description_priority',
				'default'  => 10,
				'type'     => 'number',
				'desc' 	   => apply_filters( 'get_wc_jetpack_plus_message', '', 'desc' ),
				'custom_attributes'	
						   => apply_filters( 'get_wc_jetpack_plus_message', '', 'readonly' ),					
			),
			
			array(
				'title'    => __( 'Title', 'woocommerce-jetpack' ),
				'desc_tip' 	   => __( 'Leave blank for WooCommerce defaults', 'woocommerce-jetpack' ),
				'id'       => 'wcj_product_info_product_tabs_description_title',
				'default'  => '',
				'type'     => 'text',
			),			
		
			array(
				'title' 	=> __( 'Reviews Tab', 'woocommerce-jetpack' ),
				'desc' 		=> __( 'Remove tab from product page', 'woocommerce-jetpack' ),
				'id' 		=> 'wcj_product_info_product_tabs_reviews_disable',
				'default'	=> 'no',
				'type' 		=> 'checkbox'
			),

			array(
				'title'    => __( 'Priority (i.e. Order)', 'woocommerce-jetpack' ),
				'id'       => 'wcj_product_info_product_tabs_reviews_priority',
				'default'  => 20,
				'type'     => 'number',
				'desc' 	   => apply_filters( 'get_wc_jetpack_plus_message', '', 'desc' ),
				'custom_attributes'	
						   => apply_filters( 'get_wc_jetpack_plus_message', '', 'readonly' ),				
			),
			
			array(
				'title'    => __( 'Title', 'woocommerce-jetpack' ),
				'desc_tip' 	   => __( 'Leave blank for WooCommerce defaults', 'woocommerce-jetpack' ),
				'id'       => 'wcj_product_info_product_tabs_reviews_title',
				'default'  => '',
				'type'     => 'text',
			),			
				
			array(
				'title' 	=> __( 'Additional Information Tab', 'woocommerce-jetpack' ),
				'desc' 		=> __( 'Remove tab from product page', 'woocommerce-jetpack' ),
				'id' 		=> 'wcj_product_info_product_tabs_additional_information_disable',
				'default'	=> 'no',
				'type' 		=> 'checkbox'
			),
			
			array(
				'title'    => __( 'Priority (i.e. Order)', 'woocommerce-jetpack' ),
				'id'       => 'wcj_product_info_product_tabs_additional_information_priority',
				'default'  => 30,
				'type'     => 'number',
				'desc' 	   => apply_filters( 'get_wc_jetpack_plus_message', '', 'desc' ),
				'custom_attributes'	
						   => apply_filters( 'get_wc_jetpack_plus_message', '', 'readonly' ),				
			),

			array(
				'title'    => __( 'Title', 'woocommerce-jetpack' ),
				'desc_tip' => __( 'Leave blank for WooCommerce defaults', 'woocommerce-jetpack' ),
				'id'       => 'wcj_product_info_product_tabs_additional_information_title',
				'default'  => '',
				'type'     => 'text',
			),
			
			array( 'type' 	=> 'sectionend', 'id' => 'wcj_product_info_product_tabs_options' ),
			
			// Related Products Options
			array( 'title' 	=> __( 'Related Products Options', 'woocommerce-jetpack' ), 'type' => 'title', 'desc' => 'This section lets you change related products number.', 'id' => 'wcj_product_info_related_products_options' ),
		
			array(
				'title' 	=> __( 'Enable', 'woocommerce-jetpack' ),
				'id' 		=> 'wcj_product_info_related_products_enable',
				'default'	=> 'no',
				'type' 		=> 'checkbox'
			),
			
			array(
				'title'    => __( 'Related Products Number', 'woocommerce-jetpack' ),
				'id'       => 'wcj_product_info_related_products_num',
				'default'  => 3,
				'type'     => 'number',
			),
			
			array(
				'title'    => __( 'Related Products Columns', 'woocommerce-jetpack' ),
				'id'       => 'wcj_product_info_related_products_columns',
				'default'  => 3,
				'type'     => 'number',
			),			
			
			array(
				'title'    => __( 'Order by', 'woocommerce-jetpack' ),
				'id'       => 'wcj_product_info_related_products_orderby',
				'default'  => 'rand',
				'type'     => 'select',
				'options'  => array(
						'rand'  => __( 'Random', 'woocommerce-jetpack' ),
						'date'	=> __( 'Date', 'woocommerce-jetpack' ),
						'title' => __( 'Title', 'woocommerce-jetpack' ),
					),
			),			
			
			array(
				'title'    => __( 'Order', 'woocommerce-jetpack' ),
				'desc_tip' => __( 'Ignored if order by "Random" is selected above.', 'woocommerce-jetpack' ),
				'id'       => 'wcj_product_info_related_products_order',
				'default'  => 'desc',
				'type'     => 'select',
				'options'  => array(
						'asc'   => __( 'Ascending', 'woocommerce-jetpack' ),
						'desc'	=> __( 'Descending', 'woocommerce-jetpack' ),
					),
			),					
		
			array( 'type' 	=> 'sectionend', 'id' => 'wcj_product_info_related_products_options' ),			
			
		);
		
		return $settings;
	}
	
	/**
	 * Add settings section.
	 */	
	function settings_section( $sections ) {
	
		$sections['product_info'] = __( 'Product Info', 'woocommerce-jetpack' );
		
		return $sections;
	}	
}

endif;

return new WCJ_Product_Info();

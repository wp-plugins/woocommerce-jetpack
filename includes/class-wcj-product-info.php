<?php
/**
 * WooCommerce Jetpack Product Info
 *
 * The WooCommerce Jetpack Product Info class.
 *
 * @class 		WCJ_Product_Info
 * @version		1.1.4
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
	
		// Product archives filters array
		$this->product_info_on_archive_filters_array = array(
			'woocommerce_before_shop_loop_item'				=> __( 'Before product', 'woocommerce-jetpack' ),
			'woocommerce_before_shop_loop_item_title'		=> __( 'Before product title', 'woocommerce-jetpack' ),
			'woocommerce_after_shop_loop_item'				=> __( 'After product', 'woocommerce-jetpack' ),
			'woocommerce_after_shop_loop_item_title'		=> __( 'After product title', 'woocommerce-jetpack' ),
		);

		// Single product filters array
		$this->product_info_on_single_filters_array = array(
			'woocommerce_single_product_summary'			=> __( 'Inside single product summary', 'woocommerce-jetpack' ),
			'woocommerce_before_single_product_summary'		=> __( 'Before single product summary', 'woocommerce-jetpack' ),
			'woocommerce_after_single_product_summary'		=> __( 'After single product summary', 'woocommerce-jetpack' ),
		);			
	
		// Main hooks
		if ( 'yes' === get_option( 'wcj_product_info_enabled' ) ) {
		
			add_filter( 'woocommerce_product_tabs', array( $this, 'customize_product_tabs' ), 98 );	
			
			if ( get_option( 'wcj_product_info_related_products_enable' ) === 'yes' ) {
				add_filter( 'woocommerce_related_products_args', array( $this, 'related_products_limit' ), 100 );
				add_filter( 'woocommerce_output_related_products_args', array( $this, 'related_products_limit_args' ), 100 );
			}
					
			if ( ( 'yes' === get_option( 'wcj_product_info_on_archive_enabled' ) ) &&
				 ( '' != get_option( 'wcj_product_info_on_archive' ) ) &&
				 ( '' != get_option( 'wcj_product_info_on_archive_filter' ) ) && 
				 ( '' != get_option( 'wcj_product_info_on_archive_filter_priority' ) ) &&
				 ( array_key_exists( get_option( 'wcj_product_info_on_archive_filter' ), $this->product_info_on_archive_filters_array ) ) )
						add_action( get_option( 'wcj_product_info_on_archive_filter' ), array( $this, 'product_info' ), get_option( 'wcj_product_info_on_archive_filter_priority' ) );
				
			if ( ( 'yes' === get_option( 'wcj_product_info_on_single_enabled' ) ) &&
				 ( '' != get_option( 'wcj_product_info_on_single' ) ) &&
				 ( '' != get_option( 'wcj_product_info_on_single_filter' ) ) &&
				 ( '' != get_option( 'wcj_product_info_on_single_filter_priority' ) ) &&
				 ( array_key_exists( get_option( 'wcj_product_info_on_single_filter' ), $this->product_info_on_single_filters_array ) ) )
						add_action( get_option( 'wcj_product_info_on_single_filter' ), array( $this, 'product_info' ), get_option( 'wcj_product_info_on_single_filter_priority' ) );				
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
	 * product_info.
	 */
	public function product_info() {	

		$the_action_name = current_filter();
		if ( array_key_exists( $the_action_name, $this->product_info_on_archive_filters_array ) )
			$the_product_info = get_option( 'wcj_product_info_on_archive' );
		else if ( array_key_exists( $the_action_name, $this->product_info_on_single_filters_array ) )
			$the_product_info = apply_filters( 'wcj_get_option_filter', 'Total sales: %total_sales%', get_option( 'wcj_product_info_on_single' ) );			
		global $product;
		$product_custom_fields = get_post_custom( $product->id );
		$total_sales = ( isset( $product_custom_fields['total_sales'][0] ) ) ? $product_custom_fields['total_sales'][0] : 0;
		$product_info_shortcodes_array = array(
			'%sku%'				=> $product->get_sku(),
			'%total_sales%'		=> $total_sales,
		);
		foreach ( $product_info_shortcodes_array as $search_for_phrase => $replace_with_phrase )
			$the_product_info = str_replace( $search_for_phrase, $replace_with_phrase, $the_product_info );
		echo $the_product_info;
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
				'type' 		=> 'checkbox',
			),
			
			array( 'type' 	=> 'sectionend', 'id' => 'wcj_product_info_options' ),		
			
			// Product Info Additional Options
			array( 'title' 	=> __( 'More Products Info', 'woocommerce-jetpack' ), 'type' => 'title', 'desc' => '', 'id' => 'wcj_product_info_additional_options' ),
			
			array(
				'title' 	=> __( 'Product Info on Archive Pages', 'woocommerce-jetpack' ),
				'desc' 		=> __( 'Enable', 'woocommerce-jetpack' ),
				'id' 		=> 'wcj_product_info_on_archive_enabled',
				'default'	=> 'no',
				'type' 		=> 'checkbox',
			),			
			
			array(
				'title' 	=> '',
				'desc_tip'	=> __( 'HTML info. Predefined: %total_sales%, %sku%', 'woocommerce-jetpack' ),
				'id' 		=> 'wcj_product_info_on_archive',
				'default'	=> 'SKU: %sku%',
				'type' 		=> 'textarea',
				'css'	   => 'width:50%;min-width:300px;height:100px;',				
			),
			
			array(
				'title'    => '',
				'desc'     => __( 'Position', 'woocommerce-jetpack' ),
				'id'       => 'wcj_product_info_on_archive_filter',
				'css'      => 'min-width:350px;',
				'class'    => 'chosen_select',
				'default'  => 'woocommerce_after_shop_loop_item_title',
				'type'     => 'select',
				'options'  => $this->product_info_on_archive_filters_array,
				'desc_tip' =>  true,
			),		

			array(
				'title'    => '',
				'desc_tip'    => __( 'Priority (i.e. Order)', 'woocommerce-jetpack' ),
				'id'       => 'wcj_product_info_on_archive_filter_priority',
				'default'  => 10,
				'type'     => 'number',
			),			
			
			array(
				'title' 	=> __( 'Product Info on Single Product Pages', 'woocommerce-jetpack' ),
				'desc' 		=> __( 'Enable', 'woocommerce-jetpack' ),
				'id' 		=> 'wcj_product_info_on_single_enabled',
				'default'	=> 'no',
				'type' 		=> 'checkbox',
			),			

			array(
				'title' 	=> '',
				'desc_tip'	=> __( 'HTML info. Predefined: %total_sales%, %sku%', 'woocommerce-jetpack' ),
				'id' 		=> 'wcj_product_info_on_single',
				'default'	=> 'Total sales: %total_sales%',
				'type' 		=> 'textarea',
				'desc' 	    => apply_filters( 'get_wc_jetpack_plus_message', '', 'desc' ),
				'custom_attributes'
						    => apply_filters( 'get_wc_jetpack_plus_message', '', 'readonly' ),
				'css'	    => 'width:50%;min-width:300px;height:100px;',				
			),		

			array(
				'title'    => '',
				'desc'     => __( 'Position', 'woocommerce-jetpack' ),
				'id'       => 'wcj_product_info_on_single_filter',
				'css'      => 'min-width:350px;',
				'class'    => 'chosen_select',
				'default'  => 'woocommerce_after_single_product_summary',
				'type'     => 'select',
				'options'  => $this->product_info_on_single_filters_array,
				'desc_tip' =>  true,
			),		

			array(
				'title'    => '',
				'desc_tip'    => __( 'Priority (i.e. Order)', 'woocommerce-jetpack' ),
				'id'       => 'wcj_product_info_on_single_filter_priority',
				'default'  => 10,
				'type'     => 'number',
			),			
			
			array( 'type' 	=> 'sectionend', 'id' => 'wcj_product_info_additional_options' ),				
		
			// Product Tabs Options
			array( 'title' 	=> __( 'Product Tabs Options', 'woocommerce-jetpack' ), 'type' => 'title', 'desc' => 'This section lets you customize single product tabs.', 'id' => 'wcj_product_info_product_tabs_options' ),
		
			array(
				'title' 	=> __( 'Description Tab', 'woocommerce-jetpack' ),
				'desc' 		=> __( 'Remove tab from product page', 'woocommerce-jetpack' ),
				'id' 		=> 'wcj_product_info_product_tabs_description_disable',
				'default'	=> 'no',
				'type' 		=> 'checkbox',
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
				'type' 		=> 'checkbox',
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
				'type' 		=> 'checkbox',
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
				'type' 		=> 'checkbox',
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

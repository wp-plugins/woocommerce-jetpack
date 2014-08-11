<?php
/**
 * WooCommerce Jetpack Call for Price
 *
 * The WooCommerce Jetpack Call for Price class.
 *
 * @class		WCJ_Call_For_Price
 * @category	Class
 * @author		Algoritmika Ltd.
 */
 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'WCJ_Call_For_Price' ) ) :

class WCJ_Call_For_Price {
	
	public function __construct() {
	
		// Defaults
		$this->default_empty_price_text = '<strong>Call for price</strong>';
		
		// HOOKS
		// Main hooks
		// Empty Price hooks
		if ( get_option( 'wcj_call_for_price_enabled' ) == 'yes' ) {
		
			add_filter( 'woocommerce_empty_price_html', array( $this, 'on_empty_price' ), 100 );
			add_filter( 'woocommerce_sale_flash', array( $this, 'hide_sales_flash' ), 100, 2 );
		}		
	
		// Settings hooks
		add_filter( 'wcj_settings_sections', array( $this, 'settings_section' ) );
		add_filter( 'wcj_settings_call_for_price', array( $this, 'get_settings' ), 100 );
		add_filter( 'wcj_features_status', array( $this, 'add_enabled_option' ), 100 );
	}
	
	/**
	 * add_enabled_option.
	 */
	public function add_enabled_option( $settings ) {
	
		$all_settings = $this->get_settings();
		$settings[] = $all_settings[1];
		
		return $settings;
	}
	
	/**
	 * hide_sales_flash.
	 */
	public function hide_sales_flash( $post, $product )	{
	
		if ( get_option('wcj_call_for_price_hide_sale_sign') === 'yes' ) {
		
			$current_product = get_product( $product->ID );
			if ( $current_product->get_price() == '' ) return false;
		}
		
		return '<span class="onsale">' . __( 'Sale!', 'woocommerce' ) . '</span>';
	}
	
	/**
	 * on_empty_price.
	 *	
	public function on_empty_price( $price ) {

		if ( ( get_option('wcj_call_for_price_show_on_single') == 'yes' ) && is_single() ) return $this->default_empty_price_text;
		if ( ( get_option('wcj_call_for_price_show_on_archive') == 'yes' ) && is_archive() ) return $this->default_empty_price_text;
		if ( ( get_option('wcj_call_for_price_show_on_home') == 'yes' ) && is_front_page() ) return $this->default_empty_price_text;
		
		// No changes
		return $price;
	}
	
	/**
	 * On empty price.
	 */	
	public function on_empty_price( $price ) {
	
		/*$updated_price = get_option( 'wcj_call_for_price_text' );
		
		if ( ( get_option('wcj_call_for_price_show_on_single') == 'yes' ) && is_single() ) return apply_filters( 'wcj_get_option_filter', $this->default_empty_price_text, $updated_price );
		if ( ( get_option('wcj_call_for_price_show_on_archive') == 'yes' ) && is_archive() ) return apply_filters( 'wcj_get_option_filter', $this->default_empty_price_text, $updated_price );
		if ( ( get_option('wcj_call_for_price_show_on_home') == 'yes' ) && is_front_page() ) return apply_filters( 'wcj_get_option_filter', $this->default_empty_price_text, $updated_price );*/
		
		if ( ( get_option('wcj_call_for_price_text') !== '' ) && is_single() ) return apply_filters( 'wcj_get_option_filter', $this->default_empty_price_text, get_option('wcj_call_for_price_text') );
		if ( ( get_option('wcj_call_for_price_text_on_archive') !== '' ) && is_archive() ) return apply_filters( 'wcj_get_option_filter', $this->default_empty_price_text, get_option('wcj_call_for_price_text_on_archive') );
		if ( ( get_option('wcj_call_for_price_text_on_home') !== '' ) && is_front_page() ) return apply_filters( 'wcj_get_option_filter', $this->default_empty_price_text, get_option('wcj_call_for_price_text_on_home') );		
		
		// No changes
		return $price;
	}	
	
	/**
	 * get_settings.
	 */		
	function get_settings() {

		$settings = array(

			array(	'title' => __( 'Call for Price Options', 'woocommerce-jetpack' ), 'type' => 'title', 'desc' => __( 'Leave price empty when adding or editing products. Then set the options here.', 'woocommerce-jetpack' ), 'id' => 'wcj_call_for_price_options' ),
			
			array(
				'title' 	=> __( 'Call for Price', 'woocommerce-jetpack' ),
				'desc' 		=> __( 'Enable the Call for Price feature', 'woocommerce-jetpack' ),
				'desc_tip'	=> __( 'Create any custom price label for all products with empty price.', 'woocommerce-jetpack' ),
				'id' 		=> 'wcj_call_for_price_enabled',
				'default'	=> 'yes',
				'type' 		=> 'checkbox',
			),
		
			array(
				'title' 	=> __( 'Label to Show on Single', 'woocommerce-jetpack' ),
				'desc_tip' 	=> __( 'This sets the html to output on empty price. Leave blank to disable.', 'woocommerce-jetpack' ),
				'desc'     	=> apply_filters( 'get_wc_jetpack_plus_message', '', 'desc' ),
				'id' 		=> 'wcj_call_for_price_text',
				'default'	=> $this->default_empty_price_text,
				'type' 		=> 'textarea',
				'css'		=> 'width:50%;min-width:300px;',
				'custom_attributes'	
							=> apply_filters( 'get_wc_jetpack_plus_message', '', 'readonly' ),
			),
			
			array(
				'title' 	=> __( 'Label to Show on Archives', 'woocommerce-jetpack' ),
				'desc_tip' 	=> __( 'This sets the html to output on empty price. Leave blank to disable.', 'woocommerce-jetpack' ),
				'desc'     	=> apply_filters( 'get_wc_jetpack_plus_message', '', 'desc' ),
				'id' 		=> 'wcj_call_for_price_text_on_archive',
				'default'	=> $this->default_empty_price_text,
				'type' 		=> 'textarea',
				'css'		=> 'width:50%;min-width:300px;',
				'custom_attributes'	
							=> apply_filters( 'get_wc_jetpack_plus_message', '', 'readonly' ),
			),

			array(
				'title' 	=> __( 'Label to Show on Homepage', 'woocommerce-jetpack' ),
				'desc_tip' 	=> __( 'This sets the html to output on empty price. Leave blank to disable.', 'woocommerce-jetpack' ),
				'desc'     	=> apply_filters( 'get_wc_jetpack_plus_message', '', 'desc' ),
				'id' 		=> 'wcj_call_for_price_text_on_home',
				'default'	=> $this->default_empty_price_text,
				'type' 		=> 'textarea',
				'css'		=> 'width:50%;min-width:300px;',
				'custom_attributes'	
							=> apply_filters( 'get_wc_jetpack_plus_message', '', 'readonly' ),
			),			
			
			/*array(
				'title' 	=> __( 'Visibility', 'woocommerce-jetpack' ),
				//'title' 	=> __( 'Show on Single Product', 'woocommerce-jetpack' ),
				'desc' 		=> __( 'Check to show on single products page', 'woocommerce-jetpack' ),
				'id' 		=> 'wcj_call_for_price_show_on_single',
				'default'	=> 'yes',
				'type' 		=> 'checkbox',
				'checkboxgroup'   => 'start',
			),

			array(
				//'title' 	=> __( 'Show on Products Archive', 'woocommerce-jetpack' ),
				'desc' 		=> __( 'Check to show on products archive page', 'woocommerce-jetpack' ),
				'id' 		=> 'wcj_call_for_price_show_on_archive',
				'default'	=> 'yes',
				'type' 		=> 'checkbox',
				'checkboxgroup'		=> '',
			),

			array(
				//'title' 	=> __( 'Show on Home Page', 'woocommerce-jetpack' ),
				'desc' 		=> __( 'Check to show on home page', 'woocommerce-jetpack' ),
				'id' 		=> 'wcj_call_for_price_show_on_home',
				'default'	=> 'yes',
				'type' 		=> 'checkbox',
				'checkboxgroup'   => 'end',
			),*/

			array(
				'title' => __( 'Hide Sale! Tag', 'woocommerce-jetpack' ),
				'desc' 		=> __( 'Hide the tag', 'woocommerce-jetpack' ),
				'id' 		=> 'wcj_call_for_price_hide_sale_sign',
				'default'	=> 'yes',
				'type' 		=> 'checkbox',
			),
			
			array( 'type' => 'sectionend', 'id' => 'wcj_call_for_price_options' ),
		);
		
		return $settings;
	}
	
	/**
	 * settings_section.
	 */		
	function settings_section( $sections ) {
	
		$sections['call_for_price'] = __( 'Call for Price', 'woocommerce-jetpack' );
		
		return $sections;
	}	
}

endif;

return new WCJ_Call_For_Price();

<?php
/**
 * WooCommerce Jetpack Product Custom Input
 *
 * The WooCommerce Jetpack Product Custom Input class.
 *
 * @class       WCJ_Product_Custom_Input
 * @version		1.0.0.1
 * @category	Class
 * @author 		Algoritmika Ltd.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
 
if ( ! class_exists( 'WCJ_Product_Custom_Input' ) ) :
 
class WCJ_Product_Custom_Input {
    
    /**
     * Constructor.
     */
    public function __construct() {
 
        // Main hooks
        if ( 'yes' === get_option( 'wcj_product_custom_input_enabled' ) ) {
		
			// Product Add-ons		
			add_action( 'woocommerce_before_add_to_cart_button', 	array( $this, 'product_add_ons_add_custom_text_input' ), 100 );
			add_filter( 'woocommerce_add_cart_item_data', 			array( $this, 'product_add_ons_add_cart_item_data' ), 100, 3 );
			add_filter( 'woocommerce_get_cart_item_from_session', 	array( $this, 'product_add_ons_get_cart_item_from_session' ), 100, 3 );
			add_filter( 'woocommerce_cart_item_name', 				array( $this, 'product_add_ons_cart_item_name' ), 100, 3 );
			add_action( 'woocommerce_add_order_item_meta', 			array( $this, 'product_add_ons_add_order_item_meta' ), 100, 3 );
			add_filter( 'woocommerce_add_to_cart_validation', 		array( $this, 'product_add_ons_validate_values' ), 100, 2 );		
        
        }        
    
        // Settings hooks
        add_filter( 'wcj_settings_sections', array( $this, 'settings_section' ) );
        add_filter( 'wcj_settings_product_custom_input', array( $this, 'get_settings' ), 100 );
        add_filter( 'wcj_features_status', array( $this, 'add_enabled_option' ), 100 );
    }
	
	
	/**
	 * product_add_ons_validate_values.
	 */
	public function product_add_ons_validate_values( $passed, $product_id ) {
		if ( isset( $_POST['wcj_product_add_ons_custom_text'] ) && '' == $_POST['wcj_product_add_ons_custom_text'] ) {
			$passed = false;
			wc_add_notice( __( 'Fill text box before adding to cart.', 'woocommerce-jetpack' ), 'error' );
		}
		return $passed;
	}		
	
	/**
	 * product_add_ons_add_custom_text_input.
	 */
	public function product_add_ons_add_custom_text_input() {
		echo '<p>' . '<input type="text" name="wcj_product_add_ons_custom_text" value="the custom text">' . '</p>';
	}	
	
	/**
	 * product_add_ons_add_cart_item_data.
	 */
	public function product_add_ons_add_cart_item_data( $cart_item_data, $product_id, $variation_id ) {
		if ( isset( $_POST['wcj_product_add_ons_custom_text'] ) )
			$cart_item_data['wcj_product_add_ons_custom_text'] = $_POST['wcj_product_add_ons_custom_text'];
		return $cart_item_data;
	}

	/**
	 * product_add_ons_get_cart_item_from_session.
	 */
	public function product_add_ons_get_cart_item_from_session( $item, $values, $key ) {
		if ( array_key_exists( 'wcj_product_add_ons_custom_text', $values ) )
			$item['wcj_product_add_ons_custom_text'] = $values['wcj_product_add_ons_custom_text'];
		return $item;
	}
	
	/**
	 * product_add_ons_cart_item_name.
	 */
	public function product_add_ons_cart_item_name(  $name, $cart_item, $cart_item_key  ) {	
		if ( array_key_exists( 'wcj_product_add_ons_custom_text', $cart_item ) )	
			$name .= '<p style="font-size:smaller;">' . $cart_item['wcj_product_add_ons_custom_text'] . '</p>';
		return $name;
	}		
	
	/**
	 * product_add_ons_add_order_item_meta.
	 */
	public function product_add_ons_add_order_item_meta(  $item_id, $values, $cart_item_key  ) {		
		if ( array_key_exists( 'wcj_product_add_ons_custom_text', $values ) )
			wc_add_order_item_meta( $item_id, '_wcj_product_add_ons_custom_text', $values['wcj_product_add_ons_custom_text'] );
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
     * get_settings.
     */    
    function get_settings() {
 
        $settings = array(
 
            array( 'title' => __( 'Product Custom Input Options', 'woocommerce-jetpack' ), 'type' => 'title', 'desc' => __( 'Product Custom Input.', 'woocommerce-jetpack' ), 'id' => 'wcj_product_custom_input_options' ),
            
            array(
                'title'    => __( 'Product Custom Input', 'woocommerce-jetpack' ),
                'desc'     => __( 'Enable the Product Custom Input feature', 'woocommerce-jetpack' ),
                'desc_tip' => __( 'Product Custom Input.', 'woocommerce-jetpack' ),
                'id'       => 'wcj_product_custom_input_enabled',
                'default'  => 'yes',
                'type'     => 'checkbox',
            ),
        
            array( 'type'  => 'sectionend', 'id' => 'wcj_product_custom_input_options' ),
        );
        
        return $settings;
    }
 
    /**
     * settings_section.
     */
    function settings_section( $sections ) {    
        $sections['product_custom_input'] = __( 'Product Custom Input', 'woocommerce-jetpack' );        
        return $sections;
    }    
}
 
endif;
 
return new WCJ_Product_Custom_Input();

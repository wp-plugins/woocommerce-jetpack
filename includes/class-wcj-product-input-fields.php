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
		
			// Add meta box
			add_action( 'add_meta_boxes', array( $this, 'add_custom_input_fields_meta_box' ) );
			// Save Post
			add_action( 'save_post_product', array( $this, 'save_custom_input_fields' ), 999, 2 );		
		
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
	 * Save custom input fields.
	 */	
	public function save_custom_input_fields( $post_id, $post ) {
		// Check that we are saving with input fields displayed.
		if ( ! isset( $_POST['woojetpack_input_fields_save_post'] ) )
			return;
		// Option name?
		$option_name = 'wcj_input_fields_text_1_enabled';
		// Save
		if ( isset( $_POST[ $option_name ] ) )
			update_post_meta( $post_id, '_' . $option_name, $_POST[ $option_name ] );
	}		
	
	/**
	 * add_custom_input_fields_meta_box.
	 */	
	public function add_custom_input_fields_meta_box() {	
		add_meta_box( 'wc-jetpack-input-fields', 'WooCommerce Jetpack: Custom Input Fields', array($this, 'create_custom_input_fields_meta_box'), 'product', 'normal', 'high' );
	}	
	
	/**
	 * create_custom_input_fields_meta_box.
	 */	
	public function create_custom_input_fields_meta_box() {
		$html = '<h4>' . __( 'Text Fields', 'woocommerce-jetpack' ) . '</h4>';
		$html .= '<table style="width:100%;">';
	
		$is_disabled = '';		
		$current_post_id = 0;		
		
		$option_name = 'wcj_input_fields_text_1_enabled';		
		$is_checked = checked( get_post_meta( $current_post_id, '_' . $option_name, true ), 'on', false );				
		$html .= '<tr>';
		$html .= '<td>';		
		$html .= __( 'Enable', 'woocommerce-jetpack' );
		$html .= '</td>';		
		$html .= '<td>';
		$html .= '<input class="checkbox" type="checkbox" ' . $is_disabled . ' name="' . $option_name . '" id="' . $option_name . '" ' . $is_checked . ' />';
		$html .= '</td>';
		$html .= '</tr>';
		
		$option_name = 'wcj_input_fields_text_1_requred';		
		$is_checked = checked( get_post_meta( $current_post_id, '_' . $option_name, true ), 'on', false );		
		$html .= '<tr>';
		$html .= '<td>';		
		$html .= __( 'Required', 'woocommerce-jetpack' );
		$html .= '</td>';		
		$html .= '<td>';
		$html .= '<input class="checkbox" type="checkbox" ' . $is_disabled . ' name="' . $option_name . '" id="' . $option_name . '" ' . $is_checked . ' />';
		$html .= '</td>';
		$html .= '</tr>';		

		$option_name = 'wcj_input_fields_text_1_title';
		$saved_title = get_post_meta( $current_post_id, '_' . $option_name, true );
		$html .= '<tr>';
		$html .= '<td>';
		$html .= __( 'Title', 'woocommerce-jetpack' );
		$html .= '</td>';
		$html .= '<td>';
		$html .= '<textarea style="width:30%;min-width:100px;height:50px;" ' . $is_disabled . ' name="' . $option_name . '">' . $saved_title . '</textarea>';		
		$html .= '</td>';
		$html .= '</tr>';
	
		$html .= '</table>';
		$html .= '<input type="hidden" name="woojetpack_input_fields_save_post" value="woojetpack_input_fields_save_post">';
		echo $html;
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

<?php
/**
 * WooCommerce Jetpack Product Input Fields Global
 *
 * The WooCommerce Jetpack Product Input Fields Global class.
 *
 * @class       WCJ_Product_Input_Fields_Global
 * @version		1.0.0
 * @category	Class
 * @author 		Algoritmika Ltd.
 */

if ( ! defined( 'ABSPATH' ) ) exit;
 
if ( ! class_exists( 'WCJ_Product_Input_Fields_Global' ) ) :
 
class WCJ_Product_Input_Fields_Global extends WCJ_Product_Input_Fields {
    
    /**
     * Constructor.
     */
    public function __construct() {
 
		$this->scope = 'global';
 
        // Main hooks
        if ( 'yes' === get_option( 'wcj_product_input_fields_global_enabled' ) ) {			
					
			// Show fields at frontend
			add_action( 'woocommerce_before_add_to_cart_button', 	array( $this, 'add_product_input_fields_to_frontend' ), 100 );			

			// Process from $_POST to cart item data
			add_filter( 'woocommerce_add_to_cart_validation', 		array( $this, 'validate_product_input_fields_on_add_to_cart' ), 100, 2 );			
			add_filter( 'woocommerce_add_cart_item_data', 			array( $this, 'add_product_input_fields_to_cart_item_data' ), 100, 3 );
			// from session
			add_filter( 'woocommerce_get_cart_item_from_session', 	array( $this, 'get_cart_item_product_input_fields_from_session' ), 100, 3 );
			
			// Show details at cart, order details, emails
			add_filter( 'woocommerce_cart_item_name', 				array( $this, 'add_product_input_fields_to_cart_item_name' ), 100, 3 );			
			add_filter( 'woocommerce_order_item_name', 				array( $this, 'add_product_input_fields_to_order_item_name' ), 100, 2 );						

			// Add item meta from cart to order
			add_action( 'woocommerce_add_order_item_meta', 			array( $this, 'add_product_input_fields_to_order_item_meta' ), 100, 3 );

			// Make nicer name for product input fields in order at backend (shop manager)
			add_action( 'woocommerce_before_order_itemmeta', 		array( $this, 'start_making_nicer_name_for_product_input_fields' ), 100, 3 );        
			add_action( 'woocommerce_after_order_itemmeta', 		array( $this, 'finish_making_nicer_name_for_product_input_fields' ), 100, 3 );        
        }        
    
        // Settings hooks
        add_filter( 'wcj_settings_sections', 						array( $this, 'settings_section' ) );
        add_filter( 'wcj_settings_product_input_fields_global',		array( $this, 'get_settings' ), 100 );
        add_filter( 'wcj_features_status', 							array( $this, 'add_enabled_option' ), 100 );
    }
	
	/**
	 * get_value.
	 */	
	public function get_value( $option_name, $product_id, $default ) {
		return get_option( $option_name, $default );
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
 
            array( 
				'title'    => __( 'Product Input Fields Global Options', 'woocommerce-jetpack' ), 
				'type'     => 'title', 
				'desc'     => __( 'Add custom input fields to product\'s single page for customer to fill before adding product to cart.', 'woocommerce-jetpack' ), 
				'id'       => 'wcj_product_input_fields_global_options', 
			),
            
            array(
                'title'    => __( 'Product Input Fields - All Products', 'woocommerce-jetpack' ),
                'desc'     => '<strong>' . __( 'Enable', 'woocommerce-jetpack' ) . '</strong>',
                'desc_tip' => __( 'Add custom input fields to all products.', 'woocommerce-jetpack' ),
                'id'       => 'wcj_product_input_fields_global_enabled',
                'default'  => 'no',
                'type'     => 'checkbox',
            ),
        
			array(
				'title' 	=> __( 'Product Input Fields Number', 'woocommerce-jetpack' ),
				'desc_tip' 	=> __( 'Click "Save changes" after you change this number.', 'woocommerce-jetpack' ),
				'id' 		=> 'wcj_product_input_fields_global_total_number',
				'default'	=> 1,
				'type' 		=> 'number',
				'desc' 	   => apply_filters( 'get_wc_jetpack_plus_message', '', 'desc' ),
				'custom_attributes'	
						   => apply_filters( 'get_wc_jetpack_plus_message', '', 'readonly' ),					
			),				
        );
		
		$options = $this->get_options();
		for ( $i = 1; $i <= apply_filters( 'wcj_get_option_filter', 1, get_option( 'wcj_product_input_fields_global_total_number', 1 ) ); $i++ ) {		
			foreach( $options as $option ) {
				$settings[] = 
					array(
						'title' 	=> ( 'wcj_product_input_fields_enabled_global_' === $option['id'] ) ? __( 'Product Input Field', 'woocommerce-jetpack' ) . ' #' . $i : '',
						'desc'		=> $option['title'],
						'id' 		=> $option['id'] . $i,
						'default'	=> $option['default'],
						'type' 		=> $option['type'],
						'css'	    => 'width:30%;min-width:300px;',
					);		
			}
		}

		$settings[] = 
			array( 
				'type'     => 'sectionend', 
				'id'       => 'wcj_product_input_fields_global_options',
			);
        
        return $settings;
    }
 
    /**
     * settings_section.
     */
    function settings_section( $sections ) {    
        $sections['product_input_fields_global'] = __( 'Product Input Fields - All Products', 'woocommerce-jetpack' );
        return $sections;
    }    
}
 
endif;

return new WCJ_Product_Input_Fields_Global();

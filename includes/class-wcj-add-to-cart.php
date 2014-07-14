<?php
/**
 * WooCommerce Jetpack Add to cart
 *
 * The WooCommerce Jetpack Add to cart class.
 *
 * @class		WCJ_Add_to_cart
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
 
if ( ! class_exists( 'WCJ_Add_to_cart' ) ) :
 
class WCJ_Add_to_cart {
    
    /**
     * Constructor.
     */
    public function __construct() {
    
        // HOOKS
 
        // Main hooks
        if ( get_option( 'wcj_add_to_cart_enabled' ) == 'yes' ) {
        
			add_filter( 'woocommerce_product_single_add_to_cart_text', array( $this, 'custom_add_to_cart_button_text' ), 100 );
			add_filter( 'woocommerce_product_add_to_cart_text', array( $this, 'custom_add_to_cart_button_text' ), 100 );
			
        }        
    
        // Settings hooks
        add_filter( 'wcj_settings_sections', array( $this, 'settings_section' ) );
        add_filter( 'wcj_settings_add_to_cart', array( $this, 'get_settings' ), 100 );
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
     * custom_add_to_cart_button_text.
     */
    public function custom_add_to_cart_button_text( $add_to_cart_text) {
	
		global $product;
		
		$product_type = $product->product_type;
		
		if ( ! in_array( $product_type, array( 'external', 'grouped', 'simple', 'variable' ) ) )
			$product_type = 'other';
			
		if ( current_filter() == 'woocommerce_product_single_add_to_cart_text' ) {
		
			if ( get_option( 'wcj_add_to_cart_text_enabled_on_single_' . $product_type ) == 'yes' ) return get_option( 'wcj_add_to_cart_text_on_single_' . $product_type );
			else return $add_to_cart_text;
		}		
		else if ( current_filter() == 'woocommerce_product_add_to_cart_text' ) {
		
			if ( get_option( 'wcj_add_to_cart_text_enabled_on_archives_' . $product_type ) == 'yes' ) return get_option( 'wcj_add_to_cart_text_on_archives_' . $product_type );
			else return $add_to_cart_text;
		}
		
		// Default
		return $add_to_cart_text;
    }
    
    /**
     * get_settings.
     */    
    function get_settings() {
	
        $settings = array(
 
				array( 'title' => __( 'Add to Cart Options', 'woocommerce-jetpack' ), 'type' => 'title', 'desc' => '', 'id' => 'wcj_add_to_cart_options' ),
				
				array(
					'title'    => __( 'Add to Cart', 'woocommerce-jetpack' ),
					'desc'     => __( 'Enable the Add to Cart feature', 'woocommerce-jetpack' ),
					'desc_tip' => __( 'Change text for Add to cart button by product type.', 'woocommerce-jetpack' ),
					'id'       => 'wcj_add_to_cart_enabled',
					'default'  => 'yes',
					'type'     => 'checkbox',
				),
				
				//array( 'type' => 'sectionend', 'id' => 'wcj_add_to_cart_options' ),
		);
		
		//ADD TO CART TEXT
		$groups_by_product_type = array(
		
			array(
				'id'		=>		'simple',
				'title'		=>		__( 'Simple product', 'woocommerce-jetpack' ),
				'default'	=>		'Add to cart',
			),
			array(
				'id'		=>		'variable',
				'title'		=>		__( 'Variable product', 'woocommerce-jetpack' ),
				'default'	=>		'Select options',
			),		
			array(
				'id'		=>		'external',
				'title'		=>		__( 'External product', 'woocommerce-jetpack' ),
				'default'	=>		'Buy product',
			),
			array(
				'id'		=>		'grouped',
				'title'		=>		__( 'Grouped product', 'woocommerce-jetpack' ),
				'default'	=>		'View products',
			),
			array(
				'id'		=>		'other',
				'title'		=>		__( 'Other product', 'woocommerce-jetpack' ),
				'default'	=>		'Read more',
			),			
		);		
			
		//$settings[] = array( 'title' => __( 'Add to Cart Options', 'woocommerce-jetpack' ), 'type' => 'title', 'desc' => 'This sets the text on add to cart button by product types.', 'id' => 'wcj_add_to_cart_options' );
		
		foreach ( $groups_by_product_type as $group_by_product_type ) {
		
			$settings[] = 
				array(
					'title'    => $group_by_product_type['title'],
					'id'       => 'wcj_add_to_cart_text_on_single_' . $group_by_product_type['id'],
					'default'  => $group_by_product_type['default'],
					'type'     => 'text',
					'css'      => 'width:30%;min-width:300px;',
				);
				
			$settings[] = 
				array(
					'title'    => '',//$group_by_product_type['title'],
					'desc'     => __( 'Enable on single product pages', 'woocommerce-jetpack' ),
					'id'       => 'wcj_add_to_cart_text_enabled_on_single_' . $group_by_product_type['id'],
					'default'  => 'yes',
					'type'     => 'checkbox',
				);
				
			$settings[] = 
				array(
					'title'    => '',//$group_by_product_type['title'],
					'id'       => 'wcj_add_to_cart_text_on_archives_' . $group_by_product_type['id'],
					'default'  => $group_by_product_type['default'],
					'type'     => 'text',
					'css'      => 'width:30%;min-width:300px;',
					//'custom_attributes' => apply_filters( 'get_wc_jetpack_plus_message', '', 'readonly' ),
				);				
				
			$settings[] = 
				array(
					'title'    => '',//$group_by_product_type['title'],
					'desc'     => __( 'Enable on product archives', 'woocommerce-jetpack' ),
					//'desc_tip' => apply_filters( 'get_wc_jetpack_plus_message', '', 'desc' ),
					'id'       => 'wcj_add_to_cart_text_enabled_on_archives_' . $group_by_product_type['id'],
					'default'  => 'yes',
					'type'     => 'checkbox',
					//'custom_attributes' => apply_filters( 'get_wc_jetpack_plus_message', '', 'disabled' ),
				);
		}
		
		//$settings[] = array( 'type' => 'sectionend', 'id' => 'wcj_add_to_cart_options' );
		$settings[] = array( 'type' => 'sectionend', 'id' => 'wcj_add_to_cart_options' );
         
        

        
        return $settings;
    }
 
    /**
     * settings_section.
     */
    function settings_section( $sections ) {
    
        $sections['add_to_cart'] = 'Add to Cart';
        
        return $sections;
    }    
}
 
endif;
 
return new WCJ_Add_to_cart();

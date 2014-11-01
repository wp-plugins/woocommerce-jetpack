<?php
/**
 * WooCommerce Jetpack Add to cart
 *
 * The WooCommerce Jetpack Add to cart class.
 *
 * @class		WCJ_Add_to_cart
 * @version		1.0.0
 * @category	Class
 * @author 		Algoritmika Ltd.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
 
if ( ! class_exists( 'WCJ_Add_to_cart' ) ) :
 
class WCJ_Add_to_cart {
    
    /**
     * Constructor.
     */
    public function __construct() {
    
        // Main hooks
        if ( get_option( 'wcj_add_to_cart_enabled' ) == 'yes' ) {
        
			if ( get_option( 'wcj_add_to_cart_text_enabled' ) == 'yes' ) {
				add_filter( 'woocommerce_product_single_add_to_cart_text', array( $this, 'custom_add_to_cart_button_text' ), 100 );
				add_filter( 'woocommerce_product_add_to_cart_text', array( $this, 'custom_add_to_cart_button_text' ), 100 );
			}
			
			if ( get_option( 'wcj_add_to_cart_redirect_enabled' ) == 'yes' )
				add_action( 'add_to_cart_redirect', array( $this, 'redirect_to_url' ), 100 );
				
			//add_action( 'admin_head', array( $this, 'hook_javascript' ) );
        }        
    
        // Settings hooks
        add_filter( 'wcj_settings_sections', array( $this, 'settings_section' ) );
        add_filter( 'wcj_settings_add_to_cart', array( $this, 'get_settings' ), 100 );
        add_filter( 'wcj_features_status', array( $this, 'add_enabled_option' ), 100 );
    }
	
    /**
     * hook_javascript.
     *	
	function hook_javascript() {

		//$output='<script> alert("Page is loading..."); </script>';

		//echo $output;

		?><script type="text/javascript">
			function toggle_visibility( class_name, start_element ) {
				
				var elements = document.getElementsByClassName( class_name );
			   
				for ( var i = start_element; i < elements.length; i++ ) {
				
					var e = elements[i];			
			   
					if ( ( e.style.display == '' ) || ( e.style.display == 'table' ) )
						e.style.display = 'none';
					else
						e.style.display = 'table';
				}
		   }
		</script><?php

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
	 * redirect_to_url.
	 */
	function redirect_to_url() {
	
		global $woocommerce;
		$checkout_url = get_option( 'wcj_add_to_cart_redirect_url' );
		if ( $checkout_url === '' ) 
			$checkout_url = $woocommerce->cart->get_checkout_url();
		return $checkout_url;
	}	
    
    /**
     * custom_add_to_cart_button_text.
     */
    public function custom_add_to_cart_button_text( $add_to_cart_text) {
	
		global $woocommerce, $product;
		
		$product_type = $product->product_type;
		
		if ( ! in_array( $product_type, array( 'external', 'grouped', 'simple', 'variable' ) ) )
			$product_type = 'other';
		
		$single_or_archive = '';		
		if ( current_filter() == 'woocommerce_product_single_add_to_cart_text' ) $single_or_archive = 'single';
		else if ( current_filter() == 'woocommerce_product_add_to_cart_text' )  $single_or_archive = 'archives';
		
		if ( $single_or_archive !== '' ) {
		
			if ( get_option( 'wcj_add_to_cart_text_enabled_on_' . $single_or_archive . '_in_cart_' . $product_type ) == 'yes' ) {

				foreach( $woocommerce->cart->get_cart() as $cart_item_key => $values ) {
				
					$_product = $values['data'];
				
					if( get_the_ID() == $_product->id ) {
					
						return get_option( 'wcj_add_to_cart_text_on_' . $single_or_archive . '_in_cart_' . $product_type );
					}
				}
			}
			
			if ( get_option( 'wcj_add_to_cart_text_enabled_on_' . $single_or_archive . '_' . $product_type ) == 'yes' ) return get_option( 'wcj_add_to_cart_text_on_' . $single_or_archive . '_' . $product_type );
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
					'desc_tip' => __( 'Set any url to redirect to on add to cart. Change text for Add to cart button by product type. Display "Already in cart" instead of "Add to cart" button if current product is already in cart.', 'woocommerce-jetpack' ),
					'id'       => 'wcj_add_to_cart_enabled',
					'default'  => 'yes',
					'type'     => 'checkbox',
					/*'custom_attributes'	=> 
								  array( 'onclick' => "toggle_visibility('form-table',1);", ),*/
				),
				
				array( 'type' => 'sectionend', 'id' => 'wcj_add_to_cart_options' ),
		);
		
		//ADD TO CART REDIRECT
        $settings[] = array( 'title' => __( 'Add to Cart Redirect Options', 'woocommerce-jetpack' ), 'type' => 'title', 'desc' => __( 'This section lets you set any url to redirect to after successfully adding product to cart. Leave empty to redirect to checkout page (skipping the cart page).', 'woocommerce-jetpack' ), 'id' => 'wcj_add_to_cart_redirect_options' );
            
		$settings[] = array(
				'title'    => __( 'Redirect', 'woocommerce-jetpack' ),
				'desc'     => __( 'Enable', 'woocommerce-jetpack' ),
				'id'       => 'wcj_add_to_cart_redirect_enabled',
				'default'  => 'no',
				'type'     => 'checkbox',
			);
			
		$settings[] = array(
				'title'    => __( 'Redirect URL', 'woocommerce-jetpack' ),
				'desc_tip' => __( 'Redirect URL. Leave empty to redirect to checkout.', 'woocommerce-jetpack' ),
				'id'       => 'wcj_add_to_cart_redirect_url',
				'default'  => '',
				'type'     => 'text',
				'css'      => 'width:50%;min-width:300px;',
			);
        
        $settings[] = array( 'type'  => 'sectionend', 'id' => 'wcj_add_to_cart_redirect_options' );		
		
		//ADD TO CART TEXT		
		$settings[] = array( 'title' => __( 'Add to Cart Button Text Options', 'woocommerce-jetpack' ), 'type' => 'title', 'desc' => 'This sections lets you set text for add to cart button for various products types and various conditions.', 'id' => 'wcj_add_to_cart_text_options' );
		
		$settings[] = array(
				'title'    => __( 'Add to cart text', 'woocommerce-jetpack' ),
				'desc'     => __( 'Enable', 'woocommerce-jetpack' ),
				'id'       => 'wcj_add_to_cart_text_enabled',
				'default'  => 'no',
				'type'     => 'checkbox',
			);
		
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
				
			if ( $group_by_product_type['id'] === 'external' ) continue;
				
			$settings[] = 
				array(
					'title'    => '',//$group_by_product_type['title'],
					'id'       => 'wcj_add_to_cart_text_on_single_in_cart_' . $group_by_product_type['id'],
					'default'  => 'Already in cart - Add Again?',
					'type'     => 'text',
					'css'      => 'width:30%;min-width:300px;',
					//'custom_attributes' => apply_filters( 'get_wc_jetpack_plus_message', '', 'readonly' ),
					//'desc'	   => apply_filters( 'get_wc_jetpack_plus_message', '', 'desc' ),
					//'desc'	   => __( 'Set text for "Already in cart" on single product pages', 'woocommerce-jetpack' ),
				);
				
			$settings[] = 
				array(
					'title'    => '',//$group_by_product_type['title'],
					'desc'     => __( 'Enable "Already in cart" on single product pages', 'woocommerce-jetpack' ),
					'id'       => 'wcj_add_to_cart_text_enabled_on_single_in_cart_' . $group_by_product_type['id'],
					'default'  => 'yes',
					'type'     => 'checkbox',
				);	
				
			$settings[] = 
				array(
					'title'    => '',//$group_by_product_type['title'],
					'id'       => 'wcj_add_to_cart_text_on_archives_in_cart_' . $group_by_product_type['id'],
					'default'  => 'Already in cart - Add Again?',
					'type'     => 'text',
					'css'      => 'width:30%;min-width:300px;',
					//'custom_attributes' => apply_filters( 'get_wc_jetpack_plus_message', '', 'readonly' ),
				);
				
			$settings[] = 
				array(
					'title'    => '',//$group_by_product_type['title'],
					'desc'     => __( 'Enable "Already in cart" on product archives', 'woocommerce-jetpack' ),
					'id'       => 'wcj_add_to_cart_text_enabled_on_archives_in_cart_' . $group_by_product_type['id'],
					'default'  => 'yes',
					'type'     => 'checkbox',
				);					
		}
		
		$settings[] = array( 'type' => 'sectionend', 'id' => 'wcj_add_to_cart_text_options' );
        
        return $settings;
    }
 
    /**
     * settings_section.
     */
    function settings_section( $sections ) {
    
        $sections['add_to_cart'] = __( 'Add to Cart', 'woocommerce-jetpack' );
        
        return $sections;
    }    
}
 
endif;
 
return new WCJ_Add_to_cart();

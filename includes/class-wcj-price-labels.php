<?php
/**
 * WooCommerce Jetpack Price Labels
 *
 * The WooCommerce Jetpack Price Labels class.
 *
 * @class 		WCJ_Price_Labels
 * @category	Class
 * @author 		Algoritmika Ltd.
 */
 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'WCJ_Price_Labels' ) ) :

class WCJ_Price_Labels {
	
	/**
	 * Constructor.
	 */	
	public function __construct() {
	
		// HOOKS
		// Main hooks
		// Custom Price Labels hooks
		add_action( 'add_meta_boxes', array( $this, 'add_price_label_meta_box' ) );
		// Custom Price Labels - cart item price hooks
		//add_filter( 'woocommerce_cart_item_price_html', array( $this, 'custom_price' ), 999, 2 ); // depreciated?
		//add_filter( 'woocommerce_cart_item_price', array( $this, 'custom_price' ), 999, 2 );		
		
		// Custom Price Labels - price hooks

		//add_filter( 'woocommerce_get_price_html', array( $this, 'custom_price' ), 100, 2 );
		
		add_filter( 'woocommerce_empty_price_html', array( $this, 'custom_price' ), 100, 2 );		
		add_filter( 'woocommerce_free_price_html', array( $this, 'custom_price' ), 100, 2 );		
		add_filter( 'woocommerce_free_sale_price_html', array( $this, 'custom_price' ), 100, 2 );
		add_filter( 'woocommerce_price_html', array( $this, 'custom_price' ), 100, 2 );
		add_filter( 'woocommerce_sale_price_html', array( $this, 'custom_price' ), 100, 2 );
		// Custom Price Labels - price hooks
		add_filter( 'woocommerce_grouped_price_html', array( $this, 'custom_price' ), 100, 2 );	
		// Custom Price Labels - price hooks
		add_filter( 'woocommerce_variable_empty_price_html', array( $this, 'custom_price' ), 100, 2 );		
		add_filter( 'woocommerce_variable_free_price_html', array( $this, 'custom_price' ), 100, 2 );		
		add_filter( 'woocommerce_variable_free_sale_price_html', array( $this, 'custom_price' ), 100, 2 );
		add_filter( 'woocommerce_variable_price_html', array( $this, 'custom_price' ), 100, 2 );		
		add_filter( 'woocommerce_variable_sale_price_html', array( $this, 'custom_price' ), 100, 2 );			
		// Custom Price Labels - price hooks
		add_filter( 'woocommerce_variation_empty_price_html', array( $this, 'custom_price' ), 100, 2 );		
		add_filter( 'woocommerce_variation_free_price_html', array( $this, 'custom_price' ), 100, 2 );
		//woocommerce_variation_option_name
		add_filter( 'woocommerce_variation_price_html', array( $this, 'custom_price' ), 100, 2 );		
		add_filter( 'woocommerce_variation_sale_price_html', array( $this, 'custom_price' ), 100, 2 );
		// Custom Price Labels - save post hook
		add_action( 'save_post', array( $this, 'save_custom_price_labels' ), 999, 2 );
	
		// Settings hooks
		add_filter( 'wcj_settings_sections', array( $this, 'settings_section' ) );
		add_filter( 'wcj_settings_price_labels', array( $this, 'get_settings' ), 100 );
		add_filter( 'wcj_features_status', array( $this, 'add_enabled_option' ), 100 );
		
		// Custom Price Labels - fields array
		$this->custom_tab_group_name = 'wcj_price_labels';// for compatibility with Custom Price Label Pro plugin should use 'simple_is_custom_pricing_label'		
		$this->custom_tab_sections = array ( '_instead', '_before', '_between', '_after', );
		$this->custom_tab_sections_titles = array ( 
			'_instead'	=> 'Instead of the price',// for compatibility with Custom Price Label Pro plugin should use ''	 
			'_before'	=> 'Before the price', 
			'_between'	=> 'Between the regular and sale price', 
			'_after'	=> 'After the price', 
		);
		$this->custom_tab_section_variations = array ( '_text', '_enabled', '_home', '_products', '_single', );
		$this->custom_tab_section_variations_titles = array ( 
			'_text'		 => 'The label', 
			'_enabled'	 => 'Enable',// for compatibility with Custom Price Label Pro plugin should use ''	  
			'_home'		 => 'Hide on home page', 
			'_products'	 => 'Hide on products page', 
			'_single'	 => 'Hide on single',
		);
	}
	
	/*public function custom_price1( $price, $product ) {	
		echo '[' . $price . ']';
		return $price;
	}*/
	
	/**
	 * add_enabled_option.
	 */
	public function add_enabled_option( $settings ) {
	
		$all_settings = $this->get_settings();
		$settings[] = $all_settings[1];
		
		return $settings;
	}
	
	public function save_custom_price_labels( $post_id, $post ) {
		
		//$product = get_product( $post );TODO - do I need it?
		
		foreach ( $this->custom_tab_sections as $custom_tab_section ) {			
		
			foreach ( $this->custom_tab_section_variations as $custom_tab_section_variation ) {
			
				//$option_name = $this->custom_tab_group_name;
				$option_name = $this->custom_tab_group_name . $custom_tab_section . $custom_tab_section_variation;
				
				if ( $custom_tab_section_variation == '_text' ) {
					//$option_name .= $custom_tab_section_variation . $custom_tab_section;
					if ( isset( $_POST[ $option_name ] ) ) update_post_meta( $post_id, '_' . $option_name, $_POST[ $option_name ] );
				}
				else {	
					//$option_name .= $custom_tab_section . $custom_tab_section_variation;			
					if ( isset( $_POST[ $option_name ] ) ) update_post_meta( $post_id, '_' . $option_name, $_POST[ $option_name ] );
					else update_post_meta( $post_id, '_' . $option_name, 'off' );			
				}
			}
		}
	}	

	public function add_price_label_meta_box() {
	
		add_meta_box( 'wc-jetpack-price-labels', 'Custom Price Labels', array($this, 'wcj_price_label'), 'product', 'normal', 'high' );
	}	
	
	/*
	 * back end
	 */
	public function wcj_price_label() {
	
		$current_post_id = get_the_ID();
		
		foreach ( $this->custom_tab_sections as $custom_tab_section ) {
		
			if ( $custom_tab_section == '_before' ) $disabled_if_no_plus = apply_filters( 'get_wc_jetpack_plus_message', '', 'desc_below' );
			else $disabled_if_no_plus = '';
		
			echo '<p>' . $disabled_if_no_plus . '<ul><strong>' . $this->custom_tab_sections_titles[ $custom_tab_section ] . '</strong>';
		
			foreach ( $this->custom_tab_section_variations as $custom_tab_section_variation ) {
			
				//$option_name = $this->custom_tab_group_name;
				$option_name = $this->custom_tab_group_name . $custom_tab_section . $custom_tab_section_variation;
				
				if ( $custom_tab_section_variation == '_text' ) {
				
					//$option_name .= $custom_tab_section_variation . $custom_tab_section;
					
					if ( $custom_tab_section != '_instead' ) $disabled_if_no_plus = apply_filters( 'get_wc_jetpack_plus_message', '', 'readonly_string' );
					else $disabled_if_no_plus = '';			
					//if ( $disabled_if_no_plus != '' ) $disabled_if_no_plus = 'readonly';
					
					$label_text = get_post_meta($current_post_id, '_' . $option_name, true );
					$label_text = str_replace ( '"', '&quot;', $label_text );					
					
					echo '<li>' . $this->custom_tab_section_variations_titles[ $custom_tab_section_variation ] . ' <input style="width:50%;min-width:300px;" type="text" ' . $disabled_if_no_plus . ' name="' . $option_name . '" id="' . $option_name . '" value="' . $label_text . '" /></li>';
					
				}
				else { 
				
					//$option_name .= $custom_tab_section . $custom_tab_section_variation;
					
					if ( $custom_tab_section != '_instead' ) $disabled_if_no_plus = apply_filters( 'get_wc_jetpack_plus_message', '', 'disabled_string' );
					else $disabled_if_no_plus = '';
					//if ( $disabled_if_no_plus != '' ) $disabled_if_no_plus = 'disabled';
		
					echo '<li><input class="checkbox" type="checkbox" ' . $disabled_if_no_plus . ' name="' . $option_name . '" id="' . $option_name . '" ' . 
						checked( get_post_meta($current_post_id, '_' . $option_name, true), 'on', false ) . ' /> ' . $this->custom_tab_section_variations_titles[ $custom_tab_section_variation ] . '</li>';
				}
			}

			echo '</ul></p>';
		}
	}
	
	public function customize_price( $price, $custom_tab_section, $custom_label ) {

		switch ( $custom_tab_section ) {
		
			case '_instead': 
				$price = $custom_label;
				break;
				
			case '_before': 
				$price = apply_filters( 'wcjpc_filter', $price, $custom_label . $price );
				break;
				
			case '_between': 
				$price = apply_filters( 'wcjpc_filter', $price, str_replace( '</del> <ins>', '</del>' . $custom_label . '<ins>', $price ) );
				break;
				
			case '_after':
				$price = apply_filters( 'wcjpc_filter', $price, $price . $custom_label );
				break;
		}	
	
		return $price;
	}

	/*
	 * front end
	 */	
	public function custom_price( $price, $product ) {	
	
		foreach ( $this->custom_tab_sections as $custom_tab_section ) {
		
			$labels_array = array();
		
			foreach ( $this->custom_tab_section_variations as $custom_tab_section_variation ) {
		
				//$option_name = $this->custom_tab_group_name;
				$option_name = $this->custom_tab_group_name . $custom_tab_section . $custom_tab_section_variation;
				$labels_array[ 'variation' . $custom_tab_section_variation ] = get_post_meta($product->post->ID, '_' . $option_name, true );
				
				/*if ( $custom_tab_section_variation == '_text' ) {
				
					//$option_name .= $custom_tab_section_variation . $custom_tab_section;					
					$labels_array[ 'variation' . $custom_tab_section_variation ] = get_post_meta($product->post->ID, '_' . $option_name, true );
				}
				else { 
				
					//$option_name .= $custom_tab_section . $custom_tab_section_variation;					
					$labels_array[ 'variation' . $custom_tab_section_variation ] = get_post_meta($product->post->ID, '_' . $option_name, true);
				}*/
				
				//$price .= print_r( $labels_array );
			}
			
			
			
			if ( $labels_array[ 'variation_enabled' ] == 'on' ) {
			
				if ( ( ( $labels_array['variation_home'] 	 == 'off' ) && ( is_front_page() ) ) ||
					 ( ( $labels_array['variation_products'] == 'off' ) && ( is_archive() ) ) ||
					 ( ( $labels_array['variation_single'] 	 == 'off' ) && ( is_single() ) ) )
						$price = $this->customize_price( $price, $custom_tab_section, $labels_array['variation_text'] );
			}
			
			//unset( $labels_array );
		}
		
		return $price;
	}
	
	function get_settings() {

		$settings = array(

			array(	'title' => __( 'Custom Price Labels Options', 'woocommerce-jetpack' ), 'type' => 'title', 'desc' => '', 'id' => 'wcj_price_labels_options' ),
			
			array(
				'title' 	=> __( 'Custom Price Labels', 'woocommerce-jetpack' ),
				'desc' 		=> __( 'Enable the Custom Price Labels feature', 'woocommerce-jetpack' ),
				'desc_tip'	=> __( 'Create any custom price label for any product.', 'woocommerce-jetpack' ),
				'id' 		=> 'wcj_price_labels_enabled',
				'default'	=> 'yes',
				'type' 		=> 'checkbox',
			),
		
			array( 'type' 	=> 'sectionend', 'id' => 'wcj_price_labels_options' ),
		);
		
		return $settings;
	}
	
	function settings_section( $sections ) {
	
		$sections['price_labels'] = 'Custom Price Labels';
		
		return $sections;
	}	
}

endif;

return new WCJ_Price_Labels();

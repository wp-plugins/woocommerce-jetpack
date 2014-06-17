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
	
	public function __construct() {
	
		//HOOKS
		
		// Main hooks
		if ( get_option( 'wcj_product_info_enabled' ) == 'yes' ) {
		
			add_action( 'woocommerce_single_product_summary', array( $this, 'print_total_sales' ), 999);	
			//add_action( 'woocommerce_after_single_product', array( $this, 'print_total_sales' ) );
		}
		
		// Settings hooks
		add_filter( 'wcj_settings_sections', array( $this, 'settings_section' ) );
		add_filter( 'wcj_settings_product_info', array( $this, 'get_settings' ), 100 );
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
	
	function print_total_sales() {
	
		echo str_replace( '[TOTALSALES]', get_post_custom( get_the_ID() )['total_sales'][0], get_option( 'wcj_product_info_total_sales_text' ) );
	}
	
	function get_settings() {

		$settings = array(
		
			array( 'title' 	=> __( 'Product Info Options', 'woocommerce-jetpack' ), 'type' => 'title', 'desc' => '', 'id' => 'wcj_product_info_options' ),
			
			array(
				'title' 	=> __( 'Product Info', 'woocommerce-jetpack' ),
				'desc' 		=> __( 'Enable the Product Info feature', 'woocommerce-jetpack' ),
				'desc_tip'	=> __( 'Display total product sales etc.', 'woocommerce-jetpack' ),
				'id' 		=> 'wcj_product_info_enabled',
				'default'	=> 'yes',
				'type' 		=> 'checkbox'
			),
			
			array( 'type' 	=> 'sectionend', 'id' => 'wcj_product_info_options' ),
		
		
			array( 'title' 	=> __( 'Total Sales Options', 'woocommerce-jetpack' ), 'type' => 'title', 'desc' => '', 'id' => 'wcj_product_info_total_sales_options' ),
		
			array(
				'title' 	=> __( 'Enable', 'woocommerce-jetpack' ),
				'desc' 		=> __( 'Enable Total Sales', 'woocommerce-jetpack' ),
				//'desc_tip'=> __( 'Display total product sales etc.', 'woocommerce-jetpack' ),
				'id' 		=> 'wcj_product_info_total_sales_enabled',
				'default'	=> 'no',
				'type' 		=> 'checkbox'
			),		
		
			array(
				'title' 	=> __( 'Text to Show', 'woocommerce-jetpack' ),
				'desc_tip' 	=> __( 'This sets the text to output for total sales. Default is "Total Sales: [TOTALSALES]"', 'woocommerce-jetpack' ),
				'desc'     	=> apply_filters( 'get_wc_jetpack_plus_message', '', 'desc' ),
				'id' 		=> 'wcj_product_info_total_sales_text',
				'default'	=> __( 'Total Sales: [TOTALSALES]', 'woocommerce-jetpack' ),
				'type' 		=> 'textarea',
				'css'		=> 'width:50%;min-width:300px;',
				'custom_attributes'	
							=> apply_filters( 'get_wc_jetpack_plus_message', '', 'readonly' ),
			),
				
			array( 'type' 	=> 'sectionend', 'id' => 'wcj_product_info_total_sales_options' ),
			
			/*array(
				'title' 	=> __( 'Show on Single Product', 'woocommerce-jetpack' ),
				//'desc_tip'=> __( 'Check to show single products page', 'woocommerce-jetpack' ),
				'id' 		=> 'wcj_product_info_show_on_single',
				'default'	=> 'yes',
				'type' 		=> 'checkbox',
			),

			array(
				'title' 	=> __( 'Show on Products Archive', 'woocommerce-jetpack' ),
				'id' 		=> 'wcj_product_info_show_on_archive',
				'default'	=> 'yes',
				'type' 		=> 'checkbox',
			),

			array(
				'title' 	=> __( 'Show on Home Page', 'woocommerce-jetpack' ),
				'id' 		=> 'wcj_product_info_show_on_home',
				'default'	=> 'yes',
				'type' 		=> 'checkbox',
			),*/
		);
		
		return $settings;
	}
	
	function settings_section( $sections ) {
	
		$sections['product_info'] = 'Product Info';
		
		return $sections;
	}	
}

endif;

return new WCJ_Product_Info();

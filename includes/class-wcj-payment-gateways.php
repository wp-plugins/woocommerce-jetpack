<?php
/**
 * WooCommerce Jetpack Payment Gateways
 *
 * The WooCommerce Jetpack Payment Gateways class.
 *
 * @class       WCJ_Payment_Gateways
 * @version		1.0.2
 * @category	Class
 * @author 		Algoritmika Ltd. 
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
 
if ( ! class_exists( 'WCJ_Payment_Gateways' ) ) :
 
class WCJ_Payment_Gateways {

	public $woocommerce_icon_filters = array(
		'woocommerce_cod_icon' 				=> 'COD',
		'woocommerce_cheque_icon' 			=> 'Cheque',
		'woocommerce_bacs_icon' 			=> 'BACS',
		'woocommerce_mijireh_checkout_icon' => 'Mijireh Checkout', //depreciated?
		'woocommerce_paypal_icon' 			=> 'PayPal',
		//'woocommerce_wcj_custom_icon' 		=> 'WooJetpack Custom',
	);
    
    /**
     * Constructor.
     */
    public function __construct() {       
        if ( get_option( 'wcj_payment_gateways_enabled' ) == 'yes' ) {
			// Include custom payment gateway 
			include_once( 'gateways/class-wc-gateway-wcj-custom.php' );  
			
			// Main hooks
			// Icons for default WooCommerce methods hooks
			/*$this->woocommerce_icon_filters = array (
				'woocommerce_cod_icon' 				=> __( 'COD', 'woocommerce-jetpack' ),
				'woocommerce_cheque_icon' 			=> __( 'Cheque', 'woocommerce-jetpack' ),
				'woocommerce_bacs_icon' 			=> __( 'BACS', 'woocommerce-jetpack' ),
				'woocommerce_mijireh_checkout_icon' => __( 'Mijireh Checkout', 'woocommerce-jetpack' ),
				'woocommerce_paypal_icon' 			=> __( 'PayPal', 'woocommerce-jetpack' ),
				//'woocommerce_wcj_custom_icon' 		=> __( 'WooJetpack Custom', 'woocommerce-jetpack' ),
			);*/
			foreach ( $this->woocommerce_icon_filters as $filter_name => $filter_title )
				add_filter( $filter_name, array( $this, 'set_icon' ) );
				
			// Settings
			add_filter( 'woocommerce_payment_gateways_settings', array( $this, 'add_woocommerce_icons_options' ), 100 );					
        }        
    
        // Settings hooks
        add_filter( 'wcj_settings_sections', array( $this, 'settings_section' ) );
        add_filter( 'wcj_settings_payment_gateways', array( $this, 'get_settings' ), 100 );
        add_filter( 'wcj_features_status', array( $this, 'add_enabled_option' ), 100 );
    }
	
    /**
     * add_enabled_option.
     */	
	function add_woocommerce_icons_options( $settings ) {

        $settings[] = array( 'title' => __( 'WooCommerce Jetpack: Default WooCommerce Payment Gateways Options', 'woocommerce-jetpack' ), 'type' => 'title', 'desc' => __( 'If you want to show an image next to the gateway\'s name on the frontend, enter a URL to an image.', 'woocommerce-jetpack' ), 'id' => 'wcj_payment_gateways_icons_options' );
        
		foreach ( $this->woocommerce_icon_filters as $filter_name => $filter_title ) {		
			// Prepare current value
			$desc = '';
			$icon_url = apply_filters( $filter_name, '' );
			if ( $icon_url !== '' )
				$desc = '<img src="' . $icon_url . '" alt="' . $filter_title . '" title="' . $filter_title . '" />';
				//$desc = __( 'Current Icon: ', 'woocommerce-jetpack' ) . '<img src="' . $icon_url . '" alt="' . $filter_title . '" title="' . $filter_title . '" />';
				
			$settings[] = array(
					'title'    	=> $filter_title,
					//'title'   => sprintf( __( 'Icon for %s payment gateway', 'woocommerce-jetpack' ), $filter_title ),
					'desc'    	=> $desc,
					//'desc_tip'	=> $filter_name,
					'id'       	=> 'wcj_payment_gateways_icons_' . $filter_name,
					'default'  	=> '',
					'type'		=> 'text',
					'css'    	=> 'min-width:300px;width:50%;',
				);
		}
        
        $settings[] = array( 'type'  => 'sectionend', 'id' => 'wcj_payment_gateways_icons_options' );
	  
		return $settings;
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
	 * set_icon
	 */
	public function set_icon( $value ) {
		$icon_url = get_option( 'wcj_payment_gateways_icons_' . current_filter(), '' );
		if ( $icon_url === '' )
			return $value;
		return $icon_url;
	}		
    
    /**
     * get_settings.
     */    
    function get_settings() {
 
        $settings = array(
 
            array( 'title' => __( 'Payment Gateways Options', 'woocommerce-jetpack' ), 'type' => 'title', 'desc' => __( '', 'woocommerce-jetpack' ), 'id' => 'wcj_payment_gateways_options' ),
            
            array(
                'title'    => __( 'Payment Gateways', 'woocommerce-jetpack' ),
                'desc'     => __( 'Enable the Payment Gateways feature', 'woocommerce-jetpack' ),
                'desc_tip' => __( 'Add custom payment gateway, change icons (images) for all default WooCommerce payment gateways.', 'woocommerce-jetpack' ),
                'id'       => 'wcj_payment_gateways_enabled',
                'default'  => 'yes',
                'type'     => 'checkbox',
            ),
        
            array( 'type'  => 'sectionend', 'id' => 'wcj_payment_gateways_options' ),
			
            array( 'title' => __( 'Custom Payment Gateways Options', 'woocommerce-jetpack' ), 'type' => 'title', 'desc' => __( '', 'woocommerce-jetpack' ), 'id' => 'wcj_custom_payment_gateways_options' ),
            
            array(
                'title'    => __( 'Number of Gateways', 'woocommerce-jetpack' ),
                'desc' 	   => apply_filters( 'get_wc_jetpack_plus_message', '', 'desc' ),
                'desc_tip' => __( 'Number of custom payments gateways to be added. All settings for each new gateway are in WooCommerce > Settings > Checkout.', 'woocommerce-jetpack' ),
                'id'       => 'wcj_custom_payment_gateways_number',
                'default'  => 1,
                'type'     => 'number',
				'custom_attributes' => array(
					'min'  => 1,
					'step' => 1,
					'max'  => apply_filters( 'wcj_get_option_filter', 1, 10 ),
				)				
            ),
        
            array( 'type'  => 'sectionend', 'id' => 'wcj_custom_payment_gateways_options' ),			
		);
			
        $settings[] = array( 'title' => __( 'Default WooCommerce Payment Gateways Options', 'woocommerce-jetpack' ), 'type' => 'title', 'desc' => __( 'If you want to show an image next to the gateway\'s name on the frontend, enter a URL to an image.', 'woocommerce-jetpack' ), 'id' => 'wcj_payment_gateways_icons_options' );
        
		foreach ( $this->woocommerce_icon_filters as $filter_name => $filter_title ) {		
			// Prepare current value
			$desc = '';
			$icon_url = apply_filters( $filter_name, '' );
			if ( $icon_url !== '' )
				$desc = '<img src="' . $icon_url . '" alt="' . $filter_title . '" title="' . $filter_title . '" />';
				//$desc = __( 'Current Icon: ', 'woocommerce-jetpack' ) . '<img src="' . $icon_url . '" alt="' . $filter_title . '" title="' . $filter_title . '" />';
				
			$settings[] = array(
					'title'    	=> $filter_title,
					//'title'   => sprintf( __( 'Icon for %s payment gateway', 'woocommerce-jetpack' ), $filter_title ),
					'desc'    	=> $desc,
					//'desc_tip'	=> $filter_name,
					'id'       	=> 'wcj_payment_gateways_icons_' . $filter_name,
					'default'  	=> '',
					'type'		=> 'text',
					'css'    	=> 'min-width:300px;width:50%;',
				);
		}
        
        $settings[] = array( 'type'  => 'sectionend', 'id' => 'wcj_payment_gateways_icons_options' );			
        
        return $settings;
    }
 
    /**
     * settings_section.
     */
    function settings_section( $sections ) {
    
        $sections['payment_gateways'] = __( 'Payment Gateways', 'woocommerce-jetpack' );
        
        return $sections;
    }    
}
 
endif;
 
return new WCJ_Payment_Gateways();

<?php
/**
 * WooCommerce Jetpack Settings
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'WC_Settings_Jetpack' ) ) :

/**
 * WC_Settings_Jetpack
 */
class WC_Settings_Jetpack extends WC_Settings_Page {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->id    = 'jetpack';
		$this->label = __( 'Jetpack', 'woocommerce-jetpack' );

		add_filter( 'woocommerce_settings_tabs_array', array( $this, 'add_settings_page' ), 20 );
		add_action( 'woocommerce_settings_' . $this->id, array( $this, 'output' ) );
		add_action( 'woocommerce_settings_save_' . $this->id, array( $this, 'save' ) );
		add_action( 'woocommerce_sections_' . $this->id, array( $this, 'output_sections' ) );
	}

	/**
	 * Get sections
	 *
	 * @return array
	 */
	public function get_sections() {
	
		return apply_filters( 'wcj_settings_sections', array(
			''	=> __( 'Dashboard', 'woocommerce-jetpack' ),
		) );
	}

	/**
	 * Output the settings
	 */
	public function output() {
	
		global $current_section;

		$settings = $this->get_settings( $current_section );

 		WC_Admin_Settings::output_fields( $settings );
	}

	/**
	 * Save settings
	 */
	public function save() {
	
		global $current_section;

		$settings = $this->get_settings( $current_section );
		WC_Admin_Settings::save_fields( $settings );
		
		echo apply_filters('get_wc_jetpack_plus_message', '', 'global' );
	}
	
	/**
	 * Get settings array
	 *
	 * @return array
	 */
	public function get_settings( $current_section = '' ) {

		if ( $current_section != '' ) {
			
			return apply_filters('wcj_settings_' . $current_section, array() );
		}
		else {

			$settings[] = array( 'title' => __( 'Features', 'woocommerce-jetpack' ), 'type' => 'title', 'desc' => $desc, 'id' => 'wcj_options' );
			
			$settings = apply_filters( 'wcj_features_status', $settings );		
			
			/*$statuses = array();		
			$statuses[] = include_once( 'class-wcj-price-labels.php' );
			$statuses[] = include_once( 'includes/class-wcj-call-for-price.php' );
			$statuses[] = include_once( 'includes/class-wcj-currencies.php' );		
			$statuses[] = include_once( 'includes/class-wcj-sorting.php' );
			$statuses[] = include_once( 'includes/class-wcj-old-slugs.php' );
			$statuses[] = include_once( 'includes/class-wcj-product-info.php' );
			foreach ( $statuses as $section )
				$settings[] = $section->get_statuses()[1];*/
			
			$settings[] = array( 'type' => 'sectionend', 'id' => 'wcj_options' );
				
			return $settings;//apply_filters('wcj_general_settings', $settings );
		}
	}
}

endif;

return new WC_Settings_Jetpack();

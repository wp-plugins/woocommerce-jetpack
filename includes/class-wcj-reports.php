<?php
/**
 * WooCommerce Jetpack Reports
 *
 * The WooCommerce Jetpack Reports class.
 *
 * @class 		WCJ_Reports
 * @version		2.0.0
 * @category	Class
 * @author 		Algoritmika Ltd.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WCJ_Reports' ) ) :

class WCJ_Reports {

	/** @var string Report ID. */
	public $report_id;	

	/** @var int Stock reports - range in days. */
	public $range_days;	

	/** @var string: yes/no Customers reports - group countries. */
	public $group_countries;	

	/**
	 * Constructor.
	 */
	public function __construct() {
		
		// Main hooks
		if ( 'yes' === get_option( 'wcj_reports_enabled' ) ) {
			if ( is_admin() ) {
				add_filter( 'woocommerce_admin_reports', 		array( $this, 'add_customers_by_country_report' ) );	
				add_filter( 'woocommerce_admin_reports', 		array( $this, 'add_stock_reports' ) );	
				add_action( 'init',						 		array( $this, 'catch_arguments' ) );	
				
				include_once( 'reports/wcj-class-reports-customers.php' );
				include_once( 'reports/wcj-class-reports-stock.php' );
			}
		}
		
		// Settings hooks
		add_filter( 'wcj_settings_sections', 					array( $this, 'settings_section' ) ); 			// Add section to WooCommerce > Settings > Jetpack
		add_filter( 'wcj_settings_reports', 					array( $this, 'get_settings' ),       100 );    // Add the settings
		add_filter( 'wcj_features_status', 						array( $this, 'add_enabled_option' ), 100 );	// Add Enable option to Jetpack Settings Dashboard		
	}
	
	/**
	 * catch_arguments.
	 */	
	public function catch_arguments() {
		$this->report_id       = isset( $_GET['report'] )                             ? $_GET['report'] : 'on_stock';
		$this->range_days      = isset( $_GET['period'] )                             ? $_GET['period'] : 30;
		$this->group_countries = ( 'customers_by_country_sets' === $this->report_id ) ? 'yes'           : 'no';
	}	
	
	/**
	 * get_report_stock.
	 */		
	public function get_report_stock() {
		$report = new WCJ_Reports_Stock( array ( 
			'report_id'  => $this->report_id, 
			'range_days' => $this->range_days, 
		) );
		echo $report->get_report_html();		
	}	
	
	/**
	 * get_report_customers.
	 */		
	public function get_report_customers() {
		$report = new WCJ_Reports_Customers( array ( 'group_countries' => $this->group_countries ) );
		echo $report->get_report();
	}
	
	/**
	 * Add reports to WooCommerce > Reports > Stock
	 */
	public function add_stock_reports( $reports ) {
	
		$reports['stock']['reports']['on_stock'] = array(
			'title'       => __( 'WooJetpack: All in stock', 'woocommerce-jetpack' ),
			'description' => '',
			'hide_title'  => true,
			'callback'    => array( $this, 'get_report_stock' ),
		);
		
		$reports['stock']['reports']['understocked'] = array(
			'title'       => __( 'WooJetpack: Understocked', 'woocommerce-jetpack' ),
			'description' => '',
			'hide_title'  => true,
			'callback'    => array( $this, 'get_report_stock' ),
		);		
		
		return $reports;
	}	
	
	/**
	 * Add reports to WooCommerce > Reports > Customers
	 */
	public function add_customers_by_country_report( $reports ) {
		
		$reports['customers']['reports']['customers_by_country'] = array(
			'title'       => __( 'WooJetpack: Customers by Country', 'woocommerce-jetpack' ),
			'description' => '',
			'hide_title'  => true,
			'callback'    => array( $this, 'get_report_customers' ),
		);		
		
		$reports['customers']['reports']['customers_by_country_sets'] = array(
			'title'       => __( 'WooJetpack: Customers by Country Sets', 'woocommerce-jetpack' ),
			'description' => '',
			'hide_title'  => true,
			'callback'    => array( $this, 'get_report_customers' ),
		);
		
		return $reports;
	}
	
	/**
	 * Add Enable option to Jetpack Settings Dashboard.
	 */
	public function add_enabled_option( $settings ) {
		$all_settings = $this->get_settings();
		$settings[] = $all_settings[1];
		return $settings;
	}

	/*
	 * Add the settings.
	 */
	function get_settings() {

		$settings = array(

			array( 'title' 	=> __( 'Reports Options', 'woocommerce-jetpack' ), 'type' => 'title', 'desc' => '', 'id' => 'wcj_reports_options' ),

			array(
				'title' 	=> __( 'Reports', 'woocommerce-jetpack' ),
				'desc' 		=> __( 'Enable the Reports feature', 'woocommerce-jetpack' ),
				'desc_tip' 	=> __( 'Stock, sales, customers etc. reports.', 'woocommerce-jetpack' ),
				'id' 		=> 'wcj_reports_enabled',
				'default'	=> 'yes',
				'type' 		=> 'checkbox'
			),

			array( 'type' 	=> 'sectionend', 'id' => 'wcj_reports_options' ),
			
			array( 
				'title' 	=> __( 'Available Reports', 'woocommerce-jetpack' ),
				'type' 		=> 'title', 
				'desc' 		=> '<p>'
							   . __( 'WooJetpack: Customers by Country. Available in WooCommerce > Reports > Customers.', 'woocommerce-jetpack' )
							   . '</p><p>'
							   . __( 'WooJetpack: Customers by Country Sets. Available in WooCommerce > Reports > Customers.', 'woocommerce-jetpack' )
							   . '</p><p>'							   
							   . __( 'WooJetpack: All in Stock with sales data. Available in WooCommerce > Reports > Stock.', 'woocommerce-jetpack' )
							   . '</p><p>'
							   . __( 'WooJetpack: Understocked products (calculated by sales data). Available in WooCommerce > Reports > Stock.', 'woocommerce-jetpack' )
							   . '</p>',
				'id' 		=> 'wcj_reports_more_options' 
			),

			array( 'type' 	=> 'sectionend', 'id' => 'wcj_reports_more_options' ),			
		);

		return $settings;
	}

	/*
	 * Add settings section to WooCommerce > Settings > Jetpack.
	 */
	function settings_section( $sections ) {
		$sections['reports'] = __( 'Reports', 'woocommerce-jetpack' );
		return $sections;
	}	
}

endif;

return new WCJ_Reports();

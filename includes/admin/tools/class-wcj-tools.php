<?php
/**
 * WooCommerce Jetpack Tools
 *
 * The WooCommerce Jetpack Tools class.
 *
 * @class 		WCJ_Tools
 * @version		1.0.0
 * @category	Class
 * @author 		Algoritmika Ltd.
 */
 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'WCJ_Tools' ) ) :

class WCJ_Tools {
	
	public function __construct() {
		if ( is_admin() ) {		
			add_action( 'admin_menu', array($this, 'add_wcj_tools'), 100 );
		}
	}
	
	public function add_wcj_tools() {	
		add_submenu_page( 'woocommerce', 'WooCommerce Jetpack Tools', 'Jetpack Tools', 'manage_options', 'wcj-tools', array( $this, 'create_tools_page' ) );
	}
	
	public function create_tools_page() {
	
		$tabs = array(
			array( 
				'id'		=> 'dashboard',
				'title'		=> __( 'Tools Dashboard', 'woocommerce-jetpack' ),
				//'desc'		=> __( 'WooCommerce Jetpack Tools Dashboard', 'woocommerce-jetpack' ),
			),
		);
		
		$tabs = apply_filters( 'wcj_tools_tabs', $tabs );
		
		$html = '<h2 class="nav-tab-wrapper woo-nav-tab-wrapper">';
		
		$active_tab = 'dashboard';
		if ( isset( $_GET['tab'] ) )
			$active_tab = $_GET['tab'];
			
		foreach ( $tabs as $tab ) {
		
			$is_active = '';
			if ( $active_tab === $tab['id'] )
				$is_active = 'nav-tab-active';
				
			//$html .= '<a href="' . get_admin_url() . 'admin.php?page=wcj-tools&tab=' . $tab['id'] . '" class="nav-tab ' . $is_active . '">' . $tab['title'] . '</a>';
			//$html .= '<a href="' . add_query_arg( 'tab', $tab['id'] ) . '" class="nav-tab ' . $is_active . '">' . $tab['title'] . '</a>';
			$html .= '<a href="admin.php?page=wcj-tools&tab=' . $tab['id'] . '" class="nav-tab ' . $is_active . '">' . $tab['title'] . '</a>';
			
		}			
		$html .= '</h2>';
		
		echo $html;
		
		do_action( 'wcj_tools_' . $active_tab );		
	}	
}

endif;

return new WCJ_Tools();

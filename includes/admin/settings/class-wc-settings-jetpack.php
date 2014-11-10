<?php
/**
 * WooCommerce Jetpack Settings
 *
 * The WooCommerce Jetpack Settings class.
 *
 * @class       WC_Settings_Jetpack
 * @version		1.1.0
 * @category	Class
 * @author 		Algoritmika Ltd.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'WC_Settings_Jetpack' ) ) :

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
	 * active.
	 */
	public function active( $active ) {
		if ( 'yes' === $active ) return 'active';
		else return 'inactive';
	}	

	/**
	 * Output the settings.
	 */
	public function output() {
	
		global $current_section;

		$settings = $this->get_settings( $current_section );
		
		if ( '' != $current_section )
			WC_Admin_Settings::output_fields( $settings );		
		else {
		
			$the_settings = $this->get_settings();
			
			echo '<h3>' . $the_settings[0]['title'] . '</h3>';
			echo '<p>' . $the_settings[0]['desc'] . '</p>';
		
			?><table class="wp-list-table widefat plugins">
				<thead>
				<tr>
				<th scope="col" id="cb" class="manage-column column-cb check-column" style=""><label class="screen-reader-text" for="cb-select-all-1"><?php _e( 'Select All', 'woocommerce-jetpack' ); ?></label><input id="cb-select-all-1" type="checkbox"></th>
				<th scope="col" id="name" class="manage-column column-name" style=""><?php _e( 'Feature', 'woocommerce-jetpack' ); ?></th>
				<th scope="col" id="description" class="manage-column column-description" style=""><?php _e( 'Description', 'woocommerce-jetpack' ); ?></th>
				</tr>
				</thead>
				<tfoot>
				<tr>
				<th scope="col" class="manage-column column-cb check-column" style=""><label class="screen-reader-text" for="cb-select-all-2"><?php _e( 'Select All', 'woocommerce-jetpack' ); ?></label><input id="cb-select-all-2" type="checkbox"></th>
				<th scope="col" class="manage-column column-name" style=""><?php _e( 'Feature', 'woocommerce-jetpack' ); ?></th>
				<th scope="col" class="manage-column column-description" style=""><?php _e( 'Description', 'woocommerce-jetpack' ); ?></th>
				</tr>
				</tfoot>
				<tbody id="the-list"><?php										
					$html = '';					
					foreach ( $the_settings as $the_feature ) {		

						if ( 'checkbox' !== $the_feature['type'] ) continue;
					
						$html .= '<tr id="' . $the_feature['id'] . '" ' . 'class="' . $this->active( get_option( $the_feature['id'] ) ) . '">';
						
						$html .= '<th scope="row" class="check-column">';
						$html .= '<label class="screen-reader-text" for="' . $the_feature['id'] . '">' . $the_feature['desc'] . '</label>';
						$html .= '<input type="checkbox" name="' . $the_feature['id'] . '" value="1" id="' . $the_feature['id'] . '" ' . checked( get_option( $the_feature['id'] ), 'yes', false ) . '>';
						$html .= '</th>';			

						$html .= '<td class="plugin-title"><strong>' . $the_feature['title'] . '</strong>';						
						$html .= '<div class="row-actions visible">';
						
						// Temporary solution - 17/09/2014
						$section = $the_feature['id'];
						$section = str_replace( 'wcj_', '', $section );
						$section = str_replace( '_enabled', '', $section );
						if ( 'currency' === $section ) $section = 'currencies';
						
						$html .= '<span class="0"><a href="' . admin_url() . 'admin.php?page=wc-settings&tab=jetpack&section=' . $section . '">Settings</a></span>';
						$html .= '</div>';
						$html .= '</td>';							
					
						$html .= '<td class="column-description desc">';
						$html .= '<div class="plugin-description"><p>' . $the_feature['desc_tip'] . '</p></div>';
						$html .= '</td>';						
	
						$html .= '</tr>';
					}
					echo $html;									
				?></tbody>
			</table><?php 
		}
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
			$settings[] = array( 'title' => __( 'WooCommerce Jetpack Dashboard', 'woocommerce-jetpack' ), 'type' => 'title', 'desc' => 'This dashboard lets you enable/disable any Jetpack feature. Each checkbox comes with short feature\'s description. Please visit <a href="http://woojetpack.com" target="_blank">WooJetpack.com</a> for detailed info on each feature.', 'id' => 'wcj_options' );
			$settings = apply_filters( 'wcj_features_status', $settings );		
			$settings[] = array( 'type' => 'sectionend', 'id' => 'wcj_options' );
			return $settings;
			//apply_filters('wcj_general_settings', $settings );
		}
	}
}

endif;

return new WC_Settings_Jetpack();

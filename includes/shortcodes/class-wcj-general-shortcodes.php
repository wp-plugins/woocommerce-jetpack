<?php
/**
 * WooCommerce Jetpack General Shortcodes
 *
 * The WooCommerce Jetpack General Shortcodes class.
 *
 * @class    WCJ_General_Shortcodes
 * @version  2.2.0
 * @author   Algoritmika Ltd.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WCJ_General_Shortcodes' ) ) :

class WCJ_General_Shortcodes extends WCJ_Shortcodes {

    /**
     * Constructor.
     */
    public function __construct() {

		$this->the_shortcodes = array(
			'wcj_current_date',
			//'wcj_image',
			'wcj_cart_items_total_weight',
			'wcj_wpml',
		);

		$this->the_atts = array(
			'date_format' => get_option( 'date_format' ),
			/*'url'         => '',
			'class'       => '',
			'width'       => '',
			'height'      => '',*/
			'lang'        => '',
		);

		parent::__construct();

    }

    /**
     * wcj_wpml.
     */
	function wcj_wpml( $atts, $content ) {
		if ( '' == $atts['lang'] || ( defined( 'ICL_LANGUAGE_CODE' ) && ICL_LANGUAGE_CODE === $atts['lang'] ) ) return do_shortcode( $content );
		else return '';
	}

    /**
     * wcj_wpml_translate.
     */
	function wcj_wpml_translate( $atts, $content ) {
		return $this->wcj_wpml( $atts, $content );
	}

	/**
     * wcj_cart_items_total_weight.
     */
	function wcj_cart_items_total_weight( $atts ) {
		$the_cart = WC()->cart;
		return $the_cart->cart_contents_weight;
	}

    /**
     * wcj_current_date.
     */
	function wcj_current_date( $atts ) {
		return date_i18n( $atts['date_format'] );
	}

    /**
     * wcj_image.
     */
	/*function wcj_image( $atts ) {
		return '<img src="' . $atts['url'] . '" class="' . $atts['class'] . '" width="' . $atts['width'] . '" height="' . $atts['height'] . '">';
	}*/
}

endif;

return new WCJ_General_Shortcodes();

<?php
/**
 * class WC_Gateway_WCJ_Custom
 */
add_action( 'plugins_loaded', 'init_wcj_custom_gateway_class' );

function init_wcj_custom_gateway_class() {

	class WC_Gateway_WCJ_Custom extends WC_Payment_Gateway {
	
		/**
		 * Constructor.
		 */
		public function __construct() {	
	
			$this->id 					= 'jetpack_custom';
			//$this->icon 				= ''; //If you want to show an image next to the gateway’s name on the frontend, enter a URL to an image.
			$this->has_fields 			= false;
			$this->method_title 		= __( 'Custom', 'woocommerce-jetpack' );
			$this->method_description 	= __( 'WooCommerce Jetpack: Custom Payment Gateway', 'woocommerce-jetpack' );
			
			// Load the settings.
			$this->init_form_fields();
			$this->init_settings();

			// Define user set variables
			$this->title        = $this->get_option( 'title' );
			$this->description  = $this->get_option( 'description' );
			$this->instructions = $this->get_option( 'instructions', $this->description );

			// Actions
			add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
			add_action( 'woocommerce_thankyou_wcj_custom', array( $this, 'thankyou_page' ) );

			// Customer Emails
			add_action( 'woocommerce_email_before_order_table', array( $this, 'email_instructions' ), 10, 3 );
		}
		
		/**
		 * Initialise Gateway Settings Form Fields
		 */
		public function init_form_fields() {

			$this->form_fields = array(
				'enabled' => array(
					'title'   => __( 'Enable/Disable', 'woocommerce' ),
					'type'    => 'checkbox',
					'label'   => __( 'Enable Custom Payment', 'woocommerce' ),
					'default' => 'no'
				),
				'title' => array(
					'title'       => __( 'Title', 'woocommerce' ),
					'type'        => 'text',
					'description' => __( 'This controls the title which the user sees during checkout.', 'woocommerce' ),
					'default'     => __( 'Custom Payment', 'woocommerce' ),
					'desc_tip'    => true,
				),
				'description' => array(
					'title'       => __( 'Description', 'woocommerce' ),
					'type'        => 'textarea',
					'description' => __( 'Payment method description that the customer will see on your checkout.', 'woocommerce' ),
					'default'     => __( 'Custom Payment Description.', 'woocommerce' ),
					'desc_tip'    => true,
				),
				'instructions' => array(
					'title'       => __( 'Instructions', 'woocommerce' ),
					'type'        => 'textarea',
					'description' => __( 'Instructions that will be added to the thank you page and emails.', 'woocommerce' ),
					'default'     => '',
					'desc_tip'    => true,
				),
			);
		}		
		

		/**
		 * Output for the order received page.
		 */
		public function thankyou_page() {
			if ( $this->instructions )
				echo wpautop( wptexturize( $this->instructions ) );
		}	

		/**
		 * Add content to the WC emails.
		 *
		 * @access public
		 * @param WC_Order $order
		 * @param bool $sent_to_admin
		 * @param bool $plain_text
		 */
		public function email_instructions( $order, $sent_to_admin, $plain_text = false ) {
			if ( $this->instructions && ! $sent_to_admin && 'jetpack_custom' === $order->payment_method && 'on-hold' === $order->status ) {
				echo wpautop( wptexturize( $this->instructions ) ) . PHP_EOL;
			}
		}

		/**
		 * Process the payment and return the result
		 *
		 * @param int $order_id
		 * @return array
		 */
		public function process_payment( $order_id ) {

			$order = new WC_Order( $order_id );

			// Mark as on-hold (we're awaiting the payment)
			$order->update_status( 'on-hold', __( 'Awaiting payment', 'woocommerce' ) );

			// Reduce stock levels
			$order->reduce_order_stock();

			// Remove cart
			WC()->cart->empty_cart();

			// Return thankyou redirect
			return array(
				'result' 	=> 'success',
				'redirect'	=> $this->get_return_url( $order )
			);
		}		
	}
	
	

	function add_wcj_custom_gateway_class( $methods ) {

		$methods[] = 'WC_Gateway_WCJ_Custom';
		return $methods;
	}
	add_filter( 'woocommerce_payment_gateways', 'add_wcj_custom_gateway_class' );
}
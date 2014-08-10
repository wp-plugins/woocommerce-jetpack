<?php
/**
 * class WC_Gateway_WCJ_Custom
 */
add_action( 'plugins_loaded', 'init_wc_gateway_wcj_custom_class' );

function init_wc_gateway_wcj_custom_class() {

	class WC_Gateway_WCJ_Custom extends WC_Payment_Gateway {
	
		/**
		 * Constructor.
		 */
		public function __construct() {	
	
			$this->id 					= 'jetpack_custom_gateway';
			//$this->icon 				= ''; //If you want to show an image next to the gateway�s name on the frontend, enter a URL to an image.
			$this->has_fields 			= false;
			$this->method_title 		= __( 'Custom Gateway', 'woocommerce-jetpack' );
			$this->method_description 	= __( 'WooCommerce Jetpack: Custom Payment Gateway', 'woocommerce-jetpack' );
			
			// Load the settings.
			$this->init_form_fields();
			$this->init_settings();

			// Define user set variables
			$this->title        		= $this->get_option( 'title' );
			$this->description  		= $this->get_option( 'description' );
			$this->instructions 		= $this->get_option( 'instructions', $this->description );
			$this->icon					= $this->get_option( 'icon', '' );//apply_filters( 'woocommerce_wcj_custom_icon', $this->get_option( 'icon', '' ) );
			$this->min_amount			= $this->get_option( 'min_amount', 0 );
			$this->enable_for_methods 	= $this->get_option( 'enable_for_methods', array() );
			$this->enable_for_virtual 	= $this->get_option( 'enable_for_virtual', 'yes' ) === 'yes' ? true : false;			

			// Actions
			add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
			add_action( 'woocommerce_thankyou_wcj_custom', array( $this, 'thankyou_page' ) );

			// Customer Emails
			add_action( 'woocommerce_email_before_order_table', array( $this, 'email_instructions' ), 10, 3 );
			
			
			//add_filter( 'woocommerce_wcj_custom_icon', array( $this, 'set_icon' ) );
		}
		
		/**
		 * set_icon
		 *
		public function set_icon() {
			$icon_url = get_option( 'wcj_payment_gateways_icons_woocommerce_wcj_custom_icon', '' );
			if ( $icon_url === '' ) 
				return $this->get_option( 'icon', '' );
			return $icon_url;			
		}			
		
		/**
		 * Initialise Gateway Settings Form Fields
		 */
		public function init_form_fields() {		
			global $woocommerce;

			$shipping_methods = array();

			if ( is_admin() )
				foreach ( WC()->shipping->load_shipping_methods() as $method ) {
					$shipping_methods[ $method->id ] = $method->get_title();
				}	

			$desc = '';
			$icon_url = $this->get_option( 'icon', '' );//apply_filters( 'woocommerce_wcj_custom_icon', $this->get_option( 'icon', '' ) );
			if ( $icon_url !== '' )
				$desc = '<img src="' . $icon_url . '" alt="WooJetpack Custom" title="WooJetpack Custom" />';				

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
				'icon' => array(
					'title'       => __( 'Icon', 'woocommerce-jetpack' ),
					'type'        => 'text',
					'desc_tip' 	  => __( 'If you want to show an image next to the gateway\'s name on the frontend, enter a URL to an image.', 'woocommerce-jetpack' ),
					'default'     => '',
					'description' => $desc,
					'css'    	  => 'min-width:300px;width:50%;',
				),
				'min_amount' => array(
					'title'       	=> __( 'Minimum order amount', 'woocommerce-jetpack' ),
					'type'        	=> 'number',
					'desc_tip' 		=> __( 'If you want to set minimum order amount to show this gateway on frontend, enter a number here. Set to 0 to disable.', 'woocommerce-jetpack' ),
					'default'     	=> 0,
					'description' 	=> apply_filters( 'get_wc_jetpack_plus_message', '', 'desc' ),
					'custom_attributes'
									=> apply_filters( 'get_wc_jetpack_plus_message', '', 'disabled' ),						
				),
				'enable_for_methods' => array(
					'title'             => __( 'Enable for shipping methods', 'woocommerce' ),
					'type'              => 'multiselect',
					'class'             => 'chosen_select',
					'css'               => 'width: 450px;',
					'default'           => '',
					'description'       => __( 'If gateway is only available for certain shipping methods, set it up here. Leave blank to enable for all methods.', 'woocommerce-jetpack' ),
					'options'           => $shipping_methods,
					'desc_tip'          => true,
					'custom_attributes' => array(
						'data-placeholder' => __( 'Select shipping methods', 'woocommerce' )
					)
				),
				'enable_for_virtual' => array(
					'title'             => __( 'Enable for virtual orders', 'woocommerce' ),
					'label'             => __( 'Enable gateway if the order is virtual', 'woocommerce-jetpack' ),
					'type'              => 'checkbox',
					'default'           => 'yes'
				),
			);
		}		
		
		/**
		 * Check If The Gateway Is Available For Use
		 *
		 * @return bool
		 */
		public function is_available() {	
			// Check min amount
			$min_amount = apply_filters( 'wcj_get_option_filter', 0, $this->min_amount );
			if ( $min_amount > 0 ) {
				if ( WC()->cart->total < $min_amount )
					return false;
			}
			
			// Check shipping methods and is virtual
			$order = null;

			if ( ! $this->enable_for_virtual ) {
				if ( WC()->cart && ! WC()->cart->needs_shipping() ) {
					return false;
				}

				if ( is_page( wc_get_page_id( 'checkout' ) ) && 0 < get_query_var( 'order-pay' ) ) {
					$order_id = absint( get_query_var( 'order-pay' ) );
					$order    = new WC_Order( $order_id );

					// Test if order needs shipping.
					$needs_shipping = false;

					if ( 0 < sizeof( $order->get_items() ) ) {
						foreach ( $order->get_items() as $item ) {
							$_product = $order->get_product_from_item( $item );

							if ( $_product->needs_shipping() ) {
								$needs_shipping = true;
								break;
							}
						}
					}

					$needs_shipping = apply_filters( 'woocommerce_cart_needs_shipping', $needs_shipping );

					if ( $needs_shipping ) {
						return false;
					}
				}
			}

			if ( ! empty( $this->enable_for_methods ) ) {

				// Only apply if all packages are being shipped via ...
				$chosen_shipping_methods_session = WC()->session->get( 'chosen_shipping_methods' );

				if ( isset( $chosen_shipping_methods_session ) ) {
					$chosen_shipping_methods = array_unique( $chosen_shipping_methods_session );
				} else {
					$chosen_shipping_methods = array();
				}

				$check_method = false;

				if ( is_object( $order ) ) {
					if ( $order->shipping_method ) {
						$check_method = $order->shipping_method;
					}

				} elseif ( empty( $chosen_shipping_methods ) || sizeof( $chosen_shipping_methods ) > 1 ) {
					$check_method = false;
				} elseif ( sizeof( $chosen_shipping_methods ) == 1 ) {
					$check_method = $chosen_shipping_methods[0];
				}

				if ( ! $check_method ) {
					return false;
				}

				$found = false;

				foreach ( $this->enable_for_methods as $method_id ) {
					if ( strpos( $check_method, $method_id ) === 0 ) {
						$found = true;
						break;
					}
				}

				if ( ! $found ) {
					return false;
				}
			}			
		
			return parent::is_available();
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
	
	

	function add_wc_gateway_wcj_custom_class( $methods ) {

		$methods[] = 'WC_Gateway_WCJ_Custom';
		return $methods;
	}
	add_filter( 'woocommerce_payment_gateways', 'add_wc_gateway_wcj_custom_class' );
}
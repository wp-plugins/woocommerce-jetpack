<?php
/**
 * WooCommerce Jetpack Orders
 *
 * The WooCommerce Jetpack Orders class.
 *
 * @class		WCJ_Orders
 * @version		1.3.3
 * @category	Class
 * @author 		Algoritmika Ltd.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'WCJ_Orders' ) ) :

class WCJ_Orders {

    /**
     * Constructor.
     */
    public function __construct() {

		// Variables
		$this->default_statuses = array(
			'wc-pending'    => _x( 'Pending payment', 'Order status', 'woocommerce' ),
			'wc-processing' => _x( 'Processing', 'Order status', 'woocommerce' ),
			'wc-on-hold'    => _x( 'On hold', 'Order status', 'woocommerce' ),
			'wc-completed'  => _x( 'Completed', 'Order status', 'woocommerce' ),
			'wc-cancelled'  => _x( 'Cancelled', 'Order status', 'woocommerce' ),
			'wc-refunded'   => _x( 'Refunded', 'Order status', 'woocommerce' ),
			'wc-failed'     => _x( 'Failed', 'Order status', 'woocommerce' ),
		);

        // Main hooks
		if ( get_option( 'wcj_orders_enabled' ) == 'yes' ) {

			if ( 'yes' === get_option( 'wcj_order_numbers_enabled' ) ) {
				add_action( 'woocommerce_new_order', 		array( $this, 'add_order_number_meta' ), 								100 );
				add_filter( 'woocommerce_order_number', 	array( $this, 'display_order_number' ), 								100, 	2 );
				add_filter( 'wcj_tools_tabs', 				array( $this, 'add_renumerate_orders_tool_tab' ), 						100 );
				add_action( 'wcj_tools_renumerate_orders', 	array( $this, 'create_renumerate_orders_tool' ), 						100 );
			}
			add_action( 	'wcj_tools_dashboard', 			array( $this, 'add_renumerate_orders_tool_info_to_tools_dashboard' ), 	100 );

			if ( get_option( 'wcj_order_minimum_amount' ) > 0 ) {
				add_action( 'woocommerce_checkout_process', array( $this, 'order_minimum_amount' ) );
				add_action( 'woocommerce_before_cart', 		array( $this, 'order_minimum_amount' ) );
			}

			if ( 'yes' === get_option( 'wcj_orders_custom_statuses_enabled' ) ) {
				add_filter( 'wc_order_statuses', 			array( $this, 'add_custom_statuses_to_filter' ), 						100 );
				add_action( 'init', 						array( $this, 'register_custom_post_statuses' ) );
				add_action( 'admin_head', 					array( $this, 'hook_statuses_icons_css' ) );
				add_filter( 'wcj_tools_tabs', 				array( $this, 'add_custom_statuses_tool_tab' ), 						100 );
				add_action( 'wcj_tools_custom_statuses', 	array( $this, 'create_custom_statuses_tool' ), 							100 );
			}
			add_action( 	'wcj_tools_dashboard', 			array( $this, 'add_custom_statuses_tool_info_to_tools_dashboard' ), 	100 );

			if ( 'yes' === get_option( 'wcj_order_auto_complete_enabled' ) )
				add_action( 'woocommerce_thankyou', 		array( $this, 'auto_complete_order' ) );
		}

        // Settings hooks
        add_filter( 'wcj_settings_sections', 	array( $this, 'settings_section' ) );
        add_filter( 'wcj_settings_orders', 		array( $this, 'get_settings' ), 		100 );
        add_filter( 'wcj_features_status', 		array( $this, 'add_enabled_option' ), 	100 );
    }

	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//																				CUSTOM STATUSES																		  //
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
	/**
	 * register_custom_post_statuses.
	 */
	public function register_custom_post_statuses() {
		$wcj_orders_custom_statuses_array = ( '' == get_option( 'wcj_orders_custom_statuses_array' ) ) ? array() : get_option( 'wcj_orders_custom_statuses_array' );
		foreach ( $wcj_orders_custom_statuses_array as $slug => $label )
			register_post_status( $slug, array(
					'label'                     => $label,
					'public'                    => true,
					'exclude_from_search'       => false,
					'show_in_admin_all_list'    => true,
					'show_in_admin_status_list' => true,
					'label_count'               => _n_noop( $label . ' <span class="count">(%s)</span>', $label . ' <span class="count">(%s)</span>' ),
				) 
			);
	}

	/**
	 * add_custom_statuses_to_filter.
	 */
	public function add_custom_statuses_to_filter( $order_statuses ) {
		$wcj_orders_custom_statuses_array = ( '' == get_option( 'wcj_orders_custom_statuses_array' ) ) ? array() : get_option( 'wcj_orders_custom_statuses_array' );
		$order_statuses = ( '' == $order_statuses ) ? array() : $order_statuses;
		return array_merge( $order_statuses, $wcj_orders_custom_statuses_array );
	}

	/**
	 * add_custom_statuses_tool_info_to_tools_dashboard.
	 */
	public function add_custom_statuses_tool_info_to_tools_dashboard() {
		if ( 'yes' === get_option( 'wcj_orders_custom_statuses_enabled' ) )
			echo '<h3>Custom Statuses tool is enabled.</h3>';
		else
			echo '<h3>Custom Statuses tool is disabled.</h3>';
		echo '<p>The tool lets you add or delete any custom status for WooCommerce orders.</p>';
	}

	/**
	 * add_custom_statuses_tool_tab.
	 */
	public function add_custom_statuses_tool_tab( $tabs ) {
		$tabs[] = array(
			'id'		=> 'custom_statuses',
			'title'		=> __( 'Custom Statuses', 'woocommerce-jetpack' ),
		);
		return $tabs;
	}

    /**
     * hook_statuses_icons_css.
	 * TODO: content, color
     */
	public function hook_statuses_icons_css() {
		$output = '<style>';
		$statuses = function_exists( 'wc_get_order_statuses' ) ? wc_get_order_statuses() : array();
		foreach( $statuses as $status => $status_name ) {
			if ( ! array_key_exists( $status, $this->default_statuses ) ) {
				$output .= 'mark.' . substr( $status, 3 ) . '::after { content: "\e011"; color: #999; }';
				$output .= 'mark.' . substr( $status, 3 ) . ':after {font-family:WooCommerce;speak:none;font-weight:400;font-variant:normal;text-transform:none;line-height:1;-webkit-font-smoothing:antialiased;margin:0;text-indent:0;position:absolute;top:0;left:0;width:100%;height:100%;text-align:center}';
			}
		}
		$output .= '.close:after { content: "\e011"; }';
		$output .= '</style>';
		echo $output;
	}

    /**
     * Add new custom status to wcj_orders_custom_statuses_array.
     */
	public function add_custom_status( $new_status, $new_status_label ) {

		// Checking function arguments
		if ( ! isset( $new_status ) || '' == $new_status )
			return '<div class="error"><p>' . __( 'Status slug is empty. Status not added.', 'woocommerce-jetpack' ) . '</p></div>';
		if ( ! isset( $new_status_label ) || '' == $new_status_label )
			return '<div class="error"><p>' . __( 'Status label is empty. Status not added.', 'woocommerce-jetpack' ) . '</p></div>';

		// Checking status
		$statuses_updated = ( '' == get_option( 'wcj_orders_custom_statuses_array' ) ) ? array() : get_option( 'wcj_orders_custom_statuses_array' );
		$new_key = 'wc-' . $_POST['new_status'];
		if ( isset( $statuses_updated[$new_key] ) )
			return '<div class="error"><p>' . __( 'Duplicate slug. Status not added.', 'woocommerce-jetpack' ) . '</p></div>';
		$statuses_updated[$new_key] = $_POST['new_status_label'];

		// Adding custom status
		$result = update_option( 'wcj_orders_custom_statuses_array', $statuses_updated );
		if ( true === $result )
			return '<div class="updated"><p>' . __( 'New status have been successfully added!', 'woocommerce-jetpack' ) . '</p></div>';
		else
			return '<div class="error"><p>' . __( 'Status was not added.', 'woocommerce-jetpack' ) . '</p></div>';
	}

    /**
     * create_custom_statuses_tool.
     */	
	public function create_custom_statuses_tool() {

		$result_message = '';
		if ( isset( $_POST['add_custom_status'] ) )
			$result_message = $this->add_custom_status( $_POST['new_status'], $_POST['new_status_label'] );
		else if ( isset( $_GET['delete'] ) && ( '' != $_GET['delete'] ) ) {
			$statuses_updated = apply_filters( 'wc_order_statuses', $statuses_updated );
			unset( $statuses_updated[ $_GET['delete'] ] );
			$result = update_option( 'wcj_orders_custom_statuses_array', $statuses_updated );
			if ( true === $result )
				$result_message = '<div class="updated"><p>' . __( 'Status have been successfully deleted.', 'woocommerce-jetpack' ) . '</p></div>';
			else
				$result_message = '<div class="error"><p>' . __( 'Delete failed.', 'woocommerce-jetpack' ) . '</p></div>';
		}
		?><div>
			<h2><?php echo __( 'WooCommerce Jetpack - Custom Statuses', 'woocommerce-jetpack' ); ?></h2>
			<p><?php echo __( 'The tool lets you add or delete any custom status for WooCommerce orders.', 'woocommerce-jetpack' ); ?></p>
			<?php echo $result_message; ?>
			<h3><?php echo __( 'Statuses', 'woocommerce-jetpack' ); ?></h3>
			<table class="wc_status_table widefat"><?php
				echo '<tr>';
				echo '<th>' . __( 'Slug', 'woocommerce-jetpack' ) . '</th>';
				echo '<th>' . __( 'Label', 'woocommerce-jetpack' ) . '</th>';
				//echo '<th>' . __( 'Count', 'woocommerce-jetpack' ) . '</th>';
				echo '<th>' . __( 'Delete', 'woocommerce-jetpack' ) . '</th>';
				echo '</tr>';
				$statuses = function_exists( 'wc_get_order_statuses' ) ? wc_get_order_statuses() : array();
				foreach( $statuses as $status => $status_name ) {
					echo '<tr>';
					echo '<td>' . esc_attr( $status ) . '</td>';
					echo '<td>' . esc_html( $status_name ) . '</td>';
					if ( array_key_exists( $status, $this->default_statuses ) )
						echo '<td></td>';
					else
						echo '<td>' . '<a href="' . add_query_arg( 'delete', $status ) . '">' . __( 'Delete', 'woocommerce-jetpack' ) . '</a>' . '</td>';
					echo '</tr>';
				}
			?></table>
			<p></p>
		</div><?php
		?><div class="metabox-holder" style="width:300px;">
				<div class="postbox">
					<h3 class="hndle"><span>Add</span></h3>
					<div class="inside">
						<form method="post" action="<?php echo remove_query_arg( 'delete' ); ?>">
							<ul>
								<li><?php _e( 'Slug (without wc- prefix)', 'woocommerce-jetpack' ); ?><input type="text" name="new_status" style="width:100%;"></li>
								<li><?php _e( 'Label', 'woocommerce-jetpack' ); ?><input type="text" name="new_status_label" style="width:100%;"></li>
							</ul>
							<input class="button-primary" type="submit" name="add_custom_status" value="Add new custom status">
						</form>
					</div>
				</div>
		</div><?php
	}

	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//																				ORDERS NUMBERS																		  //
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////		
    /**
     * Display order number.
     */
    public function display_order_number( $order_number, $order ) {
		$order_number_meta = get_post_meta( $order->id, '_wcj_order_number', true );
		if ( $order_number_meta !== '' )
			$order_number = apply_filters( 'wcj_get_option_filter', '#' . $order_number_meta, sprintf( '%s%0' . get_option( 'wcj_order_number_min_width', 0 ) . 'd', get_option( 'wcj_order_number_prefix', '' ), $order_number_meta ) );
		return $order_number;
    }
	
	/**
	 * add_renumerate_orders_tool_info_to_tools_dashboard.
	 */
	public function add_renumerate_orders_tool_info_to_tools_dashboard() {
		if ( 'yes' === get_option( 'wcj_order_numbers_enabled' ) )
			echo '<h3>Orders Renumerate tool is enabled.</h3>';
		else
			echo '<h3>Orders Renumerate tool is disabled.</h3>';
		echo '<p>' . __( 'The tool renumerates all orders.', 'woocommerce-jetpack' ) . '</p>';
	}

	/**
	 * add_renumerate_orders_tool_tab.
	 */
	public function add_renumerate_orders_tool_tab( $tabs ) {
		$tabs[] = array(
			'id'		=> 'renumerate_orders',
			'title'		=> __( 'Renumerate orders', 'woocommerce-jetpack' ),
		);
		return $tabs;
	}	

    /**
     * Add Renumerate Orders tool to WooCommerce menu (the content).
     */
	public function create_renumerate_orders_tool() {
		$result_message = '';
		if ( isset( $_POST['renumerate_orders'] ) ) {
			$this->renumerate_orders();
			$result_message = '<div class="updated"><p><strong>Orders successfully renumerated!</strong></p></div>';
		}
		?><div>
			<h2><?php echo __( 'WooCommerce Jetpack - Renumerate Orders', 'woocommerce-jetpack' ); ?></h2>
			<p><?php echo __( 'The tool renumerates all orders. Press the button below to renumerate all existing orders starting from order counter settings in WooCommerce > Settings > Jetpack > Order Numbers.', 'woocommerce-jetpack' ); ?></p>
			<?php echo $result_message; ?>
			<form method="post" action="">
				<input class="button-primary" type="submit" name="renumerate_orders" value="Renumerate orders">
			</form>
		</div><?php
	}

    /**
     * Add/update order_number meta to order.
     */
    public function add_order_number_meta( $order_id ) {

		$current_order_number = get_option( 'wcj_order_number_counter' );
		//echo $current_order_number;
		update_option( 'wcj_order_number_counter', ( $current_order_number + 1 ) );
		update_post_meta( $order_id, '_wcj_order_number', $current_order_number );
	}

    /**
     * Renumerate orders function.
     */
	public function renumerate_orders() {

		$args = array(
			'post_type'			=> 'shop_order',
			'post_status' 		=> 'any',
			'posts_per_page' 	=> -1,
			'orderby'			=> 'date',
			'order'				=> 'ASC',
		);

		$loop = new WP_Query( $args );

		while ( $loop->have_posts() ) : $loop->the_post();

			$order_id = $loop->post->ID;
			$this->add_order_number_meta( $order_id );

		endwhile;
	}	

	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//																				AUTO COMPLETE																		  //
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////		
	/**
	* Auto Complete all WooCommerce orders.
	*/
	public function auto_complete_order( $order_id ) {

		global $woocommerce;

		if ( !$order_id )
			return;
		$order = new WC_Order( $order_id );
		$order->update_status( 'completed' );
	}

	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//																				MINIMUM AMOUNT																		  //
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
    /**
     * order_minimum_amount.
     */
	public function order_minimum_amount() {

		$minimum = get_option( 'wcj_order_minimum_amount' );
		if ( WC()->cart->total < $minimum ) {

			if( is_cart() ) {
				if ( 'yes' === get_option( 'wcj_order_minimum_amount_cart_notice_enabled' ) ) {
					wc_print_notice(
						sprintf( apply_filters( 'wcj_get_option_filter', 'You must have an order with a minimum of %s to place your order, your current order total is %s.', get_option( 'wcj_order_minimum_amount_cart_notice_message' ) ),
							woocommerce_price( $minimum ),
							woocommerce_price( WC()->cart->total )
						), 'notice'
					);
				}
			} else {
				wc_add_notice(
					sprintf( apply_filters( 'wcj_get_option_filter', 'You must have an order with a minimum of %s to place your order, your current order total is %s.', get_option( 'wcj_order_minimum_amount_error_message' ) ),
						woocommerce_price( $minimum ),
						woocommerce_price( WC()->cart->total )
					), 'error'
				);
			}
		}
	}


    /**
     * Add Enabled option to Jetpack Dashboard.
     */
    public function add_enabled_option( $settings ) {

        $all_settings = $this->get_settings();
        $settings[] = $all_settings[1];

        return $settings;
    }

    /**
     * Add settings arrays to Jetpack Settings.
     */
    function get_settings() {

        $settings = array(

			//This first section\'s checkbox enables/disables all options below.
			array( 'title' => __( 'Orders Options', 'woocommerce-jetpack' ), 'type' => 'title', 'desc' => __( '', 'woocommerce-jetpack' ), 'id' => 'wcj_orders_options' ),

            array(
                'title'    => __( 'Orders', 'woocommerce-jetpack' ),
                'desc'     => __( 'Enable the Orders feature', 'woocommerce-jetpack' ),
                'desc_tip' => __( 'Sequential order numbering, custom order number prefix and number width. Minimum order amount.', 'woocommerce-jetpack' ),
                'id'       => 'wcj_orders_enabled',
                'default'  => 'yes',
                'type'     => 'checkbox',
            ),

			array( 'type'  => 'sectionend', 'id' => 'wcj_orders_options' ),

            array( 'title' => __( 'Order Numbers', 'woocommerce-jetpack' ), 'type' => 'title', 'desc' => __( 'This section lets you enable sequential order numbering, set custom number prefix and width.', 'woocommerce-jetpack' ), 'id' => 'wcj_order_numbers_options' ),

            array(
                'title'    => __( 'Order Numbers', 'woocommerce-jetpack' ),
                'desc'     => __( 'Enable', 'woocommerce-jetpack' ),
                'id'       => 'wcj_order_numbers_enabled',
                'default'  => 'no',
                'type'     => 'checkbox',
            ),

            array(
                'title'    => __( 'Next Order Number', 'woocommerce-jetpack' ),
                'desc'     => __( 'Next new order will be given this number.', 'woocommerce-jetpack' ),
                'id'       => 'wcj_order_number_counter',
                'default'  => 1,
                'type'     => 'number',
            ),

            array(
                'title'    => __( 'Order Number Prefix', 'woocommerce-jetpack' ),
				'desc' 	   => apply_filters( 'get_wc_jetpack_plus_message', '', 'desc' ),
                'desc_tip' => __( 'Prefix before order number (optional). This will change the prefixes for all existing orders.', 'woocommerce-jetpack' ),
                'id'       => 'wcj_order_number_prefix',
                'default'  => '#',
                'type'     => 'text',
				'custom_attributes'
						   => apply_filters( 'get_wc_jetpack_plus_message', '', 'readonly' ),
            ),

            array(
                'title'    => __( 'Order Number Width', 'woocommerce-jetpack' ),
				'desc' 	   => apply_filters( 'get_wc_jetpack_plus_message', '', 'desc' ),
                'desc_tip' => __( 'Minimum width of number without prefix (zeros will be added to the left side). This will change the minimum width of order number for all existing orders. E.g. set to 5 to have order number displayed as 00001 instead of 1. Leave zero to disable.', 'woocommerce-jetpack' ),
                'id'       => 'wcj_order_number_min_width',
                'default'  => 0,
                'type'     => 'number',
				'custom_attributes'
						   => apply_filters( 'get_wc_jetpack_plus_message', '', 'readonly' ),
            ),

            array( 'type'  => 'sectionend', 'id' => 'wcj_order_numbers_options' ),

            array( 'title' => __( 'Order Minimum Amount', 'woocommerce-jetpack' ), 'type' => 'title', 'desc' => __( 'This section lets you set minimum order amount.', 'woocommerce-jetpack' ), 'id' => 'wcj_order_minimum_amount_options' ),

            array(
                'title'    => __( 'Amount', 'woocommerce-jetpack' ),
                'desc'     => __( 'Minimum order amount. Set to 0 to disable.', 'woocommerce-jetpack' ),
                'id'       => 'wcj_order_minimum_amount',
                'default'  => 0,
                'type'     => 'number',
            ),

            array(
                'title'    => __( 'Error message', 'woocommerce-jetpack' ),
				'desc' 	   => apply_filters( 'get_wc_jetpack_plus_message', '', 'desc' ),
                'desc_tip' => __( 'Message to customer if order is below minimum amount. Default: You must have an order with a minimum of %s to place your order, your current order total is %s.', 'woocommerce-jetpack' ),
                'id'       => 'wcj_order_minimum_amount_error_message',
                'default'  => 'You must have an order with a minimum of %s to place your order, your current order total is %s.',
                'type'     => 'textarea',
				'custom_attributes'
						   => apply_filters( 'get_wc_jetpack_plus_message', '', 'readonly' ),
				'css'	   => 'width:50%;min-width:300px;',
            ),

            array(
                'title'    => __( 'Add notice to cart page also', 'woocommerce-jetpack' ),
                'desc'     => __( 'Add', 'woocommerce-jetpack' ),
                'id'       => 'wcj_order_minimum_amount_cart_notice_enabled',
                'default'  => 'no',
                'type'     => 'checkbox',
            ),

            array(
                'title'    => __( 'Message on cart page', 'woocommerce-jetpack' ),
				'desc' 	   => apply_filters( 'get_wc_jetpack_plus_message', '', 'desc' ),
                'desc_tip' => __( 'Message to customer if order is below minimum amount. Default: You must have an order with a minimum of %s to place your order, your current order total is %s.', 'woocommerce-jetpack' ),
                'id'       => 'wcj_order_minimum_amount_cart_notice_message',
                'default'  => 'You must have an order with a minimum of %s to place your order, your current order total is %s.',
                'type'     => 'textarea',
				'custom_attributes'
						   => apply_filters( 'get_wc_jetpack_plus_message', '', 'readonly' ),
				'css'	   => 'width:50%;min-width:300px;',
            ),

            array( 'type'  => 'sectionend', 'id' => 'wcj_order_minimum_amount_options' ),

			array( 'title' => __( 'Orders Auto-Complete', 'woocommerce-jetpack' ), 'type' => 'title', 'desc' => __( 'This section lets you enable orders auto-complete function.', 'woocommerce-jetpack' ), 'id' => 'wcj_order_auto_complete_options' ),

            array(
                'title'    => __( 'Auto-complete all WooCommerce orders', 'woocommerce-jetpack' ),
                'desc'     => __( 'Enable', 'woocommerce-jetpack' ),
                'desc_tip' => __( 'E.g. if you sell digital products then you are not shipping anything and you may want auto-complete all your orders.', 'woocommerce-jetpack' ),
                'id'       => 'wcj_order_auto_complete_enabled',
                'default'  => 'no',
                'type'     => 'checkbox',
            ),

			array( 'type'  => 'sectionend', 'id' => 'wcj_order_auto_complete_options' ),

			array( 'title' => __( 'Custom Statuses', 'woocommerce-jetpack' ), 'type' => 'title', 'desc' => __( 'This section lets you enable custom statuses tool.', 'woocommerce-jetpack' ), 'id' => 'wcj_orders_custom_statuses_options' ),

            array(
                'title'    => __( 'Custom Statuses', 'woocommerce-jetpack' ),
                'desc'     => __( 'Enable', 'woocommerce-jetpack' ),
                //'desc_tip' => __( 'E.g. if you sell digital products then you are not shipping anything and you may want auto-complete all your orders.', 'woocommerce-jetpack' ),
                'id'       => 'wcj_orders_custom_statuses_enabled',
                'default'  => 'yes',
                'type'     => 'checkbox',
            ),

			array( 'type'  => 'sectionend', 'id' => 'wcj_orders_custom_statuses_options' ),

        );

        return $settings;
    }

    /**
     * Add settings section to WooCommerce > Settings > Jetpack.
     */
    function settings_section( $sections ) {
        $sections['orders'] = __( 'Orders', 'woocommerce-jetpack' );
        return $sections;
    }
}

endif;

return new WCJ_Orders();

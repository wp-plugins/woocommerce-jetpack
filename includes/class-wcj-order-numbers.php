<?php
/**
 * WooCommerce Jetpack Order Numbers
 *
 * The WooCommerce Jetpack Order Numbers class.
 *
 * @class        WCJ_Order_Numbers
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
 
if ( ! class_exists( 'WCJ_Order_Numbers' ) ) :
 
class WCJ_Order_Numbers {
    
    /**
     * Constructor.
     */
    public function __construct() {
    
        // Main hooks
        if ( get_option( 'wcj_order_numbers_enabled' ) == 'yes' ) {
		
			add_action( 'woocommerce_new_order', array( $this, 'add_order_number_meta' ), 100 );
			add_filter( 'woocommerce_order_number', array( $this, 'display_order_number' ), 100, 2 );
        }
		add_action( 'admin_menu', array($this, 'add_renumerate_orders_tool'), 100 );
    
        // Settings hooks
        add_filter( 'wcj_settings_sections', array( $this, 'settings_section' ) );
        add_filter( 'wcj_settings_order_numbers', array( $this, 'get_settings' ), 100 );
        add_filter( 'wcj_features_status', array( $this, 'add_enabled_option' ), 100 );
    }
	
    /**
     * Add Renumerate Orders tool to WooCommerce menu (menu link).
     */	
	public function add_renumerate_orders_tool() {
	
		add_submenu_page( 'woocommerce', 'Jetpack - Renumerate Orders', 'Renumerate Orders', 'manage_options', 'woocommerce-jetpack--renumerate-orders', array( $this, 'create_renumerate_orders_tool' ) );
	}
	
    /**
     * Add Renumerate Orders tool to WooCommerce menu (the content).
     */	
	public function create_renumerate_orders_tool() {
			
		$result_message = '';
		if ( isset( $_POST['renumerate_orders'] ) ) {
		
			$this->renumerate_orders();
			$result_message = '<div class="updated"><p><strong>Orders successfully renumerated!</p></div>';
		}
		?>
		
		<?php echo $result_message; ?>
		<div>
			<h2>WooCommerce Jetpack - Renumerate Orders</h2>
			<p>The tool renumerates all orders. Press the button below to renumerate all existing orders starting from order counter settings in WooCommerce > Settings > Jetpack > Order Numbers.</p>
			<form method="post" action="">
				<input type="submit" name="renumerate_orders" value="Renumerate orders">
			</form>		
		</div>
		<?php		
	}	

    /**
     * Add/update order_number meta to order.
     */
    public function add_order_number_meta( $order_id ) {

		$current_order_number = get_option( 'wcj_order_number_counter' );
		update_option( 'wcj_order_number_counter', ( $current_order_number + 1 ) );
		//add_post_meta( $order_id, '_wcj_order_number', $current_order_number );
		update_post_meta( $order_id, '_wcj_order_number', $current_order_number );
	}
	
    /**
     * Display order number.
     */
    public function display_order_number( $order_number, $order ) {
    
		$order_number_meta = get_post_meta( $order->id, '_wcj_order_number', true );
		if ( $order_number_meta != '' ) 
			$order_number = '#' . $order_number_meta;
		
		return $order_number;
    }
	
    /**
     * Renumerate orders function.
     */	
	public function renumerate_orders() {
	
		$args = array(
			'post_type'			=> 'shop_order',
			'post_status' 		=> 'publish',
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
 
            array( 'title' => __( 'Order Numbers Options', 'woocommerce-jetpack' ), 'type' => 'title', 'desc' => __( 'Order Numbers.', 'woocommerce-jetpack' ), 'id' => 'wcj_order_numbers_options' ),
            
            array(
                'title'    => __( 'Order Numbers', 'woocommerce-jetpack' ),
                'desc'     => __( 'Enable the Order Numbers feature', 'woocommerce-jetpack' ),
                'desc_tip' => __( 'Sequential order numbering. Custom order number prefix and number width.', 'woocommerce-jetpack' ),
                'id'       => 'wcj_order_numbers_enabled',
                'default'  => 'no',
                'type'     => 'checkbox',
            ),
			
            array(
                'title'    => __( 'Next Order Number', 'woocommerce-jetpack' ),
                'desc'     => __( 'Next new order will be given this number.', 'woocommerce-jetpack' ),
                //'desc_tip' => __( 'Order Numbers.', 'woocommerce-jetpack' ),
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
        );
        
        return $settings;
    }
 
    /**
     * Add settings section to WooCommerce > Settings > Jetpack.
     */
    function settings_section( $sections ) {
    
        $sections['order_numbers'] = 'Order Numbers';
        
        return $sections;
    }    
}
 
endif;
 
return new WCJ_Order_Numbers();

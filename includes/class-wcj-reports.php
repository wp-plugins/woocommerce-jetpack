<?php
/**
 * WooCommerce Jetpack Reports
 *
 * The WooCommerce Jetpack Reports class.
 *
 * @class 		WCJ_Reports
 * @version		1.0.0
 * @package		WC_Jetpack/Classes
 * @category	Class
 * @author 		Algoritmika Ltd.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'WCJ_Reports' ) ) :

class WCJ_Reports {

	/**
	 * Constructor.
	 */
	public function __construct() {

		// Main hooks
		if ( get_option( 'wcj_reports_enabled' ) == 'yes' ) {
			if ( is_admin() ) {
				add_filter( 'wcj_tools_tabs', array( $this, 'add_reports_tool_tab' ), 100 );
				add_action( 'wcj_tools_reports', array( $this, 'create_reports_tool' ), 100 );
			}
		}

		// Settings hooks
		add_filter( 'wcj_settings_sections', array( $this, 'settings_section' ) ); 		// Add section to WooCommerce > Settings > Jetpack
		add_filter( 'wcj_settings_reports', array( $this, 'get_settings' ), 100 ); 		// Add the settings
		add_filter( 'wcj_features_status', array( $this, 'add_enabled_option' ), 100 );	// Add Enable option to Jetpack Settings Dashboard
	}

	/**
	 * Add tab to WooCommerce > Jetpack Tools.
	 */
	public function add_reports_tool_tab( $tabs ) {
		$tabs[] = array(
			'id'		=> 'reports',
			'title'		=> __( 'Smart Reports', 'woocommerce-jetpack' ),
		);
		return $tabs;
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
				'id' 		=> 'wcj_reports_enabled',
				'default'	=> 'yes',
				'type' 		=> 'checkbox'
			),

			array( 'type' 	=> 'sectionend', 'id' => 'wcj_reports_options' ),
		);

		return $settings;
	}

	/*
	 * Add settings section to WooCommerce > Settings > Jetpack.
	 */
	function settings_section( $sections ) {

		$sections['reports'] = __( 'Smart Reports', 'woocommerce-jetpack' );

		return $sections;
	}

	/*
	 * add_reports_tool.
	 *
	public function add_reports_tool() {
		add_submenu_page( 'woocommerce', 'Jetpack - Smart Reports', 'Smart Reports', 'manage_options', 'woocommerce-jetpack-reports', array( $this, 'create_reports_tool' ) );
	}

	/*
	 * get_products_info.
	 */
	public function get_products_info( &$products_info ) {

		$args = array(
			'post_type' => 'product',
			'posts_per_page' => -1
		);

		$loop = new WP_Query( $args );
		if ( $loop->have_posts() ) {

			while ( $loop->have_posts() ) : $loop->the_post();

				$the_ID = get_the_ID();
				$the_product = new WC_Product( $the_ID );
				$the_price = $the_product->get_price();
				$the_stock = $the_product->get_total_stock();
				$the_title = get_the_title();
				$the_date = get_the_date();
				$the_permalink = get_the_permalink();
				$total_sales = get_post_custom( $the_ID )['total_sales'][0];

				$products_info[$the_ID] = array(
					'ID'				=>	$the_ID,
					'title'				=>	$the_title,
					'permalink'			=>	$the_permalink,
					'price'				=>	$the_price,
					'stock'				=>	$the_stock,
					'stock_price'		=>	$the_price * $the_stock,
					'total_sales'		=>	$total_sales,
					'date_added'		=>	$the_date,
					'last_sale'			=>	0,
					'sales_in_period'	=> array(
						7	=> 0,
						14	=> 0,
						30	=> 0,
						60	=> 0,
						90	=> 0,
						180	=> 0,
						360	=> 0,
					),
				);

			endwhile;
		}
	}

	/*
	 * get_orders_info.
	 */
	public function get_orders_info( &$products_info ) {

		$args_orders = array(
			'post_type'			=> 'shop_order',
			'post_status' 		=> 'publish',
			'posts_per_page' 	=> -1,
			'orderby'			=> 'date',
			'order'				=> 'DESC',
			'tax_query'			=> '',
		);

		$loop_orders = new WP_Query( $args_orders );
		while ( $loop_orders->have_posts() ) : $loop_orders->the_post();

			$order_id = $loop_orders->post->ID;
			$order = new WC_Order( $order_id );
			$items = $order->get_items();

			foreach ( $items as $item ) {

				$the_timestamp =  get_the_time( 'U' );
				$now_time = time();
				$order_age = ( $now_time - $the_timestamp );
				$one_day_seconds = ( 24 * 60 * 60 );

				foreach ( $products_info[$item['product_id']]['sales_in_period'] as $the_period => $the_value ) {
					if ( $order_age < ( $the_period * $one_day_seconds ) ) {
						$products_info[$item['product_id']]['sales_in_period'][$the_period] += $item['qty'];
						//$products_info[$item['product_id']]['orders_in_period'][$the_period]++;
					}
				}

				if ( 0 == $products_info[$item['product_id']]['last_sale'] ) {
					$products_info[$item['product_id']]['last_sale'] = $the_timestamp;
				}

			}

		endwhile;

		//wp_reset_query();
	}

	/*
	 * count_info.
	 */
	public function count_info( &$info, &$products_info ) {

		$info['total_stock_price'] = 0;
		$info['stock_price_average'] = 0;
		$info['stock_average'] = 0;
		$info['sales_in_period_average'] = 0;
		$stock_non_zero_number = 0;

		foreach ( $products_info as $product_info ) {

			if ( $product_info['sales_in_period'][$this->period] > 0 )
				$products_info[ $product_info['ID'] ]['stock_to_sales'] = $product_info['stock'] / $product_info['sales_in_period'][$this->period];
			else
				$products_info[ $product_info['ID'] ]['stock_to_sales'] = 0;

			if ( $product_info['stock_price'] > 0 ) {
				$info['stock_price_average'] += $product_info['stock_price'];
				$info['stock_average'] = $product_info['stock'];
				$info['sales_in_period_average'] += $product_info['sales_in_period'][$this->period];
				$stock_non_zero_number++;
			}

			$info['total_stock_price'] += $product_info['stock_price'];
		}

		$info['stock_price_average'] /= $stock_non_zero_number;
		$info['stock_average'] /= $stock_non_zero_number;
		$info['sales_in_period_average'] /= $stock_non_zero_number;
	}

	/*
	 * sort_products_info.
	 */
	public function sort_products_info( &$products_info, $field_name, $second_field_name = '', $order_of_sorting = SORT_DESC ) {
		$field_name_array = array();
		foreach ( $products_info as $key => $row ) {
			if ( '' == $second_field_name ) $field_name_array[ $key ] = $row[ $field_name ];
			else $field_name_array[ $key ] = $row[ $field_name ][ $second_field_name ];
		}
		array_multisort( $field_name_array, $order_of_sorting, $products_info );
	}

	/*
	 * output_submenu.
	 */
	public function output_submenu() {
		?><ul class="subsubsub"><?php
		$periods = array( 90, 30, 7, );
		foreach ( $periods as $the_period ) {
			echo '<li><a href="' . get_admin_url() . 'admin.php?page=wcj-tools&tab=' . $_GET['tab'] . '&report=' . $this->report . '&period=' . $the_period . '" class="">' . $the_period . ' days</a> | </li>';
		}
		?></ul>
		<br class="clear">
		<ul class="subsubsub"><?php
		foreach ( $this->reports_info as $report => $report_info ) {
			echo '<li><a href="' . get_admin_url() . 'admin.php?page=wcj-tools&tab=' . $_GET['tab'] . '&report=' . $report . '" class="">' . $report_info['title'] . '</a> | </li>';
		}
		?></ul>
		<br class="clear">
		<h3><?php _e( 'Reports', 'woocommerce-jetpack' ); ?></h3><?php
	}

	/*
	 * output_report.
	 */
	public function output_report( $products_info, $info, $report_info ) {

		// Style
		$html = '<style>';
		$html .= '.wcj_report_table { width: 90%; border-collapse: collapse; }';
		$html .= '.wcj_report_table th { border: 1px solid black; }';
		$html .= '.wcj_report_table td { border: 1px solid black; text-align: right; }';
		$html .= '.wcj_report_table_sales_columns { background-color: #F6F6F6; }';
		$html .= '.widefat { width: 90%; }';
		$html .= '</style>';

		// Products table - header
		$html .= '<table class="widefat"><tbody>';
		$html .= '<tr>';
		$html .= '<th>#</th>';
		$html .= '<th>' . __( 'Product', 'woocommerce-jetpack' ) . '</th>';
		$html .= '<th>' . __( 'Price', 'woocommerce-jetpack' ) . '</th>';
		$html .= '<th>' . __( 'Stock', 'woocommerce-jetpack' ) . '</th>';
		$html .= '<th>' . __( 'Stock price', 'woocommerce-jetpack' ) . '</th>';

		$html .= '<th class="wcj_report_table_sales_columns">' . __( 'Last sale', 'woocommerce-jetpack' ) . '</th>';
		$html .= '<th class="wcj_report_table_sales_columns">' . sprintf( __( 'Sales in last %s days', 'woocommerce-jetpack' ), $this->period ) . '</th>';
		$html .= '<th class="wcj_report_table_sales_columns">' . __( 'Sales in previous period', 'woocommerce-jetpack' ) . '</th>';
		$html .= '<th class="wcj_report_table_sales_columns">' . __( 'Total sales', 'woocommerce-jetpack' ) . '</th>';
		$html .= '</tr>';

		// Products table - info loop
		$total_current_stock_price = 0;
		$product_counter = 0;
		foreach ( $products_info as $product_info ) {

			/*if ( ( time() - strtotime( $product_info['date_added'] ) ) < 60*60*24*30 )
				continue;*/

			if (
				/*(
				 ( $info['sales_in_90_days_average'] > $product_info['sales_in_90_days'] ) &&
				 ( $info['stock_price_average'] < $product_info['stock_price'] ) &&
				 //( 0 != $product_info['stock'] ) &&
				 ( 'bad_stock' === $report_info['id'] )
			    ) || */
				(
				 ( $info['stock_price_average'] < $product_info['stock_price'] ) &&
				 //( 0 != $product_info['stock'] ) &&
				 ( 'most_stock_price' === $report_info['id'] )
			    ) ||
				(
				 ( $product_info['sales_in_period'][$this->period] < $info['sales_in_period_average'] ) &&
				 ( $product_info['stock'] > 0 ) &&
				 ( 'bad_sales' === $report_info['id'] )
			    ) ||
				(
				 ( 0 == $product_info['sales_in_period'][$this->period] ) &&
				 ( $product_info['stock'] > 0 ) &&
				 ( 'no_sales' === $report_info['id'] )
			    ) ||
				/*(
				 ( $info['sales_in_90_days_average'] < $product_info['sales_in_90_days'] ) &&
				 ( 'good_sales' === $report_info['id'] )
			    ) ||
				(
				 ( $info['sales_in_90_days_average'] < $product_info['sales_in_90_days'] ) &&
				 //( $info['stock_average'] > $product_info['stock'] ) &&
//				 ( $product_info['sales_in_90_days'] > $product_info['stock'] ) &&
				 ( ( $product_info['stock'] / $product_info['sales_in_90_days'] ) <= 0.33 ) &&
				 ( '' !== $product_info['stock'] ) &&
				 ( 'good_sales_low_stock' === $report_info['id'] )
			    ) ||*/
				(
				 ( $product_info['stock'] > 0 ) &&
				 ( 'on_stock' === $report_info['id'] )
			    ) ||
				(
				 //( 0 != $product_info['stock'] ) &&
				 ( 'any_sale' === $report_info['id'] )
			    ) ||
				(
				 //( $info['sales_in_90_days_average'] < $product_info['sales_in_90_days'] ) &&
				 ( ( $product_info['sales_in_period'][ $this->period * 2 ] - $product_info['sales_in_period'][$this->period] ) < $product_info['sales_in_period'][$this->period] ) &&
				 ( 'sales_up' === $report_info['id'] )
			    ) ||
				(
				 ( ( $product_info['sales_in_period'][180] - $product_info['sales_in_period'][90] ) > $product_info['sales_in_period'][90] ) &&
				 ( 'sales_down' === $report_info['id'] )
			    )
			)
			{
				$total_current_stock_price += $product_info['stock_price'];
				$product_counter++;
				$html .= '<tr>';
				$html .= '<td>' . $product_counter . '</td>';
				$html .= '<th>' . '<a href='. $product_info['permalink'] . '>' . $product_info['title'] . '</a>' . '</th>';
				$html .= '<td>' . wc_price( $product_info['price'] ) . '</td>';
				$html .= '<td>' . $product_info['stock'] . '</td>';
				$html .= '<td>' . wc_price( $product_info['stock_price'] ) . '</td>';

				$html .= '<td class="wcj_report_table_sales_columns">';
				if ( 0 == $product_info['last_sale'] ) $html .= __( 'No sales yet', 'woocommerce-jetpack' );
				else $html .= date( get_option( 'date_format' ), $product_info['last_sale'] );
				$html .= '</td>';
				$html .= '<td class="wcj_report_table_sales_columns">' . $product_info['sales_in_period'][ $this->period ] . '</td>';
				$html .= '<td class="wcj_report_table_sales_columns">' . $product_info['sales_in_period'][ $this->period * 2 ] . '</td>';
				//$html .= '<td>' . $product_info['orders_in_90_days'] . '</td>';
				$html .= '<td class="wcj_report_table_sales_columns">' . $product_info['total_sales'] . '</td>';
				//$html .= '<td>' . $product_info['date_added'] . '</td>';
				//$html .= '<td>' . wc_price( $total_current_stock_price ) . ' (' . number_format( ( ( $total_current_stock_price / $info['total_stock_price'] ) * 100 ), 2, '.', '' ) . ')' . '</td>';
				$html .= '</tr>';
			}
		}
		$html .= '</tbody></table>';

		$html_header = '<h4>' . $report_info['title'] . ': ' . $report_info['desc'] . '</h4>';
		$html_header .= '<div class="updated1">' . __( 'Total current stock value: ', 'woocommerce-jetpack' ) . wc_price( $total_current_stock_price ) . '</div>';
		$html_header .= '<div class="updated1">' . __( 'Total stock value: ', 'woocommerce-jetpack' ) . wc_price( $info['total_stock_price'] ) . '</div>';
		//$html_header .= '<div class="updated1">' . __( 'Product sales in 90 average: ', 'woocommerce-jetpack' ) . number_format( $info['sales_in_90_days_average'], 2, '.', '' ) . '</div>';
		$html_header .= '<div class="updated1">' . __( 'Product stock value average: ', 'woocommerce-jetpack' ) . wc_price( $info['stock_price_average'] ) . '</div>';
		$html_header .= '<div class="updated1">' . __( 'Product stock average: ', 'woocommerce-jetpack' ) . number_format( $info['stock_average'], 2, '.', '' ) . '</div>';


		// Report title and description
		//$html_report_title = '<h4>' . $report_info['title'] . ': ' . $report_info['desc'] . '</h4>';
		//$html_report_title = sprintf( $html_report_title, number_format( $info['sales_in_90_days_average'], 2, '.', '' ) );

		echo $html_header . $html;
	}

	/*
	 * create_reports_tool.
	 */
	public function create_reports_tool() {

		$this->reports_info = array(
			'bad_stock'	=> array(
				'id'		=> 'bad_stock',
				'title'		=> __( 'Low sales - big stock', 'woocommerce-jetpack' ),
				'desc'		=> __( 'Report shows you products with stock bigger than <span style="color:green;">%s</span> average, but with sales in last 90 days lower than average. Sorted by total stock value.', 'woocommerce-jetpack' ),
				//__( 'You should consider setting lower prices for products in table below. These products have: a) less than average sales in last 90 days, and b) larger than average stock price.', 'woocommerce-jetpack' ),
			),
			'bad_sales'	=> array(
				'id'		=> 'bad_sales',
				'title'		=> __( 'Low sales - on stock', 'woocommerce-jetpack' ),
				'desc'		=> __( 'Report shows you products on stock, but with sales in last 90 days lower than average. Sorted by total stock value.', 'woocommerce-jetpack' ),
			),
			'no_sales'	=> array(
				'id'		=> 'no_sales',
				'title'		=> __( 'No sales - on stock', 'woocommerce-jetpack' ),
				'desc'		=> __( 'Report shows you products on stock, but with not a single sale in last 90 days. Sorted by total stock value.', 'woocommerce-jetpack' ),
			),
			'most_stock_price'	=> array(
				'id'		=> 'most_stock_price',
				'title'		=> __( 'Most stock price', 'woocommerce-jetpack' ),
				'desc'		=> __( 'Report shows you products with stock bigger than average. Sorted by total stock value.', 'woocommerce-jetpack' ),
			),
			'good_sales'	=> array(
				'id'		=> 'good_sales',
				'title'		=> __( 'Good sales', 'woocommerce-jetpack' ),
				'desc'		=> __( 'Report shows you products with sales in last 90 days higher than average. Sorted by total stock value.', 'woocommerce-jetpack' ),
			),
			'good_sales_low_stock'	=> array(
				'id'		=> 'good_sales_low_stock',
				'title'		=> __( 'Good sales - low stock', 'woocommerce-jetpack' ),
				'desc'		=> __( 'Report shows you products with sales in last 90 days higher than average, but stock lower than products sales in 90 days. Sorted by total stock value.', 'woocommerce-jetpack' ),
			),
			'on_stock'	=> array(
				'id'		=> 'on_stock',
				'title'		=> __( 'on_stock', 'woocommerce-jetpack' ),
				'desc'		=> __( 'on_stock.', 'woocommerce-jetpack' ),
			),
			'any_sale'	=> array(
				'id'		=> 'any_sale',
				'title'		=> __( 'any_sale', 'woocommerce-jetpack' ),
				'desc'		=> __( 'any_sale.', 'woocommerce-jetpack' ),
			),
			'sales_up'	=> array(
				'id'		=> 'sales_up',
				'title'		=> __( 'Sales up', 'woocommerce-jetpack' ),
				'desc'		=> __( 'with sales more than average and up in last 90 days comparing to 90 days before.', 'woocommerce-jetpack' ),
				'desc_sort' => __( 'sales in 90 days', 'woocommerce-jetpack' ),
			),
			'sales_down'	=> array(
				'id'		=> 'sales_down',
				'title'		=> __( 'sales_down', 'woocommerce-jetpack' ),
				'desc'		=> __( 'sales_down.', 'woocommerce-jetpack' ),
			),
		);

		$this->output_submenu();

		if ( isset( $_GET['report'] ) ) {

			$time = microtime( true );

			$this->report = $_GET['report'];

			$this->period = 90; // default
			if ( isset( $_GET['period'] ) )
				$this->period = $_GET['period'];

			$products_info = array();
			$this->get_products_info( $products_info );
//			if ( 'most_stock_price' !== $this->report )
				$this->get_orders_info( $products_info );
			//wp_reset_postdata();
			$info = array();
			$this->count_info( $info, $products_info );

			$this->sort_products_info( $products_info, 'stock_price' );

			if ( 'sales_up' === $this->report )
				$this->sort_products_info( $products_info, 'sales_in_period', $this->period );

			if ( 'good_sales_low_stock' === $this->report )
				$this->sort_products_info( $products_info, 'stock_to_sales', $this->period );


			$this->output_report( $products_info, $info, $this->reports_info[$this->report] );

			echo 'Time Elapsed: ' . ( microtime( true ) - $time ) . 's';
			echo get_option( 'woocommerce_manage_stock' );
		}
		else {
			echo '<p>' . __( 'Here you can generate reports. Some reports are generated using all your orders and products, so if you have a lot of them - it may take a while.', 'woocommerce-jetpack' ) . '</p>';
			echo '<p>' . __( 'Reports:', 'woocommerce-jetpack' ) . '</p>';
			echo '<ul>';
			foreach ( $this->reports_info as $report => $report_info ) {
				echo '<li><a href="admin.php?page=wcj-tools&tab=reports&report=' . $report . '">' . $report_info['title'] . '</a> - ' . $report_info['desc'] . '</li>';
			}
			echo '</ul>';
		}
	}
}

endif;

return new WCJ_Reports();

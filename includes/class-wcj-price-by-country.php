<?php
/**
 * WooCommerce Jetpack Price by Country
 *
 * The WooCommerce Jetpack Price by Country class.
 *
 * @class       WCJ_Price_By_Country
 * @version		1.0.0
 * @category	Class
 * @author 		Algoritmika Ltd.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WCJ_Price_By_Country' ) ) :

class WCJ_Price_By_Country {

    /**
     * Constructor.
     */
    public function __construct() {

		$this->customer_country = null;
		$this->customer_country_group_id = null;

		$this->current_db_file_version = 4;
		
		//$this->currency_symbols = include( 'currencies/wcj-currency-symbols.php' );
		/*$currencies = include( 'currencies/wcj-currencies.php' );
		$this->currency_symbols = array_column( $currencies, 'symbol', 'code' );
		$this->currency_names = array_column( $currencies, 'name', 'code' );
		foreach( $this->currency_names as $code => $name )
			$this->currency_names_and_symbols[ $code ] = $this->currency_names[ $code ] . ' (' . $this->currency_symbols[ $code ] . ')';*/
		$currencies = include( 'currencies/wcj-currencies.php' );
		foreach( $currencies as $data ) {
			$this->currency_symbols[ $data['code'] ]           = $data['symbol'];
			//$this->currency_names[ $code ]           = $data['name'];
			$this->currency_names_and_symbols[ $data['code'] ] = $data['name'] . ' (' . $data['symbol'] . ')';		
		}				

        // Main hooks
        if ( 'yes' === get_option( 'wcj_price_by_country_enabled' ) ) {
			if ( ! is_admin() || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
				// The price
				add_filter( 'woocommerce_get_price', 						array( $this, 'change_price_by_country' ),				PHP_INT_MAX );
				add_filter( 'woocommerce_get_sale_price', 					array( $this, 'change_price_by_country' ), 				PHP_INT_MAX );
				add_filter( 'woocommerce_get_regular_price', 				array( $this, 'change_price_by_country' ), 				PHP_INT_MAX );
				add_filter( 'booking_form_calculated_booking_cost',			array( $this, 'change_price_by_country' ), 				PHP_INT_MAX );
				add_filter( 'woocommerce_get_price_html', 					array( $this, 'fix_variable_product_price_on_sale' ), 	10 , 				2 );
				// Currency
				add_filter( 'woocommerce_currency_symbol', 					array( $this, 'change_currency_symbol'), 				PHP_INT_MAX, 		2 );
				add_filter( 'woocommerce_currency', 						array( $this, 'change_currency_code'), 					PHP_INT_MAX, 		1 );
			}			
			// Reports
			add_filter( 'woocommerce_reports_get_order_report_data_args', 	array( $this, 'filter_reports'), 						PHP_INT_MAX, 		1 );			
			add_filter( 'woocommerce_currency_symbol', 						array( $this, 'change_currency_symbol_reports'), 		PHP_INT_MAX, 		2 );
			//add_filter( 'woocommerce_currency', 							array( $this, 'change_currency_code_reports'), 				PHP_INT_MAX, 		2 );
			add_action( 'admin_bar_menu', 									array( $this, 'toolbar_link_to_mypage' ), 				999 );
			
			// Debug
			add_action( 'woocommerce_after_add_to_cart_button',				array( $this, 'add_debug_info'), 						PHP_INT_MAX, 		0 );
			add_action( 'admin_init',										array( $this, 'reinstall_ip_db'), 						PHP_INT_MAX, 		0 );
        }

        // Settings hooks
        add_filter( 'wcj_settings_sections', 								array( $this, 'settings_section' ) );
        add_filter( 'wcj_settings_price_by_country', 						array( $this, 'get_settings' ), 						100 );
        add_filter( 'wcj_features_status', 									array( $this, 'add_enabled_option' ), 					100 );
    }

	public function reinstall_ip_db() {
		if ( isset( $_GET['wcj-install-ip-db'] ) && '1' == $_GET['wcj-install-ip-db'] ) {
			$this->update_database();
		}
	}

	public function add_debug_info() {
		if ( isset( $_GET['wcj-debug'] ) ) {
			echo '<input type="hidden" name="wcj-get-ip" value="'                      . $this->get_the_ip() . '" />';	
			echo '<input type="hidden" name="wcj-get-country" value="'                 . $this->get_user_country_by_ip() . '" />';
			echo '<input type="hidden" name="wcj-get-country-external" value="'        . $this->get_user_country_by_ip_external() . '" />';
			echo '<input type="hidden" name="wcj-get-country-group" value="'           . $this->get_user_country_group_id() . '" />';
			echo '<input type="hidden" name="wcj-get-country-db-cur-ver" value="'      . $this->current_db_file_version . '" />';
			echo '<input type="hidden" name="wcj-get-country-db-ver" value="'          . get_option( 'wcj_geoipcountry_db_version', 0 ) . '" />';
			echo '<input type="hidden" name="wcj-get-country-db-from-size" value="'    . count( get_option( 'wcj_geoipcountry_db_from', array() ) ) . '" />';
			echo '<input type="hidden" name="wcj-get-country-db-to-size" value="'      . count( get_option( 'wcj_geoipcountry_db_to', array() ) ) . '" />';
			echo '<input type="hidden" name="wcj-get-country-db-country-size" value="' . count( get_option( 'wcj_geoipcountry_db_country', array() ) ) . '" />';
		}
	}
	
	public function toolbar_link_to_mypage( $wp_admin_bar ) {
	
		//http://codex.wordpress.org/Function_Reference/add_node
		
		if ( isset( $_GET['page'] ) && 'wc-reports' === $_GET['page'] ) {
			$the_current_code = isset( $_GET['currency'] ) ? $_GET['currency'] : get_woocommerce_currency();
			$parent = 'reports_currency_select';
			$args = array(
				'parent' => false, 
				'id' => $parent,
				'title' => __( 'Reports currency:', 'woocommerce-jetpack' ) . ' ' . $the_current_code,
				'href'  => false,
				'meta' => array( 'title' => __( 'Show reports only in', 'woocommerce-jetpack' ) . ' ' . $the_current_code, ),
			);
			
			$wp_admin_bar->add_node( $args );
			
			$currency_symbols = array();
			$currency_symbols[ $the_current_code ] = '';
			$currency_symbols[ get_woocommerce_currency() ] = '';
			for ( $i = 1; $i <= apply_filters( 'wcj_get_option_filter', 1, get_option( 'wcj_price_by_country_total_groups_number', 1 ) ); $i++ ) {		
				$currency_symbols[ get_option( 'wcj_price_by_country_exchange_rate_currency_group_' . $i ) ] = '';
			}
			$this->reports_currency_symbols = $currency_symbols;
             
					
			foreach ( $this->reports_currency_symbols as $code => $symbol ) {
				//if ( $code === $the_current_code )
				//	continue;
				$args = array(
					'parent' => $parent, 
					'id' => $parent . '_' . $code,
					'title' => $code,// . ' ' . $symbol,
					'href'  => add_query_arg( 'currency', $code),
					'meta' => array( 'title' => __( 'Show reports only in', 'woocommerce-jetpack' ) . ' ' . $code, ),
				);
				
				$wp_admin_bar->add_node( $args );
			}	
		}			
	}
	
	/**
	 * change_currency_symbol_reports.
	 */
	public function change_currency_symbol_reports( $currency_symbol, $currency ) {
		if ( isset( $_GET['page'] ) && 'wc-reports' === $_GET['page'] ) {
			if ( isset( $_GET['currency'] ) ) {
				if ( isset( $this->currency_symbols[ strtoupper( $_GET['currency'] ) ] ) ) {
					return $this->currency_symbols[ strtoupper( $_GET['currency'] ) ];
				}
			}
		}
		return $currency_symbol;
	}
	
	/**
	 * change_currency_code_reports.
	 *
	public function change_currency_code_reports( $currency ) {
		if ( isset( $_GET['page'] ) && 'wc-reports' === $_GET['page'] ) {
			if ( isset( $_GET['currency'] ) ) {
				return $_GET['currency'];
			}
		}
		return $currency;
	}	

	/**
	 * filter_reports.
	 */
	public function filter_reports( $args ) {
		$args['where_meta'] = array(
			array(
				'meta_key' 	 => '_order_currency',
				'meta_value' => isset( $_GET['currency'] ) ? $_GET['currency'] : get_woocommerce_currency(),
				'operator' => '=',
			),
		);
		return $args;
	}
	
	/**
	 * fix_variable_product_price_on_sale.
	 */
	public function fix_variable_product_price_on_sale( $price, $product ) {
		if ( $product->is_type( 'variable' ) ) {
			if ( ! $product->is_on_sale() ) {
				$start_position = strpos( $price, '<del>' );
				$length = strpos( $price, '</del>' ) - $start_position;
				// Fixing the price, i.e. removing the sale tags
				return substr_replace( $price, '', $start_position, $length );
			}
		}
		// No changes
		return $price;
	}

	/**
	 * convert_ip_to_int.
	 */
	public function convert_ip_to_int( $ip ) {
		// Convert IP to int
		$calc = array( 16777216, 65536, 256, 1 );
		$i = 0;
		$result = 0;
		$token = strtok( $ip , '.' );
		while ( $token !== false ) {
			$result += $calc[ $i++ ] * $token;
			$token = strtok( '.' );
		}
		return $result;
	}
	
	/**
	 * str_getcsv.
	 */	
	public function parse_csv_line( $line ) {
		return explode( ',', trim( $line ) );
	}	

	/**
	 * get_country_by_ip_mysql.
	 */
	public function get_country_by_ip_mysql( $ip ) {
		global $wpdb;
		$results = $wpdb->get_results( "SELECT `country_code` FROM `{$wpdb->prefix}woojetpack_country_ip` WHERE `ip_from` <= $ip AND `ip_to` >= $ip", OBJECT );
		if ( 1 !== count( $results ) )
			return null;
		return $results[0]->country_code;
	}
	
	/**
	 * check_and_update_database.
	 */
	public function update_database() {
	
		// Started
		update_option( 'wcj_geoipcountry_db_version', -1 );
		
		ob_start();
		
		// Get IPs from file
		// This product includes GeoLite data created by MaxMind, available from <a href="http://www.maxmind.com">http://www.maxmind.com</a>.
		//$csv = array_map( array( $this, 'parse_csv_line' ), file( plugin_dir_path( __FILE__ ) . 'lib/ipdb.csv' ) );
		$csv = array_map( array( $this, 'parse_csv_line' ), file( plugin_dir_path( __FILE__ ) . 'lib/ipdb.csv' ) );
		if ( ! empty ( $csv ) ) {		
//		$the_values = file_get_contents( plugin_dir_path( __FILE__ ) . 'lib/ipdb.sql' );
//		if ( ! empty ( $the_values ) ) {
		
			global $wpdb;
		
			$charset_collate = $wpdb->get_charset_collate();
		
			$table_name = $wpdb->prefix . 'woojetpack_country_ip';
			
			//echo ( false === $wpdb->query( "DROP TABLE $table_name;" ) ) ? 'truncate-false' : 'truncate-true';
			$wpdb->query( "DROP TABLE $table_name;" );

			/*$sql = "CREATE TABLE $table_name (
				ip_from BIGINT NOT NULL,
				ip_to BIGINT NOT NULL,
				country_code text NOT NULL,
				UNIQUE KEY ip_from (ip_from)
			) $charset_collate;";*/

			$sql = "CREATE TABLE $table_name (
				ip_from BIGINT NOT NULL,
				ip_to BIGINT NOT NULL,
				country_code VARCHAR(2) NOT NULL,
				UNIQUE KEY ip_from (ip_from)
			) $charset_collate;";
			//echo ( false === $wpdb->query( $sql ) ) ? 'false' : 'true';
			$wpdb->query( $sql );
			
			//print_r( $wpdb->get_results("SELECT * FROM $table_name;") );

			//require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			//dbDelta( $sql );

			$i = 0;
			$the_values = '';
			$max_insert_size = 10000;
			$the_size = count( $csv );
			//echo $the_size;
			foreach ( $csv as $key => $data ) {
				$the_values .=  "('$data[0]', '$data[1]', '$data[2]'), ";
				//$the_values .= "('" . implode( "', '", $data ) . "'), ";
				$i++;
				if ( $i >= $max_insert_size ) {
					$the_values = rtrim( $the_values, ', ' );
					$wpdb->query( "INSERT INTO $table_name (`ip_from`, `ip_to`, `country_code`) VALUES $the_values;" );
					$i = 0;
					$the_values = '';
				}
			}
			if ( '' != $the_values ) {
				$the_values = rtrim( $the_values, ', ' );
				$wpdb->query( "INSERT INTO $table_name (`ip_from`, `ip_to`, `country_code`) VALUES $the_values;" );			
			}
			
			$count_db_table = $wpdb->get_var( "SELECT COUNT(*) FROM $table_name;" );
			//print_r( $count_db_table );
				
			if ( $the_size != $count_db_table ) {
				// Something went wrong
				update_option( 'wcj_geoipcountry_db_version', -2 );							
			}
			else {
				// Finished
				update_option( 'wcj_geoipcountry_db_version', $this->current_db_file_version );				
			}		


			// Depreciated - cleaning
			update_option( 'wcj_geoipcountry_db_from', array() );
			update_option( 'wcj_geoipcountry_db_to', array() );
			update_option( 'wcj_geoipcountry_db_country', array() );


			
		
			/**
			update_option( 'wcj_geoipcountry_db_from', array() );
			update_option( 'wcj_geoipcountry_db_to', array() );
			update_option( 'wcj_geoipcountry_db_country', array() );		
		
			foreach ( $csv as $key => $data ) {
				$column_ip_from[ $key ] = $data[0];		
				$column_ip_to[ $key ] = $data[1];	
				$column_ip_country[ $key ] = $data[2];	
			}
			
			update_option( 'wcj_geoipcountry_db_from', $column_ip_from );
			update_option( 'wcj_geoipcountry_db_to', $column_ip_to );
			update_option( 'wcj_geoipcountry_db_country', $column_ip_country );
			/**/
			
				
			/**
			global $wpdb;
			
			$sql = "CREATE TABLE {$wpdb->prefix}wcj_country_ip (
						ip_from INT(10) UNSIGNED NOT NULL, 
						ip_to INT(10) UNSIGNED NOT NULL, 
						country_code VARCHAR(2) NOT NULL
					)";			
			$results = $wpdb->get_results( $sql );			
				
			if ( ( $handle = fopen( plugin_dir_path( __FILE__ ) . 'lib/ipdb.csv', "r" ) ) !== FALSE ) {
				while ( ($data = fgetcsv( $handle, 100, "," ) ) !== FALSE ) {
					
					print_r( $wpdb->insert( 
						'$wpdb->prefix' . 'wcj_country_ip', 
						array( 
							'ip_from' => $data[0], 
							'ip_to' => $data[1] ,
							'country_code' => $data[2],
						), 
						array( 
							'%d', 
							'%d',
							'%s',
						) 
					) );
				}
				fclose( $handle );
			}
			/**/

			/**
			// IPs from
			foreach ( $csv as $key => $data )
				$column[ $key ] = $data[0];
			update_option( 'wcj_geoipcountry_db_from', $column );

			// IPs to
			foreach ( $csv as $key => $data )
				$column[ $key ] = $data[1];
			update_option( 'wcj_geoipcountry_db_to', $column );

			// Countries
			foreach ( $csv as $key => $data )
				$column[ $key ] = $data[2];
			update_option( 'wcj_geoipcountry_db_country', $column );
			/**/						
			/**
			$count_db_from = count( get_option( 'wcj_geoipcountry_db_from', array() ) );
			$count_db_to = count( get_option( 'wcj_geoipcountry_db_to', array() ) );
			$count_db_country = count( get_option( 'wcj_geoipcountry_db_country', array() ) );
			
			if ( 0 == $count_db_from || $count_db_from != $count_db_to || $count_db_from != $count_db_country ) {
				// Something went wrong
				update_option( 'wcj_geoipcountry_db_version', -2 );							
			}
			else {
				// Finished
				update_option( 'wcj_geoipcountry_db_version', $this->current_db_file_version );				
			}
			/**/
		}
				
		$output_buffer = ob_get_contents();
		ob_end_clean();
		//echo $output_buffer;
		update_option( 'wcj_geoipcountry_db_update_log', $output_buffer );	
	}

	/**
	 * check_and_update_database.
	 */
	public function check_and_update_database() {		
		$current_version = get_option( 'wcj_geoipcountry_db_version', 0 );
		if ( $current_version < 0 )
			return;
		if ( $current_version != $this->current_db_file_version ) {
			$this->update_database();
		}
	}

	/**
	 * search_for_nearest_below_value.
	 * returns index on success, otherwise null
	 */
	public function search_for_nearest_below_value( $db, $value_to_search ) {

		$count = count( $db );
		if( 0 == $count )
			return null;

		$div_step               = 2;
		$index                  = ceil( $count / $div_step );		
		$best_index             = null;
		$best_score             = PHP_INT_MAX;
		$direction              = null;
		$indexes_checked        = Array();

		while( true ) {
		
			// Stop if already checked
			if( isset( $indexes_checked[ $index ] ) ) {
				break ;
			}

			// Get current value
			$curr_value = $db[ $index ];
			// Stop if current value no set
			if( $curr_value === null ) {
				break ;
			}

			// Mark as already checked
			$indexes_checked[ $index ] = true;

			// Perfect match, nothing else to do
			if($curr_value == $value_to_search) {
				$best_index = $index;
				break;
			}

			// Calculate current score and direction
			$curr_score = $curr_value - $value_to_search;
			if( $curr_score > 0 ) {
				$curr_score = null;
				$direction = -1;
			}
			else {
				$curr_score = abs( $curr_score );
				$direction = 1;
			}

			// Save best index if: lower than value and better score than previous best, or first best
			if ( ( $curr_score !== null ) && ( $curr_score < $best_score ) ) {
				$best_index = $index;
				$best_score = $curr_score;
			}

			// Get next index
			$div_step *= 2;
			$index += $direction * ceil( $count / $div_step );
		}

		return $best_index;
	}

	/**
	 * get_the_ip.
	 */
	public function get_the_ip( ) {
		$ip = null;
		// Code from http://stackoverflow.com/questions/3003145/how-to-get-the-client-ip-address-in-php
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		return $ip;
	}
	
	/**
	 * get_user_country_by_ip.
	 */
	public function get_user_country_by_ip( ) {

		// We already know the country - nothing to calculate - return country
		if ( null != $this->customer_country )
			return $this->customer_country;

		// Default value
		//$this->customer_country = WC()->countries->get_base_country();

		// Get user IP
		$customer_ip = $this->get_the_ip();

		// Convert IP to int
		$ip_as_int = $this->convert_ip_to_int( $customer_ip );

		// Update DB if needed
		$this->check_and_update_database();

		// Search for IP in DB
		return $this->get_country_by_ip_mysql( $ip_as_int );
		/*
		// Search for IP in DB
		$db = get_option( 'wcj_geoipcountry_db_from', array() );
		$index = $this->search_for_nearest_below_value( $db, $ip_as_int );

		if ( null !== $index ) {
			// Check for upper limit
			$db = get_option( 'wcj_geoipcountry_db_to', array() );
			if ( $ip_as_int <= $db[ $index ] ) {
				// Get the country
				$db = get_option( 'wcj_geoipcountry_db_country', array() );
				$this->customer_country = $db[ $index ];
				return $db[ $index ];
			}
		}
		

		return null;*/
	}
	
	public function get_user_country_by_ip_external() {
		ob_start();
		$country = file_get_contents( 'http://api.hostip.info/country.php?ip=' . $this->get_the_ip() );
		//$json = file_get_contents( 'http://api.hostip.info/country.php?ip=' . $this->get_the_ip() );//file_get_contents( 'http://ipinfo.io/' . $this->get_the_ip() . '/country' );
		//$country = json_decode( $json );		
		ob_end_clean();		
		return $country;
	}	

	/**
	 * get_user_country_group_id.
	 */
	public function get_user_country_group_id() {
	
		// We already know the group - nothing to calculate - return group
		if ( null != $this->customer_country_group_id )
			return $this->customer_country_group_id;
		
		// Get the country by IP
		switch( get_option( 'wcj_price_by_country_by_ip_detection_type', 'internal' ) ) {
			case 'internal':
				$country = $this->get_user_country_by_ip();	
				break;
			case 'hostip_info':
				$country = $this->get_user_country_by_ip_external();
				break;
		}
		
		if ( null === $country )
			return null;

		// Go through all the groups, first found group is returned
		for ( $i = 1; $i <= apply_filters( 'wcj_get_option_filter', 1, get_option( 'wcj_price_by_country_total_groups_number', 1 ) ); $i++ ) {		
			$country_exchange_rate_group = get_option( 'wcj_price_by_country_exchange_rate_countries_group_' . $i );
			$country_exchange_rate_group = str_replace( ' ', '', $country_exchange_rate_group );
			$country_exchange_rate_group = explode( ',', $country_exchange_rate_group );
			if ( in_array( $country, $country_exchange_rate_group ) ) {
				$this->customer_country_group_id = $i;
				return $i;
			}
		}

		// Nothing found
		return null;
	}

	/**
	 * change_currency_symbol.
	 */
	public function change_currency_symbol( $currency_symbol, $currency ) {
		if ( null != ( $group_id = $this->get_user_country_group_id() ) ) {
			$country_currency_symbol = get_option( 'wcj_price_by_country_exchange_rate_currency_group_' . $group_id );
			if ( '' != $country_currency_symbol )
				return $this->currency_symbols[ $country_currency_symbol ];
		}
		return $currency_symbol;
	}
	
	/**
	 * change_currency_code.
	 */
	public function change_currency_code( $currency ) {
		if ( null != ( $group_id = $this->get_user_country_group_id() ) ) {
			$country_currency_symbol = get_option( 'wcj_price_by_country_exchange_rate_currency_group_' . $group_id );
			if ( '' != $country_currency_symbol )
				return $country_currency_symbol;
		}
		return $currency;
	}

	/**
	 * change_price_by_country.
	 */
	public function change_price_by_country( $price ) {
		if ( null != ( $group_id = $this->get_user_country_group_id() ) ) {
			$country_exchange_rate = get_option( 'wcj_price_by_country_exchange_rate_group_' . $group_id, 1 );
			if ( 1 != $country_exchange_rate ) {
				$modified_price = $price * $country_exchange_rate;
				$rounding = get_option( 'wcj_price_by_country_rounding', 'none' );				
				$precision = get_option( 'woocommerce_price_num_decimals', 2 );
				switch ( $rounding ) {
					case 'none':
						//return ( $modified_price );
						return round( $modified_price, $precision );
					case 'round':						
						return round( $modified_price );
					case 'floor':
						return floor( $modified_price );
					case 'ceil':
						return ceil( $modified_price );					
				}
			}
		}
		// No changes
		return $price;
	}
	
    /**
     * get_ip_db_status_html.
     */
    public function get_ip_db_status_html() {
		$installed_db_version = get_option( 'wcj_geoipcountry_db_version', 0 );
		if ( $this->current_db_file_version != $installed_db_version ) {		
			if ( $installed_db_version < 0 )
				$installed_db_version = abs( $installed_db_version ) + 10000;
			return __( 'IP DB not installed', 'woocommerce-jetpack' ) . ' (' . $installed_db_version . ').'
				   . ' ' . '<a href="' . add_query_arg( 'wcj-install-ip-db', '1' ) . '">' . __( 'Fix', 'woocommerce-jetpack' ) . '</a>';
		}
		else
			return __( 'IP DB version: ', 'woocommerce-jetpack' ) . $this->current_db_file_version;
    }	

    /**
     * add_enabled_option.
     */
    public function add_enabled_option( $settings ) {
        $all_settings = $this->get_settings();
        $settings[] = $all_settings[1];
        return $settings;
    }

    /**
     * get_settings.
     */
    function get_settings() {
	
        $settings = array(

            array( 
				'title' => __( 'Price by Country Options', 'woocommerce-jetpack' ), 
				'type' => 'title', 
				'desc' => __( 'Change product\'s price and currency by customer\'s country. Customer\'s country is detected automatically by IP.', 'woocommerce-jetpack' )
						  . '<br>'
						  . '<span style="color:gray;font-size:smaller;">'
						  . $this->get_ip_db_status_html()
						  . '</span>',
				'id' => 'wcj_price_by_country_options' ),

            array(
                'title'    => __( 'Prices and Currencies by Country', 'woocommerce-jetpack' ),
                'desc'     => '<strong>' . __( 'Enable Module', 'woocommerce-jetpack' ) . '</strong>',
                'desc_tip' => __( 'Change product\'s price and currency automatically by customer\'s country.', 'woocommerce-jetpack' ),
                'id'       => 'wcj_price_by_country_enabled',
                'default'  => 'no',
                'type'     => 'checkbox',
            ),
			
            array(
                'title'    => __( 'Country by IP Method', 'woocommerce-jetpack' ),
                'desc'     => __( 'Select which method to use for detecting customers country by IP.', 'woocommerce-jetpack' ),
                'id'       => 'wcj_price_by_country_by_ip_detection_type',
                'default'  => 'internal',
                'type'     => 'select',
				'options'  => array(
								'internal'    => __( 'Internal DB (recommended)', 'woocommerce-jetpack' ),
								'hostip_info' => __( 'External server:', 'woocommerce-jetpack' ) . ' '  . 'api.hostip.info',
				),
            ),				
			
            array(
                'title'    => __( 'Price Rounding', 'woocommerce-jetpack' ),
                'desc'     => __( 'If you choose to multiply price, set rounding options here.', 'woocommerce-jetpack' ),
                'id'       => 'wcj_price_by_country_rounding',
                'default'  => 'none',
                'type'     => 'select',
				'options'  => array(
								'none'  => __( 'No rounding', 'woocommerce-jetpack' ),
								'round' => __( 'Round', 'woocommerce-jetpack' ),
								'floor' => __( 'Round down', 'woocommerce-jetpack' ),
								'ceil'  => __( 'Round up', 'woocommerce-jetpack' ),
				),
            ),			

            array( 'type'  => 'sectionend', 'id' => 'wcj_price_by_country_options' ),

			array( 'title' => __( 'Country Groups', 'woocommerce-jetpack' ), 'type' => 'title', 'desc' => '', 'id' => 'wcj_price_by_country_exchange_rate_options' ),

            array(
                'title'    => __( 'Groups Number', 'woocommerce-jetpack' ),
                'id'       => 'wcj_price_by_country_total_groups_number',
                'default'  => 1,
                'type'     => 'number',				
				'desc'     => apply_filters( 'get_wc_jetpack_plus_message', '', 'desc' ),
				'custom_attributes'
				           => array_merge(
								is_array( apply_filters( 'get_wc_jetpack_plus_message', '', 'readonly' ) ) ? apply_filters( 'get_wc_jetpack_plus_message', '', 'readonly' ) : array(),
								array(
									'step' 	=> '1',
									'min'	=> '1',
								) ),
				),
		);

		for ( $i = 1; $i <= apply_filters( 'wcj_get_option_filter', 1, get_option( 'wcj_price_by_country_total_groups_number', 1 ) ); $i++ ) {		

            $settings[] = array(
                'title'    => __( 'Group', 'woocommerce-jetpack' ) . ' #' . $i,
				'desc'	   => __( 'Countries. List of comma separated country codes.<br>For country codes and predifined sets visit <a href="http://woojetpack.com/features/prices-and-currencies-by-customers-country">WooJetpack.com</a>', 'woocommerce-jetpack' ),
                'id'       => 'wcj_price_by_country_exchange_rate_countries_group_' . $i,
                'default'  => '',
                'type'     => 'textarea',
				'css'	   => 'width:50%;min-width:300px;height:100px;',
            );

            $settings[] = array(
                'title'    => '',
				'desc'	   => __( 'Multiply Price by', 'woocommerce-jetpack' ),
                'id'       => 'wcj_price_by_country_exchange_rate_group_' . $i,
				'default'  => 1,
				'type'     => 'number',
				'css'	   => 'width:100px;',
				'custom_attributes'	=> array(
					'step' 	=> '0.000001',
					'min'	=> '0',
				),
            );

            $settings[] = array(
                'title'    => '',
				'desc'	   => __( 'Currency', 'woocommerce-jetpack' ),
                'id'       => 'wcj_price_by_country_exchange_rate_currency_group_' . $i,
                'default'  => 'EUR',
                'type'     => 'select',
				'options'  => $this->currency_names_and_symbols,
            );
		}
		$settings[] = array( 'type'  => 'sectionend', 'id' => 'wcj_price_by_country_exchange_rate_options' );

        return $settings;
    }

    /**
     * settings_section.
     */
    function settings_section( $sections ) {
        $sections['price_by_country'] = __( 'Prices and Currencies by Country', 'woocommerce-jetpack' );
        return $sections;
    }
}

endif;

return new WCJ_Price_By_Country();

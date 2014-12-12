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

		$this->current_db_file_version = 1;
		
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
				add_filter( 'woocommerce_get_price_html', 					array( $this, 'fix_variable_product_price_on_sale' ), 	10 , 				2 );
				// Currency
				add_filter( 'woocommerce_currency_symbol', 					array( $this, 'change_currency_symbol'), 				PHP_INT_MAX, 		2 );
				add_filter( 'woocommerce_currency', 						array( $this, 'change_currency_code'), 					PHP_INT_MAX, 		1 );
			}			
			// Reports
			add_filter( 'woocommerce_reports_get_order_report_data_args', 	array( $this, 'filter_reports'), 						PHP_INT_MAX, 		1 );			
			add_filter( 'woocommerce_currency_symbol', 						array( $this, 'change_currency_symbol_reports'), 		PHP_INT_MAX, 		2 );
			//add_filter( 'woocommerce_currency', 							array( $this, 'change_currency_code_reports'), 				PHP_INT_MAX, 		2 );
			add_action( 'admin_bar_menu', array( $this, 'toolbar_link_to_mypage' ), 999 );

        }

        // Settings hooks
        add_filter( 'wcj_settings_sections', 								array( $this, 'settings_section' ) );
        add_filter( 'wcj_settings_price_by_country', 						array( $this, 'get_settings' ), 						100 );
        add_filter( 'wcj_features_status', 									array( $this, 'add_enabled_option' ), 					100 );
    }
	

	/**/
	public function toolbar_link_to_mypage( $wp_admin_bar ) {
		//http://codex.wordpress.org/Function_Reference/add_node
		
		
		if ( isset( $_GET['page'] ) && 'wc-reports' === $_GET['page'] ) {
			$the_current_code = isset( $_GET['currency'] ) ? $_GET['currency'] : get_woocommerce_currency();
			$parent = 'reports_currency_select';
			$args = array(
				'parent' => false, 
				'id' => $parent,
				'title' => 'Reports currency: ' . $the_current_code,
				'href'  => false,
				'meta' => array( 'title' => __( 'Show reports only in', 'woocommerce-jetpack' ) . ' ' . $the_current_code, ),
			);
			
			$wp_admin_bar->add_node( $args );
			//$wp_admin_bar->add_menu
			
			
			
			$this->reports_currency_symbols = array( 'GBP' => '&pound;', 'USD' => '&#36;' );
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
	 * check_and_update_database.
	 */
	public function check_and_update_database() {

		// This product includes GeoLite data created by MaxMind, available from <a href="http://www.maxmind.com">http://www.maxmind.com</a>.
		$current_version = get_option( 'wcj_geoipcountry_db_version', 0 );
		if ( -1 == $current_version )
			return;
		if ( $current_version < $this->current_db_file_version ) {

			// Updating DB - started
			update_option( 'wcj_geoipcountry_db_version', -1 );
		
			// Updating DB - get IPs from file
			$csv = array_map( 'str_getcsv', file( plugin_dir_path( __FILE__ ) . 'lib/ipdb.csv' ) );

			// Updating DB - IPs from
			foreach ( $csv as $key => $data )
				$column[ $key ] = $data[0];
			update_option( 'wcj_geoipcountry_db_from', $column );

			// Updating DB - IPs to
			foreach ( $csv as $key => $data )
				$column[ $key ] = $data[1];
			update_option( 'wcj_geoipcountry_db_to', $column );

			// Updating DB - Countries
			foreach ( $csv as $key => $data )
				$column[ $key ] = $data[2];
			update_option( 'wcj_geoipcountry_db_country', $column );

			// Updating DB - version
			update_option( 'wcj_geoipcountry_db_version', $this->current_db_file_version );
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

		return null;
	}

	/**
	 * get_user_country_group_id.
	 */
	public function get_user_country_group_id() {
	
		// We already know the group - nothing to calculate - return group
		if ( null != $this->customer_country_group_id )
			return $this->customer_country_group_id;
		
		// Get the country by IP
		$country = $this->get_user_country_by_ip();
		if ( null === $country )
			return null;

		// Go through all the groups, first found group is returned
		for ( $i = 1; $i <= apply_filters( 'wcj_get_option_filter', 1, get_option( 'wcj_price_by_country_total_groups_number', 1 ) ); $i++ ) {		
			$country_exchange_rate_group = get_option( 'wcj_price_by_country_exchange_rate_countries_group_' . $i );
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
			return ( $price * $country_exchange_rate );
		}
		return $price;
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

            array( 'title' => __( 'Price by Country Options', 'woocommerce-jetpack' ), 'type' => 'title', 'desc' => __( 'Change product\'s price and currency by customer\'s country. Customer\'s country is detected automatically by IP.', 'woocommerce-jetpack' ), 'id' => 'wcj_price_by_country_options' ),

            array(
                'title'    => __( 'Prices and Currencies by Country', 'woocommerce-jetpack' ),
                'desc'     => __( 'Enable the Price by Country feature', 'woocommerce-jetpack' ),
                'desc_tip' => __( 'Change product\'s price and currency by customer\'s country.', 'woocommerce-jetpack' ),
                'id'       => 'wcj_price_by_country_enabled',
                'default'  => 'no',
                'type'     => 'checkbox',
            ),

            array( 'type'  => 'sectionend', 'id' => 'wcj_price_by_country_options' ),

			array( 'title' => __( 'Exchange rates', 'woocommerce-jetpack' ), 'type' => 'title', 'desc' => '', 'id' => 'wcj_price_by_country_exchange_rate_options' ),

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

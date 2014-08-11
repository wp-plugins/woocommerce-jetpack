<?php
/**
 * WooCommerce Jetpack PDF Invoices
 *
 * The WooCommerce Jetpack PDF Invoices class.
 *
 * @class        WCJ_PDF_Invoices
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
 
if ( ! class_exists( 'WCJ_PDF_Invoices' ) ) :
 
class WCJ_PDF_Invoices {
    
    /**
     * Constructor.
     */
    public function __construct() {
    
        // Main hooks
        if ( get_option( 'wcj_pdf_invoices_enabled' ) == 'yes' ) {
		
			add_filter( 'woocommerce_admin_order_actions', array( $this, 'add_pdf_invoices_link_to_order_list' ), 100, 2 );
			
			add_action( 'init', array( $this, 'generate_pdf' ), 10 );
			//wp_ajax_
			
			add_action( 'admin_head', array( $this, 'add_custom_css' ) );
			
			if ( get_option( 'wcj_pdf_invoices_enabled_for_customers' ) == 'yes' )
				add_filter( 'woocommerce_my_account_my_orders_actions', array( $this, 'add_pdf_invoices_link_to_my_account' ), 100, 2 );
				//add_filter( apply_filters( 'wcj_get_option_filter', 'wcj_empty_filter', 'woocommerce_my_account_my_orders_actions' ), array( $this, 'add_pdf_invoices_link_to_my_account' ), 100, 2 );
        }
		
		//$this->generate_pdf();
    
        // Settings hooks
        add_filter( 'wcj_settings_sections', array( $this, 'settings_section' ) );
        add_filter( 'wcj_settings_pdf_invoices', array( $this, 'get_settings' ), 100 );
        add_filter( 'wcj_features_status', array( $this, 'add_enabled_option' ), 100 );
    }
	
    /**
     * Unlocks - PDF Invoices - add_pdf_invoices_link_to_my_account.
     */
    public function add_pdf_invoices_link_to_my_account( $actions, $the_order ) {

		$actions['pdf_invoice'] = array(
			'url'  => $_SERVER['REQUEST_URI'] . '?pdf_invoice=' . $the_order->id,
			'name' => __( 'Invoice', 'woocommerce-jetpack' ),
		);

        return $actions;
    }	
	
    /**
     * add_custom_css.
     */
	function add_custom_css() {
	
		echo '<style> a.button.tips.view.pdf_invoice:after { content: "\e028" !important; } </style>';
		//echo '<style> a.button.tips.view.save_pdf_invoice:after { content: "\e028" !important; } </style>';
	}
	
    /**
     * generate_pdf.
     */
    public function generate_pdf() {
	
		if ( ! isset( $_GET['pdf_invoice'] ) ) return;

		if ( ! is_user_logged_in() ) return;
		
		$order_id = $_GET['pdf_invoice'];
		$the_order = new WC_Order( $order_id );		
		$the_items = $the_order->get_items();

		if ( ( ! current_user_can( 'administrator' ) ) && ( get_current_user_id() != intval( get_post_meta( $order_id, '_customer_user', true ) ) ) ) return;
		
		// Include the main TCPDF library (search for installation path).		
		//require_once('tcpdf_include.php');
		require_once( 'tcpdf_min/tcpdf.php' );

		// create new PDF document
		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

		// set document information
		$pdf->SetCreator( PDF_CREATOR );
		//$pdf->SetAuthor( 'Algoritmika Ltd.' );
		$pdf->SetTitle( 'Invoice' );
		$pdf->SetSubject( 'Invoice PDF' );
		$pdf->SetKeywords( 'invoice, PDF' );

		// set default header data
		//$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 001', PDF_HEADER_STRING, array(0,64,255), array(0,64,128));
		//$pdf->SetHeaderData( get_option( 'wcj_pdf_invoices_seller_logo_url' ), 30, get_option( 'wcj_pdf_invoices_header_title' ), get_option( 'wcj_pdf_invoices_header_string' ), array(0,64,255), array(0,64,128));
		$pdf->SetPrintHeader(false);
		$pdf->setFooterData(array(0,64,0), array(0,64,128));
		//$pdf->SetPrintFooter(false);

		// set header and footer fonts
		$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

		// set default monospaced font
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

		// set margins
		$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

		// set auto page breaks
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

		// set image scale factor
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

		/*
		// set some language-dependent strings (optional)
		if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
			require_once(dirname(__FILE__).'/lang/eng.php');
			$pdf->setLanguageArray($l);
		}
		*/

		// ---------------------------------------------------------

		// set default font subsetting mode
		$pdf->setFontSubsetting(true);

		// Set font
		// dejavusans is a UTF-8 Unicode font, if you only need to
		// print standard ASCII chars, you can use core fonts like
		// helvetica or times to reduce file size.
		$pdf->SetFont('dejavusans', '', 12, '', true);

		// Add a page
		// This method has several options, check the source code documentation for more information.
		$pdf->AddPage();

		// set text shadow effect
		$pdf->setTextShadow(array('enabled'=>true, 'depth_w'=>0.2, 'depth_h'=>0.2, 'color'=>array(196,196,196), 'opacity'=>1, 'blend_mode'=>'Normal'));

		$html = '<style>
			.custom_table { font-size: smaller; padding: 10px; width: 100%; }
			.custom_table td { border: 1px solid #F0F0F0; }
			.custom_table th { border: 1px solid #F0F0F0; font-weight: bold; font-size: small; }
		</style>';
		
		//$html .= print_r( $the_order , true );
		
		// HEADER TEXT (AND IF SET - LOGO) //
		if ( get_option( 'wcj_pdf_invoices_seller_logo_url' ) !== '' ) 
			//$html .= get_option( 'wcj_pdf_invoices_seller_logo_url' );
			$html .= '<p><img src="' . get_option( 'wcj_pdf_invoices_seller_logo_url' ) . '"><div style="text-align:right;color:gray;font-weight:bold;">' . get_option( 'wcj_pdf_invoices_header_text' ) . '</div></p>';
		else
			$html .= '<div style="text-align:right;color:gray;font-weight:bold;">' . get_option( 'wcj_pdf_invoices_header_text' ) . '</div>';
		
		$order_number = $the_order->get_order_number();
		
		// NUMBER AND DATE //
		$html .= '<table><tbody>';
		$html .= '<tr><td>' . get_option( 'wcj_pdf_invoices_invoice_number_text' ) . '</td><td>' . $order_number . '</td></tr>';
		$html .= '<tr><td>' . get_option( 'wcj_pdf_invoices_invoice_date_text' ) . '</td><td>' . date( get_option('date_format') , strtotime( $the_order->order_date ) ) . '</td></tr>';
		$html .= '</tbody></table>';		
		
		// SELLER AND BUYER INFO //
		$html .= '<p><table><tbody>';
		$html .= '<tr><td>';
		$html .= '<h2>' . get_option( 'wcj_pdf_invoices_seller_text' ) . '</h2>';
		$html .= str_replace( PHP_EOL, '<br>', get_option( 'wcj_pdf_invoices_seller_info' ) );
		$html .= '</td><td>';
		$html .= '<h2>' . get_option( 'wcj_pdf_invoices_buyer_text' ) . '</h2>';
		$html .= $the_order->get_formatted_billing_address();
		$html .= '</td></tr></tbody></table></p>';				
				
		// ITEMS //
		$html .= '<h2>' . get_option( 'wcj_pdf_invoices_items_text' ) . '</h2>';
		$html .= '<table class="custom_table"><tbody>';
		$html .= '<tr>';
		$html .= '<th style="width:10%;">' . get_option( 'wcj_pdf_invoices_column_nr_text' ) . '</th>';
		//if ( 0 != $the_order->get_line_tax( $item ) )
		if ( $the_order->get_total_tax() != 0 )
			$html .= '<th style="width:50%;">' . get_option( 'wcj_pdf_invoices_column_item_name_text' ) . '</th>';
		else
			$html .= '<th style="width:65%;">' . get_option( 'wcj_pdf_invoices_column_item_name_text' ) . '</th>';
		$html .= '<th style="width:10%;">' . get_option( 'wcj_pdf_invoices_column_qty_text' ) . '</th>';
		//if ( 0 !== $the_order->get_line_tax( $item ) )
		//if ( $the_order->get_line_tax( $item ) != 0 )
		if ( $the_order->get_total_tax() != 0 )
			$html .= '<th style="width:15%;">' . get_option( 'wcj_pdf_invoices_column_price_tax_excl_text' ) . '</th>';
		$html .= '<th style="width:15%;">' . get_option( 'wcj_pdf_invoices_column_price_text' ) . '</th>';
		$html .= '</tr>';
		$item_counter = 0;
		foreach ( $the_items as $item ) {
		
			
			
			$item_counter++;
			
			//$html .= '<li>';
			$html .= '<tr>';
			
			//$html .= '<td>' . $the_order->get_line_tax( $item ) . '</td>';
			
			$html .= '<td>' . $item_counter . '</td>';
		
			$html .= '<td>' . $item['name'];
			
			$product = $the_order->get_product_from_item( $item );
			
			//$html .= print_r($product , true );
			
			if ( isset ( $product->variation_data ) ) {			
			
				foreach ( $product->variation_data as $key => $value ) {
				
					$taxonomy_name = str_replace( 'attribute_', '', $key );
					$taxonomy = get_taxonomy( $taxonomy_name );
					$term = get_term_by( 'slug', $value, $taxonomy_name );
					if ( isset( $term->name ) ) $html .= '<div style="font-size:x-small;">' . $taxonomy->label . ': ' . $term->name . '</div>';
				}
			}			
			$html .= '</td>';
			
			$html .= '<td>' . $item['qty'] . '</td>';
			//if ( '' !== $the_order->get_formatted_line_subtotal( $item, 'excl' ) ) $html .= '<td>' . $the_order->get_formatted_line_subtotal( $item, 'excl' ) . '</td>';
			if ( $the_order->get_line_tax( $item ) != 0 )
				$html .= '<td>' . wc_price( $the_order->get_line_subtotal( $item ), array( 'currency' => $the_order->get_order_currency() ) ) . '</td>';
			$html .= '<td>' . $the_order->get_formatted_line_subtotal( $item, 'incl' ) . '</td>';
			//$html .= '</li>';
			$html .= '</tr>';
		}
		//$html .= '</ol>';
		$html .= '</tbody></table>';
			
			
		// ORDER TOTALS //
		//$html .= '<h3>Order total: ' . $the_order->get_formatted_order_total() . '</h3>';		
		$html .= '<p><table style="font-size: smaller; padding: 10px; width: 100%;"><tbody>';
		
		// SUBTOTAL
		//$html .= '<tr><td colspan="3"></td><td style="border: 1px solid #F0F0F0;">' . get_option( 'wcj_pdf_invoices_order_subtotal_text' ) . '</td><td style="border: 1px solid #F0F0F0;">' . $the_order->get_subtotal_to_display( false, 'excl' ) . '</td></tr>';
		$subtotal = wc_price( ( $the_order->get_total() - $the_order->get_total_tax() - $the_order->get_total_shipping() - $the_order->get_total_discount() ), array( 'currency' => $the_order->get_order_currency() ) );   //$the_order->get_total_discount() $the_order->get_total_shipping()
		$html .= '<tr><td colspan="3"></td><td style="border: 1px solid #F0F0F0;">' . get_option( 'wcj_pdf_invoices_order_subtotal_text' ) . '</td><td style="border: 1px solid #F0F0F0;">' . $subtotal . '</td></tr>';
				
		// SHIPPING
		if ( $the_order->get_total_shipping() != 0 )
			$html .= '<tr><td colspan="3"></td><td style="border: 1px solid #F0F0F0;">' . get_option( 'wcj_pdf_invoices_order_shipping_text' ) . '</td><td style="border: 1px solid #F0F0F0;">' .  wc_price( $the_order->get_total_shipping(), array( 'currency' => $the_order->get_order_currency() ) ) . '</td></tr>';		
	
		// DISCOUNT
		if ( $the_order->get_total_discount() != 0 )
			$html .= '<tr><td colspan="3"></td><td style="border: 1px solid #F0F0F0;">' . get_option( 'wcj_pdf_invoices_order_discount_text' ) . '</td><td style="border: 1px solid #F0F0F0;">' .  wc_price( $the_order->get_total_discount(), array( 'currency' => $the_order->get_order_currency() ) ) . '</td></tr>';				
		//
		
		// TAXES
		foreach ( $the_order->get_tax_totals() as $tax_object )
			$html .= '<tr><td colspan="3"></td><td style="border: 1px solid #F0F0F0;">' . $tax_object->label . '</td><td style="border: 1px solid #F0F0F0;">' . $tax_object->formatted_amount . '</td></tr>';
			
		// TOTAL
		$html .= '<tr><td colspan="3"></td><td style="border: 1px solid #F0F0F0;">' . get_option( 'wcj_pdf_invoices_order_total_text' ) . '</td><td style="border: 1px solid #F0F0F0;">' . $the_order->get_formatted_order_total() . '</td></tr>';
		
		$html .= '</tbody></table></p>';
		
//		$html .= '<p>' . get_option( 'wcj_pdf_invoices_order_shipping_text' ) . ': ' . $the_order->get_shipping_to_display( 'incl' ) . '</p>';
		
//		$html .= '<p>' . wc_price( $the_order->get_total_shipping(), array( 'currency' => $the_order->get_order_currency() ) ) . '</p>';
		
		//$html .= print_r($the_order , true );
		
		//$html .= '</pre>';


		// Print text using writeHTMLCell()
		$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

		// ---------------------------------------------------------

		// Close and output PDF document
		// This method has several options, check the source code documentation for more information.
		if ( isset( $_GET['save_pdf_invoice'] ) && ( $_GET['save_pdf_invoice'] == '1' ) )
			$pdf->Output('invoice-' . $order_number . '.pdf', 'D');
		else
			$pdf->Output('invoice-' . $order_number . '.pdf', 'I');
	}	

    /**
     * add_pdf_invoices_link_to_order_list.
     */
    public function add_pdf_invoices_link_to_order_list( $actions, $the_order ) {
    
		$actions['pdf_invoice'] = array(
			'url' 		=> basename( $_SERVER['REQUEST_URI'] ) . '&pdf_invoice=' . $the_order->id,
			'name' 		=> __( 'PDF', 'woocommerce-jetpack' ),
			'action' 	=> "view pdf_invoice"
		);
		
		/*$actions['save_pdf_invoice'] = array(
			'url' 		=> basename( $_SERVER['REQUEST_URI'] ) . '&pdf_invoice=' . $the_order->id . '&save_pdf_invoice=1',
			'name' 		=> __( 'Save PDF', 'woocommerce-jetpack' ),
			'action' 	=> "view save_pdf_invoice"
		);*/
   
        return $actions;
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
 
            array( 'title' => __( 'PDF Invoices Options', 'woocommerce-jetpack' ), 'type' => 'title', 'desc' => __( 'PDF Invoices.', 'woocommerce-jetpack' ), 'id' => 'wcj_pdf_invoices_options' ),
            
            array(
                'title'    => __( 'PDF Invoices', 'woocommerce-jetpack' ),
                'desc'     => __( 'Enable the PDF Invoices feature', 'woocommerce-jetpack' ),
                'desc_tip' => __( 'Add PDF invoices for the store owners and for the customers.', 'woocommerce-jetpack' ),
                'id'       => 'wcj_pdf_invoices_enabled',
                'default'  => 'yes',
                'type'     => 'checkbox',
            ),
			
            array(
                'title'    => __( 'Your Logo URL', 'woocommerce-jetpack' ),
                'desc'     => __( 'Enter a URL to an image you want to show in the invoice\'s header. Upload your image using the <a href="/wp-admin/media-new.php">media uploader</a>.', 'woocommerce-jetpack' ),
                'desc_tip' => __( 'Header image', 'woocommerce-jetpack' ),
                'id'       => 'wcj_pdf_invoices_seller_logo_url',
                'default'  => '',
                'type'     => 'text',
				'css'	   => 'width:33%;min-width:300px;',
            ),
			
            array(
                'title'    => __( 'Header Text', 'woocommerce-jetpack' ),
                'desc_tip' => __( 'Header text', 'woocommerce-jetpack' ),
                'id'       => 'wcj_pdf_invoices_header_text',
                'default'  => __( 'INVOICE', 'woocommerce-jetpack' ),
                'type'     => 'text',
				'css'	   => 'width:33%;min-width:300px;',
            ),
			
            array(
                'title'    => __( 'Invoice Number Text', 'woocommerce-jetpack' ),
                'desc_tip' => __( 'Invoice number text', 'woocommerce-jetpack' ),
                'id'       => 'wcj_pdf_invoices_invoice_number_text',
                'default'  => __( 'Invoice number', 'woocommerce-jetpack' ),
                'type'     => 'text',
				'css'	   => 'width:33%;min-width:300px;',
            ),

            array(
                'title'    => __( 'Invoice Date Text', 'woocommerce-jetpack' ),
                'desc_tip' => __( 'Invoice date text', 'woocommerce-jetpack' ),
                'id'       => 'wcj_pdf_invoices_invoice_date_text',
                'default'  => __( 'Invoice date', 'woocommerce-jetpack' ),
                'type'     => 'text',
				'css'	   => 'width:33%;min-width:300px;',
            ),			
			
            array(
                'title'    => __( 'Seller Text', 'woocommerce-jetpack' ),
                'desc_tip' => __( 'Seller text', 'woocommerce-jetpack' ),
                'id'       => 'wcj_pdf_invoices_seller_text',
                'default'  => __( 'Seller', 'woocommerce-jetpack' ),
                'type'     => 'text',
				'css'	   => 'width:33%;min-width:300px;',
            ),			
			
            array(
                'title'    => __( 'Your business information', 'woocommerce-jetpack' ),
                'desc_tip' => __( 'Seller information', 'woocommerce-jetpack' ),
                'id'       => 'wcj_pdf_invoices_seller_info',
                'default'  => __( '<strong>Company Name</strong>', 'woocommerce-jetpack' ),
				'type'     => 'textarea',
				'css'	   => 'width:33%;min-width:300px;min-height:300px;',
            ),			
			
            array(
                'title'    => __( 'Buyer Text', 'woocommerce-jetpack' ),
                'desc_tip' => __( 'Buyer text', 'woocommerce-jetpack' ),
                'id'       => 'wcj_pdf_invoices_buyer_text',
                'default'  => __( 'Buyer', 'woocommerce-jetpack' ),
                'type'     => 'text',
				'css'	   => 'width:33%;min-width:300px;',
            ),
			
            array(
                'title'    => __( 'Items Text', 'woocommerce-jetpack' ),
                'desc_tip' => __( 'Items text', 'woocommerce-jetpack' ),
                'id'       => 'wcj_pdf_invoices_items_text',
                'default'  => __( 'Items', 'woocommerce-jetpack' ),
                'type'     => 'text',
				'css'	   => 'width:33%;min-width:300px;',
            ),			

            array(
                'title'    => __( 'Column - Nr. Text', 'woocommerce-jetpack' ),
                'desc_tip' => __( 'Nr. text', 'woocommerce-jetpack' ),
                'id'       => 'wcj_pdf_invoices_column_nr_text',
                'default'  => __( 'Nr.', 'woocommerce-jetpack' ),
                'type'     => 'text',
				'css'	   => 'width:33%;min-width:300px;',
            ),

            array(
                'title'    => __( 'Column - Item Name Text', 'woocommerce-jetpack' ),
                'desc_tip' => __( 'Item name text', 'woocommerce-jetpack' ),
                'id'       => 'wcj_pdf_invoices_column_item_name_text',
                'default'  => __( 'Item Name', 'woocommerce-jetpack' ),
                'type'     => 'text',
				'css'	   => 'width:33%;min-width:300px;',
            ),
			
            array(
                'title'    => __( 'Column - Qty Text', 'woocommerce-jetpack' ),
                'desc_tip' => __( 'Qty text', 'woocommerce-jetpack' ),
                'id'       => 'wcj_pdf_invoices_column_qty_text',
                'default'  => __( 'Qty', 'woocommerce-jetpack' ),
                'type'     => 'text',
				'css'	   => 'width:33%;min-width:300px;',
            ),
			
			array(
                'title'    => __( 'Column - Sum (TAX excl.) Text', 'woocommerce-jetpack' ),
                'desc_tip' => __( 'Price text', 'woocommerce-jetpack' ),
                'id'       => 'wcj_pdf_invoices_column_price_tax_excl_text',
                'default'  => __( 'Price (TAX excl.)', 'woocommerce-jetpack' ),
                'type'     => 'text',
				'css'	   => 'width:33%;min-width:300px;',
            ),			
			
			array(
                'title'    => __( 'Column - Sum (TAX incl.) Text', 'woocommerce-jetpack' ),
                'desc_tip' => __( 'Price text', 'woocommerce-jetpack' ),
                'id'       => 'wcj_pdf_invoices_column_price_text',
                'default'  => __( 'Price (TAX incl.)', 'woocommerce-jetpack' ),
                'type'     => 'text',
				'css'	   => 'width:33%;min-width:300px;',
            ),	

			array(
                'title'    => __( 'Order Subtotal Text', 'woocommerce-jetpack' ),
                'desc_tip' => __( 'Order Subtotal = Total - Taxes - Shipping - Discounts', 'woocommerce-jetpack' ),
                'id'       => 'wcj_pdf_invoices_order_subtotal_text',
                'default'  => __( 'Order Subtotal', 'woocommerce-jetpack' ),
                'type'     => 'text',
				'css'	   => 'width:33%;min-width:300px;',
            ),
			
			array(
                'title'    => __( 'Order Shipping Text', 'woocommerce-jetpack' ),
                'desc_tip' => __( 'Order Shipping text', 'woocommerce-jetpack' ),
                'id'       => 'wcj_pdf_invoices_order_shipping_text',
                'default'  => __( 'Shipping', 'woocommerce-jetpack' ),
                'type'     => 'text',
				'css'	   => 'width:33%;min-width:300px;',
            ),			
			
			array(
                'title'    => __( 'Total Discount Text', 'woocommerce-jetpack' ),
                'desc_tip' => __( 'Total Discount text', 'woocommerce-jetpack' ),
                'id'       => 'wcj_pdf_invoices_order_discount_text',
                'default'  => __( 'Discount', 'woocommerce-jetpack' ),
                'type'     => 'text',
				'css'	   => 'width:33%;min-width:300px;',
            ),				
			
			array(
                'title'    => __( 'Order Total Text', 'woocommerce-jetpack' ),
                'desc_tip' => __( 'Order Total text', 'woocommerce-jetpack' ),
                'id'       => 'wcj_pdf_invoices_order_total_text',
                'default'  => __( 'Order Total', 'woocommerce-jetpack' ),
                'type'     => 'text',
				'css'	   => 'width:33%;min-width:300px;',
            ),
			
            array(
                'title'    => __( 'PDF Invoices for Customers', 'woocommerce-jetpack' ),
                'desc'     => __( 'Enable the PDF Invoices in customers account', 'woocommerce-jetpack' ),
				'desc_tip'	=> apply_filters( 'get_wc_jetpack_plus_message', '', 'desc' ),
                'id'       => 'wcj_pdf_invoices_enabled_for_customers',
                'default'  => 'no',
                'type'     => 'checkbox',
				'custom_attributes'	=> apply_filters( 'get_wc_jetpack_plus_message', '', 'disabled' ),
            ),

            array( 'type'  => 'sectionend', 'id' => 'wcj_pdf_invoices_options' ),
        );
        
        return $settings;
    }
 
    /**
     * settings_section.
     */
    function settings_section( $sections ) {
    
        $sections['pdf_invoices'] = __( 'PDF Invoices', 'woocommerce-jetpack' );
        
        return $sections;
    }    
}
 
endif;
 
return new WCJ_PDF_Invoices();

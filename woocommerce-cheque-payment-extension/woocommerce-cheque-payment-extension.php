<?php
/**
 * Plugin Name: WooCommerce Check Payment Extension
 * Description: Woocommerce payment option as check with check number and payer name
 * Plugin URI: http://tier5.us
 * Version: 1.0.0
 * Author: Jon Vaughn
 * Author URI: http://tier5.us
 * License: GPLv2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
add_action('plugins_loaded', 'init_woocommerce_cheque_extension', 0);

function init_woocommerce_cheque_extension(){
    
    class WC_Gateway_ChequeExtended extends WC_Payment_Gateway {

        /**
         * Constructor for the gateway.
         */
        public function __construct() {
            $this->id                 = 'cheque-extended';
            $this->icon               = apply_filters('woocommerce_cheque_icon', '');
            $this->has_fields         = true;
            $this->method_title       = __( 'Check Extended', 'woocommerce' );
            $this->method_description = __( 'Allows check payments with check number & payer name.', 'woocommerce' );

            // Load the settings.
            $this->init_form_fields();
            $this->init_settings();

            // Define user set variables
            $this->title        = $this->get_option( 'title' );
            $this->description  = $this->get_option( 'description' );
            $this->instructions = $this->get_option( 'instructions', $this->description );

            // Actions
            add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
            add_action( 'woocommerce_thankyou_cheque', array( $this, 'thankyou_page' ) );

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
                        'label'   => __( 'Enable Check Payment', 'woocommerce' ),
                        'default' => 'yes'
                ),
                'title' => array(
                        'title'       => __( 'Title', 'woocommerce' ),
                        'type'        => 'text',
                        'description' => __( 'This controls the title which the user sees during checkout.', 'woocommerce' ),
                        'default'     => __( 'Check Payment', 'woocommerce' ),
                        'desc_tip'    => true,
                ),
//                'description' => array(
//                        'title'       => __( 'Description', 'woocommerce' ),
//                        'type'        => 'textarea',
//                        'description' => __( 'Payment method description that the customer will see on your checkout.', 'woocommerce' ),
//                        'default'     => __( 'Please send your cheque to Store Name, Store Street, Store Town, Store State / County, Store Postcode.', 'woocommerce' ),
//                        'desc_tip'    => true,
//                ),
//                'instructions' => array(
//                        'title'       => __( 'Instructions', 'woocommerce' ),
//                        'type'        => 'textarea',
//                        'description' => __( 'Instructions that will be added to the thank you page and emails.', 'woocommerce' ),
//                        'default'     => '',
//                        'desc_tip'    => true,
//                ),
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
            if ( $this->instructions && ! $sent_to_admin && 'cheque-extended' === $order->payment_method && $order->has_status( 'on-hold' ) ) {
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

            $order = wc_get_order( $order_id );

            // Mark as on-hold (we're awaiting the cheque)
            $order->update_status( 'on-hold', __( 'Awaiting check payment', 'woocommerce' ) );

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

        function payment_fields() {
            echo  woocommerce_form_field( 'cheque_number', array(
                'type'          => 'text',
                'class'         => array('cheque-number form-row-wide'),
                'label'         => 'Check Number',
                'required'      => TRUE,
                'placeholder'   => 'Enter check number',
                ), '');
            echo  woocommerce_form_field( 'payee_name', array(
                'type'          => 'text',
                'class'         => array('payee-name form-row-wide'),
                'label'         => 'Payer Name',
                'required'      => TRUE,
                'placeholder'   => 'Enter payer name',
                ), '');
        }

        function validate_fields(){
            if ($_POST['cheque_number'] == '' && $_POST['payment_method']=='cheque-extended'){
                wc_add_notice('Check number is required.', 'error');
            }
            if (!is_numeric($_POST['cheque_number']) && $_POST['payment_method']=='cheque-extended'){
                wc_add_notice('Check number should be a number.', 'error');
            }
            if ($_POST['payee_name'] == '' && $_POST['payment_method']=='cheque-extended'){
                wc_add_notice('Payer Name is required.', 'error');
            }
        }
    }

    add_action('woocommerce_email_after_order_table','display_cheque_details');
    add_action('woocommerce_order_details_after_order_table','display_cheque_details');
    
    function display_cheque_details($order){
        $_payment_method = get_post_meta($order->id, '_payment_method', true);
        if($_payment_method == 'cheque-extended'){
            $_cheque_number = get_post_meta($order->id, '_cheque_number', true);
            $_payee_name = get_post_meta($order->id, '_payee_name', true);
            echo '<h3>Check Number</h3>';
            echo "$_cheque_number";
            echo '<h3>Payer Name</h3>';
            echo "$_payee_name";
        }
    }

    add_action('woocommerce_checkout_update_order_meta', 'cheque_checkout_field_update_order_meta');

    function cheque_checkout_field_update_order_meta( $order_id ) {
        if ($_POST['cheque_number']) update_post_meta( $order_id, '_cheque_number', esc_attr($_POST['cheque_number']));
        if ($_POST['payee_name']) update_post_meta( $order_id, '_payee_name',  $_POST['payee_name']);
    }

    add_action( 'add_meta_boxes', 'cheque_add_order_metaboxes' );
    
    function cheque_add_order_metaboxes(){
        global $post;
        if(get_post_meta($post->ID, '_payment_method', true) == 'cheque-extended')
            add_meta_box('cheque_info', 'Check Details', 'cheque_box', 'shop_order', 'side', 'default');
    }
    
    function cheque_box(){
        global $post;
        if(!is_admin()) return;
        $_cheque_number = get_post_meta($post->ID, '_cheque_number', true);
        $_payee_name = get_post_meta($post->ID, '_payee_name', true);
        echo '<h3>Check Number</h3>';
        echo "$_cheque_number";
        echo '<h3>Payer Name</h3>';
        echo "$_payee_name";
    }
    
    add_filter('woocommerce_payment_gateways', 'add_cheque_extended_gateway' );
    
    function add_cheque_extended_gateway( $methods ) {
        $methods[] = 'WC_Gateway_ChequeExtended';
        return $methods;
    }
    
}
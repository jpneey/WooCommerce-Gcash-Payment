<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

// Make sure WooCommerce is active
if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
    return;
}

function jp_manual_gcash_gateway_class( $methods ) {
    $methods[] = 'WC_Gateway_JP_Manual_Gcash';
    return $methods;
}

add_filter( 'woocommerce_payment_gateways', 'jp_manual_gcash_gateway_class' );

add_action( 'plugins_loaded', function(){
    class WC_Gateway_JP_Manual_Gcash extends WC_Gateway_COD {
        
        protected function setup_properties() {
            $this->id                 = 'jp_gcash_manual';
            $this->icon               = apply_filters( 'woocommerce_jp_manual_gcash_quote_icon', '' );
            $this->method_title       = __( 'Pay via GCASH', 'jp_manual_gcash' );
            $this->method_description = __( 'Have your customers pay via gcash (You need to manually verify payments).', 'jp_manual_gcash' );
            $this->has_fields         = false;
        }
    
        public function init_form_fields() {
            $shipping_methods = array();
    
            foreach ( WC()->shipping()->load_shipping_methods() as $method ) {
                $shipping_methods[ $method->id ] = $method->get_method_title();
            }
    
            $this->form_fields = array(
                'enabled' => array(
                    'title'       => __( 'Enable/Disable', 'jp_manual_gcash' ),
                    'label'       => __( 'Gcash payment', 'jp_manual_gcash' ),
                    'type'        => 'checkbox',
                    'description' => '',
                    'default'     => 'no',
                ),
                'title' => array(
                    'title'       => __( 'Title', 'jp_manual_gcash' ),
                    'type'        => 'text',
                    'description' => __( 'Payment method description that the customer will see on your checkout.', 'jp_manual_gcash' ),
                    'default'     => __( 'Gcash Payment', 'jp_manual_gcash' ),
                    'desc_tip'    => true,
                ),
                'description' => array(
                    'title'       => __( 'Description', 'jp_manual_gcash' ),
                    'type'        => 'textarea',
                    'description' => __( 'Payment method description that the customer will see on your website.', 'jp_manual_gcash' ),
                    'default'     => __( 'Pay via Gcash.', 'jp_manual_gcash' ),
                    'desc_tip'    => true,
                ),
                'instructions' => array(
                    'title'       => __( 'Instructions', 'jp_manual_gcash' ),
                    'type'        => 'textarea',
                    'description' => __( 'Instructions that will be added to the thank you page and order email.', 'jp_manual_gcash' ),
                    'default'     => __( 'You will be notified as soon as we verify that the payment pushed through.', 'jp_manual_gcash' ),
                    'desc_tip'    => true,
                ),
                'checkout_instructions' => array(
                    'title'       => __( 'Checkout Instructions', 'jp_manual_gcash' ),
                    'type'        => 'textarea',
                    'description' => __( 'Instructions that will be added on checkout. Use <code>{{order_total}}</code> to display the order total.', 'jp_manual_gcash' ),
                    'default'     => __( 'Scan the QR Code, Pay {{order_total}} and copy paste the reference ID below.', 'jp_manual_gcash' ),
                    'desc_tip'    => false,
                ),
                'gcash_qr' => array(
                    'title'       => __( 'Gcash QR code image link', 'jp_manual_gcash' ),
                    'type'        => 'url',
                    'description' => __( 'Link to your Gcash QR Code image. <a href="https://help.gcash.com/hc/en-us/articles/15725514628121-Generate-your-Digital-QR-via-GCashPro-Portal" target="_blank">How to get my QR code</a>.', 'jp_manual_gcash' ),
                    'default'     => __( '', 'jp_manual_gcash' ),
                    'required'    => true
                ),
                'enable_for_methods' => array(
                    'title'             => __( 'Enable for shipping methods', 'jp_manual_gcash' ),
                    'type'              => 'multiselect',
                    'class'             => 'wc-enhanced-select',
                    'css'               => 'width: 400px;',
                    'default'           => '',
                    'description'       => __( 'If GCash payment is only available for certain methods, set it up here. Leave blank to enable for all methods.', 'jp_manual_gcash' ),
                    'options'           => $shipping_methods,
                    'desc_tip'          => true,
                    'custom_attributes' => array(
                        'data-placeholder' => __( 'Select shipping methods', 'jp_manual_gcash' ),
                    ),
                ),
                'enable_for_virtual' => array(
                    'title'             => __( 'Accept for virtual orders', 'jp_manual_gcash' ),
                    'label'             => __( 'Accept coupon if the order is virtual', 'jp_manual_gcash' ),
                    'type'              => 'checkbox',
                    'default'           => 'yes',
                ),
           );
        }
        
        public function process_payment( $order_id ) {
            $order = wc_get_order( $order_id );
    
            if ( $order->get_total() > 0 ) {
                $order->update_status( 'wc-gcash-pending' );
            } else {
                $order->payment_complete();
            }
    
            WC()->cart->empty_cart();
    
            return array(
                'result'   => 'success',
                'redirect' => $this->get_return_url( $order ),
            );
        }
    
    }
}, 11 );
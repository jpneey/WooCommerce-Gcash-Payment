<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class JP_Manual_Gcash_Ref {

    public function __construct()
    {
        add_action( 'woocommerce_gateway_description', [ $this, 'inline_styles' ], 19, 2 );
        add_filter( 'woocommerce_gateway_description', [ $this, 'ref_field' ], 20, 2 );
        add_action( 'woocommerce_checkout_process', [ $this, 'validate_ref' ]);
        add_action( 'woocommerce_checkout_update_order_meta', [ $this, 'save_ref' ] );

    }

    public function get_payment_gateway_custom_setting($gateway_id, $setting_key) {
        $payment_gateways = WC()->payment_gateways->get_available_payment_gateways();
    
        if (isset($payment_gateways[$gateway_id])) {
            $gateway = $payment_gateways[$gateway_id];
    
            if (isset($gateway->settings[$setting_key])) {
                return $gateway->settings[$setting_key];
            }
        }
    
        return null;
    }

    public function ref_field( $description, $payment_id )
    {
        
        if ( 'jp_gcash_manual' !== $payment_id ) {
            return $description;
        }
    
        $qr = $this->get_payment_gateway_custom_setting( 'jp_gcash_manual', 'gcash_qr' );
        $ins = $this->get_payment_gateway_custom_setting( 'jp_gcash_manual', 'checkout_instructions' );

        $ins = str_replace( '{{order_total}}', WC()->cart->get_total(), $ins );

        ob_start();
        ?>
        <p class="jp-manual-gcash-checkout-instruction"><?php echo $ins ?></p>
        <?php if ( $qr ) { ?>
        <div class="jp-manual-gcash-qr-wrapper">
            <img src="<?php echo $qr ?>" alt="merchant gcash qr code" class="jp-manual-gcash-qr" />
        </div>
        <?php
            } else if ( current_user_can( 'administrator' ) ) {
                echo sprintf(
                    '<div style="background: #ddd; padding: 15px; margin: 15px 0;">%s</div>',
                    __( 'Admin notice: You do not have a qr code setup. Please supply your qr code on the gateway settings to allow users to pay via gcash easily', 'jp_manual_gcash' )
                );
            }
        ?>
        <div class="jp-manual-gcash-fields">
            <?php
                woocommerce_form_field( 'jp_gcash_manual_ref', [
                    'type'          => 'text',
                    'default'       => '',
                    'placeholder'   => 'Enter your reference ID',
                    'label'         => 'Payment Reference Number',
                    'required'      => true,
                    'class'         => 'jp-manual-gcash-field'
                ]);
                woocommerce_form_field( 'jp_gcash_manual_ref_check', [
                    'type'          => 'checkbox',
                    'default'       => '',
                    'label'         => 'I acknowledge that my order will not be processed until a reference ID has been provided and providing false / incorrect reference ID could lead to delay on processing my order.',
                    'required'      => true,
                    'class'         => 'jp-manual-gcash-field'

                ]);
            ?>
        </div>
        <?php

        $fields = ob_get_clean();
        return $description . $fields;
    }

    public function inline_styles( $description, $payment_id )
    {
        if ( 'jp_gcash_manual' !== $payment_id ) {
            return $description;
        }
        ob_start();
        ?>
        <style>
            .payment_box.payment_method_jp_gcash_manual {
                padding: 18px !important;
            }
            .jp-manual-gcash-qr {
                float: none !important;
                width: 100% !important;
                padding: 18px !important;
                height: auto !important;
                max-width: 300px !important;
                max-height: none !important;
                margin: 0 auto !important;
                display: block !important; 
                background: #fff;
            }
            .jp-manual-gcash-fields .form-row {
                margin-bottom: 15px !important;
            }
            .jp-manual-gcash-qr-wrapper {
                display: block;
                margin-left: -18px;
                margin-right: -18px;
                margin-top: 18px;
                margin-bottom: 18px;
                padding: 30px 18px;
                background: #007DFE !important;
            }
        </style>
        <?php
        return $description . ob_get_clean();
    }

    public function validate_ref()
    {
        $payment = $_POST['payment_method'] ?? false;
        if ( $payment ) {
            $ref = $_POST['jp_gcash_manual_ref'] ?? false;   
            if ( ! $ref ) {
                wc_add_notice(__('Please supply your payment reference.') , 'error');
            }
            $check = $_POST['jp_gcash_manual_ref_check'] ?? false;
            if ( ! $check ) {
                wc_add_notice(__('Please agree to the gcash payment and terms acknowledgement.') , 'error');
            }
        }
    }

    public function save_ref( $order_id )
    {
        $payment = $_POST['payment_method'] ?? false;
        if ( $payment ) {
            $ref = $_POST['jp_gcash_manual_ref'] ?? false;   
            if ( $ref ) {
                
                update_post_meta( $order_id, "jp_gcash_manual_ref", $ref );

                $order = new WC_Order( $order_id );

                $order->add_order_note( "Gcash reference id: " . $ref. ". Please verify payment manually and update the order status accordingly." );
            }
        }
    }

}

new JP_Manual_Gcash_Ref();

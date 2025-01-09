<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class JP_Manual_Gcash_Upload {

    public function __construct()
    {
        add_action( 'admin_enqueue_scripts', [ $this, 'inline_scripts' ], 11 );
    }

    public function inline_scripts()
    {
        $screen = get_current_screen();

        $section = sanitize_text_field( wp_unslash( $_REQUEST['section'] ?? null ));

        if ( $screen->id === 'woocommerce_page_wc-settings' && $section === 'jp_gcash_manual' ) {
            wp_enqueue_media();
            wp_enqueue_style( 'gcash_payment_gateway_for_woocommerce_css', JP_MANUAL_GCASH_URL . 'assets/admin.min.css', [], JP_MANUAL_GCASH_VER );
            wp_enqueue_script( 'gcash_payment_gateway_for_woocommerce_js', JP_MANUAL_GCASH_URL . 'assets/admin.min.js', [ 'jquery' ], JP_MANUAL_GCASH_VER, true );
            wp_localize_script( 'gcash_payment_gateway_for_woocommerce_js', 'gcash_payment_gateway_for_woocommerce_js',
                array( 
                    'post_url'   => admin_url( 'post.php' ),
                )
            );
        }
    }

}

new JP_Manual_Gcash_Upload();
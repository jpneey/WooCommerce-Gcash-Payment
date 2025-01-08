<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class JP_Manual_Gcash_statuses {

    public function __construct()
    {
        add_action( 'init', [ $this, 'register_status' ] );
        add_filter( 'wc_order_statuses', [$this, 'add_status'] );
        add_filter( 'woocommerce_valid_order_statuses_for_payment', [$this, 'valid_payment_status'], 10, 2 );
    }

    public function register_status()
    {
        register_post_status(
            'wc-gcash-pending',
            array(
                'label'                     => 'Pending Gcash Payment',
                'public'                    => true,
                'show_in_admin_status_list' => true,
                'show_in_admin_all_list'    => true,
                'exclude_from_search'       => false,
                'label_count'               => _n_noop( 'Pending Gcash Ref validation (%s)', 'Pending Gcash Ref validation (%s)' )
            )
        );
    }

    public function add_status( $order_statuses )
    {
        $new_order_statuses = array();
        foreach ( $order_statuses as $key => $status ) {
            $new_order_statuses[ $key ] = $status;
            if ('wc-pending' === $key) {
                $new_order_statuses['wc-gcash-pending'] = 'Pending Gcash Payment';
            }
        }
        return $new_order_statuses;
    }

    public function valid_payment_status( $arr, $instance )
    {
        return array_merge( $arr, [ 'wc-gcash-pending' ] );
    }

}

new JP_Manual_Gcash_statuses();
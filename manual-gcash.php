<?php
/**
 * Plugin Name:       Gcash Payment Gateway for WooCommerce
 * Description:       Offline payment that allows you to accept Gcash payments by scanning your QR Code.
 * Version:           1.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            John Paul Burato
 * Author URI:        https://jpburato.vercel.app/
 * Text Domain:       gcash-payment-gateway-for-woocommerce
 * License:           GPL v2 or later
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Requires Plugins:  woocommerce
 */

define( 'JP_MANUAL_GCASH_VER', '1.0' );
define( 'JP_MANUAL_GCASH_DIR', plugin_dir_path(__FILE__) );
define( 'JP_MANUAL_GCASH_URL', plugin_dir_url(__FILE__) );

include plugin_dir_path(__FILE__) . '/includes/payment.php';
include plugin_dir_path(__FILE__) . '/includes/order-status.php';
include plugin_dir_path(__FILE__) . '/includes/reference.php';
include plugin_dir_path(__FILE__) . '/includes/upload.php';
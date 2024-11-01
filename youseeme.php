<?php
/**
 * Plugin Name: YouSeeMe Payment
 * Plugin URI: https://wordpress.org/plugins/youseeme/
 * Description: Accept cryptocurrencies as payment method.
 * Author: YouSeeMe
 * Author URI: https://youseeme.io/
 * Version: 1.2.1
 * Requires at least: 6.0
 * Tested up to: 6.5
 * WC requires at least: 7.9.0
 * WC tested up to: 7.9.0
 * Text Domain: youseeme
 * Domain Path: /languages
 * License: GPLv2 or later
 * Requires Plugins: woocommerce
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 *
 * @package Youseeme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Required minimums and constants
 */
define( 'YOUSEEME_VERSION', '1.2.0' ); // WRCS: DEFINED_VERSION.
define( 'YOUSEEME_MIN_PHP_VER', '7.3.0' );
define( 'YOUSEEME_MIN_WC_VER', '7.9.0' );
define( 'YOUSEEME_FUTURE_MIN_WC_VER', '7.9.0' );
define( 'YOUSEEME_MAIN_FILE', __FILE__ );
define( 'YOUSEEME_ABSPATH', __DIR__ . '/' );
define( 'YOUSEEME_COMMISSION', '5' );
define( 'YOUSEEME_PLUGIN_URL', untrailingslashit( plugins_url( basename( plugin_dir_path( __FILE__ ) ), basename( __FILE__ ) ) ) );
define( 'YOUSEEME_PLUGIN_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) ) );




add_action( 'plugins_loaded', 'youseeme_init' );
/**
 * Plugin initialization
 *
 * @since 1.1.2
 */
function youseeme_init(): void {
	static $plugin;

	

	if ( ! isset( $plugin )) {
		require_once __DIR__ . '/includes/class-youseeme.php';

 		$plugin = Youseeme::get_instance();
 	}
}

 
static $blocks;

if ( version_compare( WC_VERSION, YOUSEEME_MIN_WC_VER, '<' ) ) {
	add_action( 'admin_notices', 'youseeme_wc_not_supported' );
	return;
}

if ( !isset($blocks) ) {
	require_once __DIR__ . '/includes/class-youseeme-blocks-bootstrap.php';

	
	$blocks =  Youseeme_Blocks_Bootstrap::get_instance();
}



/**
 * WooCommerce fallback notice.
 *
 * @since 1.0.0
 */
function youseeme_missing_wc_notice(): void {
	/* translators: 1. URL link. */
	echo '<div class="error"><p><strong>' . sprintf( esc_html__( 'Youseeme requires WooCommerce to be installed and active. You can download %s here.', 'youseeeme' ), '<a href="https://woocommerce.com/" target="_blank">WooCommerce</a>' ) . '</strong></p></div>';
}

/**
 * WooCommerce not supported fallback notice.
 *
 * @since 1.1.0
 */
function youseeme_wc_not_supported(): void {
	/* translators: $1. Minimum WooCommerce version. $2. Current WooCommerce version. */
	echo '<div class="error"><p><strong>' . sprintf( esc_html__( 'Youseeme requires WooCommerce %1$s or greater to be installed and active. WooCommerce %2$s is no longer supported.', 'youseeeme' ), esc_html( YOUSEEME_MIN_WC_VER ), esc_html( WC_VERSION ) ) . '</strong></p></div>';
}

<?php
/**
 * Class Youseeme file.
 *
 * @package Youseeme
 */

/**
 * Class Youseeme.
 */
class Youseeme extends WC_Settings_API {


	/**
	 * The *Singleton* instance of this class
	 *
	 * @var Youseeme
	 */
	private static $instance;

	/**
	 * Returns the *Singleton* instance of this class.
	 *
	 * @return Youseeme The *Singleton* instance.
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor for the gateway.
	 */
	public function __construct() {
		include_once __DIR__ . '/class-ysme-gateway.php';
		include_once __DIR__ . '/class-ysme-gateway-iban.php';

		add_action( 'init', array( $this, 'register_youseeme_pending_order_status' ) );
		add_action( 'add_meta_boxes_shop_order', array( $this, 'add_crypto_values_metabox' ) );
		add_filter( 'woocommerce_payment_gateways', array( $this, 'add_gateways' ) );
		add_filter( 'wc_order_statuses', array( $this, 'add_pending_crypto_status' ) );

		$cryptos = get_option(
			'youseeme_cryptocurrencies',
			array(
				array(
					'crypto_name'    => $this->get_option( 'crypto_name' ),
					'crypto_address' => $this->get_option( 'crypto_address' ),
				),
			)
		);

		$ibans = get_option(
			'youseeme_ibans',
			array(
				array(
					'bank_name' => $this->get_option( 'bank_name' ),
					'iban'      => $this->get_option( 'iban' ),
					'bic'       => $this->get_option( 'bic' ),
				),
			)
		);

		if ( count( $ibans ) > 0 ) {
			add_filter( 'woocommerce_payment_gateways', array( $this, 'remove_BACS_gateway' ) );
		}
	}

	/**
	 * Add Youseeme metabox to shop order.
	 */
	public function add_crypto_values_metabox(): void {
		add_meta_box(
			'crypto_values',
			'YOUSEEME Payment',
			array( $this, 'crypto_values_metabox_html' ),
			'shop_order',
			'side',
			'high'
		);
	}

	/**
	 * Youseeme metabox html content.
	 *
	 * @param WP_Post $post The post object.
	 */
	public function crypto_values_metabox_html( $post ): void {
		$youseeme_rate       = get_post_meta( $post->ID, 'youseeme_rate', true );
		$youseeme_crypto     = get_post_meta( $post->ID, 'youseeme_crypto', true );
		$youseeme_total      = get_post_meta( $post->ID, 'youseeme_total', true );
		$youseeme_iban_bank  = get_post_meta( $post->ID, 'youseeme_iban_bank', true );
		$youseeme_iban_total = get_post_meta( $post->ID, 'youseeme_iban_total', true );
		if ( ! empty( $youseeme_rate ) ) {
			?>
			<strong>Total Crypto <?php echo esc_html( $youseeme_total ); ?> <?php echo esc_html( $youseeme_crypto ); ?> (<?php echo number_format( $youseeme_rate, 15 ); ?>)</strong>
			<p><?php echo esc_html__( 'Please note that payment is made from your customer to your direct Wallet, wait for Youseeme to send you a confirmation notice for receipt of the amount before validating the order.', 'youseeme' ); ?></p>
			<?php
		} elseif ( ! empty( $youseeme_iban_bank ) ) {
			?>
			<strong>IBAN: <?php echo esc_html( $youseeme_iban_bank ); ?> </strong>
			<br>
			<span>Total: <?php echo esc_html( $youseeme_iban_total ); ?>€</span>
			<?php
		}
	}

	/**
	 * Register the wc-youseeme-pending status.
	 */
	public function register_youseeme_pending_order_status(): void {
		register_post_status(
			'wc-youseeme-pending',
			array(
				'label'                     => __( 'Awaiting Crypto Confirmation', 'youseeme' ),
				'public'                    => true,
				'show_in_admin_status_list' => true,
				'show_in_admin_all_list'    => true,
				'exclude_from_search'       => false,
				/* translators: %s: count */
				'label_count'               => _n_noop( 'Awaiting Crypto Confirmation <span class="count">(%s)</span>', 'Awaiting Crypto Confirmation <span class="count">(%s)</span>' ),
			)
		);
	}

	/**
	 * Add the wc-youseeme-pending status to WC status list.
	 *
	 * @param array $statuses WC status list.
	 */
	public function add_pending_crypto_status( $statuses ) {
		$statuses['wc-youseeme-pending'] = __( 'Awaiting Crypto Confirmation', 'youseeme' );
		return $statuses;
	}

	/**
	 * Add the new gateways to the list of WC Gateways.
	 *
	 * @param array $methods WC gateways list.
	 */
	public function add_gateways( $methods ): array {
		$methods[] = YSME_Gateway::class;
		$methods[] = YSME_Gateway_IBAN::class;

		return $methods;
	}

	/**
	 * Remove the BACS gateway.
	 *
	 * @param array $methods WC gateways list.
	 */
	public function remove_BACS_gateway( $methods ): array {
		return array_filter(
			$methods,
			function ( $method ) {
				return 'WC_Gateway_BACS' !== $method;
			}
		);
	}
}

?>

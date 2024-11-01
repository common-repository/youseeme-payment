<?php
/**
 * Class YSME_Gateway file.
 *
 * @category File
 * @package  Youseeme\Gateways
 */

/**
 * Class YSME_Gateway.
 *
 * @category Class
 * @package  Youseeme\Gateways
 */
class YSME_Gateway extends WC_Payment_Gateway {

	const ID = 'youseeme';
	/**
	 * Selected crypto's list.
	 *
	 * @var array
	 */
	public array $cryptos;
	/**
	 * Youseeme.io api key.
	 *
	 * @var string
	 */
	protected string $api_key;
	/**
	 * Commission value in percent.
	 *
	 * @var string
	 */
	protected string $commission;
	/**
	 * Available crypto's protected list.
	 *
	 * @var array
	 */
	protected array $available_cryptos = array( 'UNI', 'FTM', 'SHIB', 'USDC', 'dogecoin', 'CARDANO', 'BINANCE USD', 'SOLANA', 'TRON', 'bitcoincash', 'EUREC', 'BTC', 'ETH', 'LTC', 'USDT', 'YOUSY', 'WBTC', 'FLOWS', 'DASH', 'DAI', '1INCH', 'BNB', 'MKR', 'SUSHI', 'MATIC', 'OMG', 'AUDIO', 'OP', 'ALICE', 'AVAX', 'UBN' );

	/**
	 * Gateway constructor
	 * *Singleton* via the `new` operator from outside of this class.
	 */
	public function __construct() {
		$this->id                 = self::ID;
		$this->method_title       = __( 'Youseeme - Cryptocurrencies', 'youseeme' );
		$this->title              = __( 'Pay using Crypto', 'youseeme' );
		$this->method_description = __( 'Accept cryptocurrencies as payment method.', 'youseeme' );
		$this->has_fields         = true;
		$this->supports           = array(
			'products',
			'refunds',
			'tokenization',
			'add_payment_method',
		);

		$this->description = __( 'Pay using your favorite Cryptocurrency', 'youseeme' );
		$this->api_key     = $this->get_option( 'api_key' );
		$this->commission  = ! empty( $this->get_option( 'commission' ) ) ? $this->get_option( 'commission' ) : YOUSEEME_COMMISSION;
		$this->init_form_fields();
		$this->init_settings();

		$this->cryptos = get_option(
			'youseeme_cryptocurrencies',
			array(
				array(
					'crypto_name'    => $this->get_option( 'crypto_name' ),
					'crypto_address' => $this->get_option( 'crypto_address' ),
				),
			)
		);

		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'save_cryptocurrencies' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_script' ) );
	}

	/**
	 * Processes and saves options.
	 * If there is an error thrown, will continue to save and validate fields, but will leave the erroring field out.
	 *
	 * @return bool was anything saved?
	 */
	public function process_admin_options() {
		$post_data = $this->get_post_data();

		if ( empty( $post_data['woocommerce_youseeme_api_key'] ) || ! $this->getCryptoRates( $post_data['woocommerce_youseeme_api_key'] ) ) {
			$this->add_error( "Clé d'api non valide, Veuillez contacter Youseeme pour obtenir une clé valide" );
			$this->display_errors();
			return false;
		}

		return parent::process_admin_options();
	}
	/**
	 * Initialise settings form fields.
	 *
	 * Add an array of fields to be displayed on the gateway's settings screen.
	 */
	public function init_form_fields() {
		// Configuration des champs de réglages pour la méthode de paiement.
		$this->form_fields = array(
			'enabled'          => array(
				'title'    => __( 'Activate/Désactivate', 'youseeme' ),
				'type'     => 'checkbox',
				'label'    => __( 'Activate payment by cryptocurrencies', 'youseeme' ),
				'default'  => 'yes',
				'required' => true,
			),
			'api_key'          => array(
				'title'       => __( 'API key <sup style="color:red;">*</sup>', 'youseeme' ),
				'type'        => 'text',
				'description' => __( 'Please sign up at <a href="https://user.youseeme.io">https://user.youseeme.io</a> for the opening of your free wallet for your cash in collection, and the API key for the rates. Or <a href="mailto:contact@youseeme.pro">contact@youseeme.pro</a>', 'youseeme' ),
				'default'     => '',
				'desc_tip'    => false,
				'required'    => true,
			),
			'commission'       => array(
				'title'       => __( 'Commission (%) <sup style="color:red;">*</sup>', 'youseeme' ),
				'type'        => 'text',
				'description' => '',
				'default'     => $this->commission,
				'desc_tip'    => false,
				'required'    => true,
			),
			'cryptocurrencies' => array(
				'type' => 'cryptocurrencies',
			),
		);
	}

	/**
	 * Enqueue js scripts to gateway admin
	 */
	public function enqueue_admin_script() {

		wp_enqueue_script('qrcode',  YOUSEEME_PLUGIN_URL . '/assets/qrcode.min.js',[],false,array( 'in_footer' => true ));
		wp_enqueue_script( 'youseeme-admin-crypto', YOUSEEME_PLUGIN_URL . '/assets/admin-crypto.js', array( 'jquery','qrcode' ), YOUSEEME_VERSION, array( 'in_footer' => true ) );
	}

	/**
	 * Generate account details html.
	 *
	 * @return string
	 */
	public function generate_cryptocurrencies_html(): string {
		ob_start();
		?>
		<tr>
			<th scope="row" class="titledesc">
		<?php esc_html_e( 'Cryptocurrencies', 'youseeme' ); ?>
			</th>
			<td class="forminp" id="youseeme_cryptos">
				<div class="wc_input_table_wrapper">
					<table class="widefat wc_input_table sortable">
						<thead>
							<tr>
								<th class="sort">&nbsp;</th>
								<th>
									<?php esc_html_e( 'Crypto', 'youseeme' ); ?>
								</th>
								<th>
									<?php esc_html_e( 'Address', 'youseeme' ); ?>
								</th>
								<th>
									<?php esc_html_e( 'QR code', 'woocommerce' ); ?>
								</th>
							</tr>
						</thead>
						<tbody class="accounts">
		<?php
			$i = -1;
		if ( $this->cryptos ) {
			foreach ( $this->cryptos as $crypto ) {
				++$i;
				echo '<tr class="cryptos">
                                    <td class="sort"></td>
                                    <td><b>' . esc_attr( $crypto['crypto_name'] ) . '</b><input id="mycryptosName_' . esc_attr( $i ) . '" type="hidden" class="crypto_name" value="' . esc_attr( $crypto['crypto_name'] ) . '"  name="crypto_name[' . esc_attr( $i ) . ']" /></td>
                                    <td><input type="text" value="' . esc_attr( $crypto['crypto_address'] ) . '" class="crypto_address" placeholder="ex: 02f5e25778dcee9539b25799831277eb8e73" name="crypto_address[' . esc_attr( $i ) . ']" id="mycryptosAddress_' . esc_attr( $i ) . '" onChange="updateQR()" /></td>
                                    <td><div id = "qr_' . esc_attr( $i ) . '" width="40" height="40"  alt="qr_code" ></div> </td>
                                </tr>';
			}
		}
		?>
						</tbody>
						<tfoot>
							<tr>
								<th colspan="7"><a href="#" class="add button">
										<?php esc_html_e( '+ Add crypto', 'youseeme' ); ?>
									</a> <a href="#" class="remove_rows button">
										<?php esc_html_e( 'Remove selected cryptos(s)', 'youseeme' ); ?>
									</a></th>
							</tr>
						</tfoot>
					</table>
				</div>
			</td>
		</tr>
		<?php
		return ob_get_clean();
	}

	/**
	 * Save account details table.
	 */
	public function save_cryptocurrencies() {

		$cryptocurrencies = array();
		$post_data        = array(
			'crypto_name'    => filter_input( INPUT_POST, 'crypto_name', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY ),
			'crypto_address' => filter_input( INPUT_POST, 'crypto_address', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY ),
		);

     // phpcs:disable WordPress.Security.NonceVerification.Missing -- Nonce verification already handled in WC_Admin_Settings::save()
		if ( ! empty( $post_data['crypto_name'] ) && ! empty( $post_data['crypto_address'] ) ) {

			$crypto_names     = wc_clean( wp_unslash( $post_data['crypto_name'] ) );
			$crypto_addresses = wc_clean( wp_unslash( $post_data['crypto_address'] ) );

			foreach ( $crypto_names as $i => $name ) {
				if ( ! isset( $name ) || ( isset( $crypto_addresses[ $i ] ) && empty( $crypto_addresses[ $i ] ) ) ) {
					continue;
				}

				$cryptocurrencies[] = array(
					'crypto_name'    => $name,
					'crypto_address' => $crypto_addresses[ $i ],
				);
			}
		}
     // phpcs:enable

		update_option( 'youseeme_cryptocurrencies', $cryptocurrencies );
	}

	/**
	 * Generate payment fields to add into payment form
	 */
	public function payment_fields() {
		$description = $this->get_description();
		$description = ! empty( $description ) ? $description : '';

		if ( empty( $this->api_key ) ) {
			return;
		}

		$vars = $this->getTemplateVarInfos();
		wp_enqueue_script('qrcode',  YOUSEEME_PLUGIN_URL . '/assets/qrcode.min.js',[],false,array( 'in_footer' => true ));
		wp_enqueue_script( 'youseeme-front-crypto', YOUSEEME_PLUGIN_URL . '/assets/front-crypto.js', array( 'jquery','qrcode' ), YOUSEEME_VERSION, array( 'in_footer' => true ) );
		wp_localize_script(
			'youseeme-front-crypto',
			'cryptosDatas',
			array(
				'cryptosJson'  => $vars['cryptosJson'],
				'labelAmount'  => __( 'Total amount to pay:', 'youseeme' ),
				'labelAddress' => __( 'Public address', 'youseeme' ),
			)
		);
		if ( count( $this->cryptos ) ) {
			echo "<div class='ysm_row'>
                        <div class='ysm_col'>
                            <p>" . esc_html( $description ) . "</p>
                            <p><select  style='padding:8px;width:250px' onchange='updatePriceAndQr()' id='cryptoSelect'>
                                <<option value='-'>" . esc_html__( 'Please select your favorite Crypto', 'youseeme' ) . '</option>';
			foreach ( $this->cryptos as $crypto ) {
				echo "<option value='" . esc_html( $crypto['crypto_name'] ) . "'>" . esc_html( $crypto['crypto_name'] ) . '</option>';
			}
			echo '</select></p>
                    <div id="crypto_price"></div>
                    <br/>
                    <p id="crypto_amount_label" style="display:none;">' . esc_html__( 'Please SCAN THE QR CODE to make your payment or COPY THE ADDRESS BELOW and paste it into your wallet to complete the payment.', 'youseeme' ) . '</p>
                    <div id="crypto_address"></div>
                    <input type="hidden" name="youseeme_total" value="">
                    <input type="hidden" name="youseeme_rate" value="">
                    <input type="hidden" name="youseeme_crypto" value="">
                </div>
                <div class="ysm_col">
                    <img alt="Youpay logo" style=";margin:1rem 0;" src="' . esc_html( YOUSEEME_PLUGIN_URL ) . '/assets/YouPay_cryptopayment.png" width="154"/>
                    <div id="crypto_QR" alt="qr_code" ></div>
                </div>
            </div>
            <style>.ysm_row{display:flex}.ysm_col{width:50%;padding:1em;display: flex; flex-direction: column;justify-content: space-between;align-items: center;}@media(max-width: 767px){.ysm_col{width:100%}}</style> ';
		}
	}

	/**
	 * Get var infos into json format.
	 */
	public function getTemplateVarInfos(): array {
		$total_amount    = WC()->cart->total;
		$response_object = $this->getCryptoRates( $this->api_key );
		$vars            = array( 'total' => $total_amount );

		if ( count( $this->cryptos ) > 0 && count( (array) $response_object ) > 0 ) {
			foreach ( $this->cryptos as $crypto ) {
				if ( 'IBAN' === $crypto['crypto_name'] ) {
					$mytotal = (float) $total_amount;
					$rate    = 1;
				} else {
					$crypto_eur = $crypto['crypto_name'] . '/EUR';
					$rate       = $response_object->$crypto_eur;
					if ( ! $rate ) {
						continue;
					}
					$mytotal = ( (float) $total_amount / (float) $rate ) * ( 1 + ( $this->commission / 100 ) );
				}

				$vars['cryptos'][] = array(
					'name'  => $crypto['crypto_name'],
					'total' => $mytotal,
					'key'   => $crypto['crypto_address'],
					'rate'  => (float) $rate,
				);
			}
			$vars['cryptosJson'] = wp_json_encode( $vars['cryptos'] );
		}

		return $vars;
	}

	/**
	 * Get var cryptocurrencies rates from youseeme.io API .
	 *
	 * @param string $api_key Youseeme api key for getting rates.
	 */
	public function getCryptoRates( string $api_key ) {
		try {
			$hashed          = hash( 'sha512', $api_key . '-' . get_site_url() );
			$response_object = wp_remote_get(
				'https://api.youseeme.io/v1/ecommerce/plugins/crypto/prices',
				array(
					'headers' => array(
						'Content-Type' => 'application/json',
						'X-Api-Key'    => $api_key,
						'serial-id '   => $hashed,
					),
				)
			);

			if ( is_wp_error( $response_object ) ) {
				return false;
			}

			if ( ! isset( $response_object['response'] ) || 200 !== $response_object['response']['code'] ) {
				return false;
			}

			return json_decode( $response_object['body'] );

		} catch ( Exception $e ) {
			wc_add_notice( $e->getMessage(), 'error' );
		}
	}
	/**
	 * Handle the payment
	 *
	 * @param  int $order_id Id of Order to be processed.
	 * @throws Exception Error indicating that a crypto name is unavailable.
	 */
	public function process_payment( $order_id ) {
		$order = wc_get_order( $order_id );

		$youseeme_data_string = $_POST['youseeme_data'];
		if ( isset( $youseeme_data_string ) && ! empty( $youseeme_data_string ) ) {
			$youseeme_data = json_decode( $youseeme_data_string );

			$payment_method  = 'youseeme';
			$youseeme_total  = filter_var( $youseeme_data->youseeme_total, FILTER_VALIDATE_FLOAT );
			$youseeme_rate   = filter_var( $youseeme_data->youseeme_rate, FILTER_VALIDATE_FLOAT );
			$youseeme_crypto = filter_var( $youseeme_data->youseeme_crypto, FILTER_SANITIZE_STRING );
		} else {
			$payment_method  = filter_input( INPUT_POST, 'payment_method', FILTER_SANITIZE_STRING );
			$youseeme_total  = filter_input( INPUT_POST, 'youseeme_total', FILTER_VALIDATE_FLOAT );
			$youseeme_rate   = filter_input( INPUT_POST, 'youseeme_rate', FILTER_VALIDATE_FLOAT );
			$youseeme_crypto = filter_input( INPUT_POST, 'youseeme_crypto', FILTER_SANITIZE_STRING );
		}

		try {
			if ( 'youseeme' === $payment_method
				&& ! empty( $youseeme_total )
				&& ! empty( $youseeme_rate )
				&& ! empty( $youseeme_crypto )
			) {

				$order->update_status( 'wc-youseeme-pending' );
				$order->update_meta_data( 'youseeme_total', floatval( $youseeme_total ) );
				$order->update_meta_data( 'youseeme_rate', floatval( $youseeme_rate ) );

				if ( ! in_array( $youseeme_crypto, $this->available_cryptos, true ) ) {
					throw new Exception( 'Unavailable crypto name' );
				}
				$order->update_meta_data( 'youseeme_crypto', wc_clean( wp_unslash( $youseeme_crypto ) ) );
				$order->save();
				WC()->cart->empty_cart();

				return array(
					'result'   => 'success',
					'redirect' => $this->get_return_url( $order ),
				);
			}
		} catch ( Exception $e ) {
			wc_add_notice( $e->getMessage(), 'error' );

			$statuses = array( 'pending', 'failed' );

			if ( $order->has_status( $statuses ) ) {
				$this->send_failed_order_email( $order_id );
			}

			return array(
				'result'   => 'fail',
				'redirect' => '',
			);
		}
	}

	/**
	 * Sends the failed order email to admin.
	 *
	 * @param   int $order_id Order object id.
	 * @since   1.0.0
	 * @version 1.0.0
	 */
	public function send_failed_order_email( int $order_id ): void {
		$emails = WC()->mailer()->get_emails();
		if ( ! empty( $emails ) && ! empty( $order_id ) ) {
			$emails['WC_Email_Failed_Order']->trigger( $order_id );
		}
	}


	/**
	 * Returns all supported currencies for this payment method.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 * @return  array
	 */
	public function get_supported_currency(): array {
		return apply_filters(
			'youseeme_supported_currencies',
			array(
				'EUR',
			)
		);
	}

	/**
	 * Checks to see if all criteria is met before showing payment method.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 * @return  bool
	 */
	public function is_available(): bool {

		if ( ! in_array( get_woocommerce_currency(), $this->get_supported_currency(), true ) ) {
			return false;
		}

		return parent::is_available();
	}
}

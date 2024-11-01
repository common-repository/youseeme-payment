<?php
/**
 * Class YSME_Gateway_IBAN file.
 *
 * @category File
 * @package  Youseeme\Gateways
 */

/**
 * Class YSME_Gateway_IBAN.
 *
 * @category Class
 * @package  Youseeme\Gateways
 */
class YSME_Gateway_IBAN extends WC_Payment_Gateway {

	const ID = 'youseeme-iban';
	/**
	 * IBAN's list.
	 *
	 * @var array
	 */
	public array $ibans;

	/**
	 * Gateway constructor
	 * *Singleton* via the `new` operator from outside of this class.
	 */
	public function __construct() {
		$this->id                 = self::ID;
		$this->method_title       = __( 'Youseeme - IBAN', 'youseeme' );
		$this->title              = __( 'Pay using IBAN', 'youseeme' );
		$this->method_description = __( 'Accept IBAN as payment method.</br>Please subscribe to a youseeme IBAN account on <a href="https://www.youseeme.fr">https://www.youseeme.fr</a> dedicated to your online sales, it contains a Mastercard, so you will receive an essential notification of payment for each sale before validating the order, because remember there are no payment intermediaries.', 'youseeme' );
		$this->has_fields         = true;
		$this->supports           = array(
			'products',
			'refunds',
			'tokenization',
			'add_payment_method',
		);

		$this->description = __( 'Pay using bank transfer', 'youseeme' );
		$this->init_form_fields();
		$this->init_settings();

		$this->ibans = get_option(
			'youseeme_ibans',
			array(
				array(
					'bank_name' => $this->get_option( 'bank_name' ),
					'iban'      => $this->get_option( 'iban' ),
					'bic'       => $this->get_option( 'bic' ),
				),
			)
		);

		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'save_ibans' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_script' ) );
	}
	/**
	 * Iznitialise Gateway Settings Form Fields.
	 *
	 * @return void
	 */
	public function init_form_fields(): void {
		$this->form_fields = array(
			'enabled' => array(
				'title'    => esc_html__( 'Activate/Désactivate', 'youseeme' ),
				'type'     => 'checkbox',
				'label'    => esc_html__( 'Activate payment by iban', 'youseeme' ),
				'default'  => 'yes',
				'required' => true,
			),
			'ibans'   => array(
				'type' => 'ibans',
			),
		);
	}
	/**
	 * Enqueue js scripts to gateway admin
	 *
	 * @return void
	 */
	public function enqueue_admin_script(): void {
		wp_enqueue_script('qrcode',  YOUSEEME_PLUGIN_URL . '/assets/qrcode.min.js',[],false,array( 'in_footer' => true ));
		wp_enqueue_script( 'youseeme-admin-ibans', YOUSEEME_PLUGIN_URL . '/assets/admin-iban.js', array( 'jquery','qrcode' ), YOUSEEME_VERSION, array( 'in_footer' => true ) );
	}

	/**
	 * Generate account details html.
	 *
	 * @return string
	 */
	public function generate_ibans_html(): string {
		ob_start();
		?>
		<tr>
			<th scope="row" class="titledesc">
		<?php esc_html_e( 'Ibans', 'youseeme' ); ?>
			</th>
			<td class="forminp" id="youseeme_ibans">
				<div class="wc_input_table_wrapper">
					<table class="widefat wc_input_table sortable">
						<thead>
							<tr>
								<th class="sort">&nbsp;</th>
								<th>
									<?php esc_html_e( 'Bank name', 'youseeme' ); ?>
								</th>
								<th>
									<?php esc_html_e( 'IBAN', 'youseeme' ); ?>
								</th>
								<th>
									<?php esc_html_e( 'BIC / SWIFT', 'youseeme' ); ?>
								</th>
								<th>
									<?php esc_html_e( 'QR code', 'youseeme' ); ?>
								</th>
							</tr>
						</thead>
						<tbody class="accounts">
		<?php
			$i = -1;
		if ( $this->ibans ) {
			foreach ( $this->ibans as $iban ) {
				++$i;
				if ( ! empty( $iban['iban'] ) ) {
					echo '<tr class="ibans">
                                    <td class="sort"></td>
                                    <td><input id="myibansName_' . esc_attr( $i ) . '" type="text" ' . ( 'Youseeme' === $iban['bank_name'] ? 'readonly' : '' ) . ' class="bank_name" value="' . esc_attr( $iban['bank_name'] ) . '"  name="bank_name[' . esc_attr( $i ) . ']" onChange="updateIbanQR()" /></td>
                                    <td><input type="text" value="' . esc_attr( $iban['iban'] ) . '" class="iban" placeholder="IBAN" name="iban[' . esc_attr( $i ) . ']" id="myibansAddress_' . esc_attr( $i ) . '" onChange="updateIbanQR()" /></td>
                                    <td><input type="text" value="' . esc_attr( $iban['bic'] ) . '" class="bic" placeholder="BIC" name="bic[' . esc_attr( $i ) . ']" id="myibansAddress_BIC_' . esc_attr( $i ) . '" onChange="updateIbanQR()" /></td>
                                    <td> <div id = "qr_' . esc_attr( $i ) . '" style="padding:5px" src="" alt="qr_code" ></div></td>
                                </tr>';
				}
			}
		}
		?>
						</tbody>
						<tfoot>
							<tr>
								<th colspan="7"><a href="#" class="add button">
										<?php esc_html_e( '+ Add iban', 'youseeme' ); ?>
									</a><a href="#" class="add_youseeme button">
										<?php esc_html_e( '+ Add Youseeme IBAN', 'youseeme' ); ?>
									</a> <a href="#" class="remove_rows button">
										<?php esc_html_e( 'Remove selected ibans(s)', 'youseeme' ); ?>
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
	 *
	 * @return void
	 */
	public function save_ibans(): void {

		$post_data = array(
			'bank_name' => filter_input( INPUT_POST, 'bank_name', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY ),
			'iban'      => filter_input( INPUT_POST, 'iban', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY ),
			'bic'       => filter_input( INPUT_POST, 'bic', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY ),
		);
		$ibans_obj = array();

     // phpcs:disable WordPress.Security.NonceVerification.Missing -- Nonce verification already handled in WC_Admin_Settings::save()
		if ( isset( $post_data['bank_name'] ) && isset( $post_data['iban'] ) ) {

			foreach ( $post_data['bank_name'] as $i => $name ) {
				if ( ! isset( $name ) || ( isset( $post_data['iban'][ $i ] ) && empty( $post_data['iban'][ $i ] ) ) ) {
					continue;
				}

				$ibans_obj[] = array(
					'bank_name' => $name,
					'iban'      => $post_data['iban'][ $i ],
					'bic'       => $post_data['bic'] ? $post_data['bic'][ $i ] : '',
				);
			}
		}
     // phpcs:enable

		update_option( 'youseeme_ibans', $ibans_obj );
	}

	/**
	 * Get var infos into json format.
	 *
	 * @return array
	 */
	public function getTemplateVarInfos(): array {
		$total_amount = WC()->cart->total;
		$vars         = array( 'total' => $total_amount );

		if ( count( $this->ibans ) > 0 ) {
			foreach ( $this->ibans as $iban ) {
				$vars['ibans'][] = array(
					'name'  => $iban['bank_name'],
					'total' => (float) $total_amount,
					'key'   => $iban['iban'],
					'bic'   => $iban['bic'],
					'rate'  => 1.0,
				);
			}
		}

		$vars['ibansJson'] = wp_json_encode( $vars['ibans'] );
		return $vars;
	}

	/**
	 * Generate payment fields to add into payment form
	 */
	public function payment_fields() {
		$description = $this->get_description();
		$description = ! empty( $description ) ? $description : '';

		$vars = $this->getTemplateVarInfos();
		wp_enqueue_script('qrcode',  YOUSEEME_PLUGIN_URL . '/assets/qrcode.min.js',[],false,array( 'in_footer' => true ));

		wp_enqueue_script( 'youseeme-front-iban', YOUSEEME_PLUGIN_URL . '/assets/front-iban.js', array( 'jquery','qrcode' ), YOUSEEME_VERSION, array( 'in_footer' => true ) );
		wp_localize_script(
			'youseeme-front-iban',
			'ibansDatas',
			array(
				'ibansJson'   => $vars['ibansJson'],
				'labelAmount' => esc_html__( 'Total amount to pay:', 'youseeme' ),
			)
		);
		if ( count( $this->ibans ) ) {
			echo "<div class='ysm_row'>
                        <div class='ysm_col'>
                            <p>" . esc_html( $description ) . "</p>
                            <p><select  style='padding:8px;width:250px' onchange='updateIbanPriceAndQr()' id='ibanSelect'>
                                <option value='-'>" . esc_html__( 'Please select an IBAN account', 'youseeme' ) . '</option>';
			foreach ( $this->ibans as $iban ) {
				echo "<option value='" . esc_html( $iban['bank_name'] ) . "'>" . esc_html( $iban['bank_name'] ) . '</option>';
			}
			echo '</select></p>
                            <div id="iban_price"></div>
                            <br/>
                            <p id="iban_amount_label" style="display:none;">' . esc_html__( 'You will need to transfer the total amount of your purchase to our account. You will receive your order confirmation via email, which will include our bank details and the order number.', 'youseeme' ) . '</p>
                            <div id="iban"></div>
                            <div id="bic"></div>
                            <input type="hidden" name="youseeme_iban_total" value="">
                            <input type="hidden" name="youseeme_iban_bank" value="">
                        </div>
                        <div class="ysm_col">
                            <img alt="Youpay logo" style=";margin:1rem 0;" src="' . esc_html( YOUSEEME_PLUGIN_URL ) . '/assets/YouPay_cryptopayment.png" width="154"/>
                            <div id="iban_QR" alt="qr_code"></div>
                        </div>
                    </div>
                <style>.ysm_row{display:flex}.ysm_col{width:50%;padding:1em;display: flex; flex-direction: column;justify-content: space-between;align-items: center;}@media(max-width: 767px){.ysm_col{width:100%}}</style> ';
		}
	}

	/**
	 * Handle the payment
	 *
	 * @param int $order_id Id of Order to be processed.
	 */
	public function process_payment( $order_id ) {
		$order = wc_get_order( $order_id );

		
		$youseeme_data_string = $_POST['youseeme_data'];

		if ( isset( $youseeme_data_string ) && ! empty( $youseeme_data_string ) ) {
			$youseeme_data = json_decode( $youseeme_data_string );
			$post_data      = array(
				'payment_method' => 'youseeme-iban',
				'youseeme_iban_total' => filter_var( $youseeme_data->youseeme_total, FILTER_VALIDATE_FLOAT ),
				'youseeme_iban_bank'  => filter_var( $youseeme_data->youseeme_bank, FILTER_SANITIZE_STRING ),
			);
		} else {
			$post_data = filter_input_array(
				INPUT_POST,
				array(
					'youseeme_iban_total' => FILTER_VALIDATE_FLOAT,
					'youseeme_iban_bank'  => FILTER_SANITIZE_STRING,
					'payment_method'      => FILTER_SANITIZE_STRING,
				)
			);
		}
		try {
			if ( 'youseeme-iban' === $post_data['payment_method']
				&& ! empty( $post_data['youseeme_iban_total'] )
				&& ! empty( $post_data['youseeme_iban_bank'] )
			) {
				
				$order->update_status( 'wc-pending' );
				$order->update_meta_data( 'youseeme_iban_total', floatval( wc_clean( wp_unslash( $post_data['youseeme_iban_total'] ) ) ) );
				$order->update_meta_data( 'youseeme_iban_bank', wc_clean( wp_unslash( $post_data['youseeme_iban_bank'] ) ) );
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
	 * @param int $order_id Id of Order to be processed.
	 */
	public function send_failed_order_email( int $order_id ) {
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

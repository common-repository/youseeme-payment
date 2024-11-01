/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-i18n/
 */
import { __ } from '@wordpress/i18n';
import React, { useState, useEffect } from 'react';

/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * Those files can contain any CSS code that gets applied to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
import './style.scss';

import { SelectControl, Flex, FlexItem } from '@wordpress/components';
import QrSvg from '@wojtekmaj/react-qr-svg';
import { CopyToClipboard } from 'react-copy-to-clipboard';
import { dispatch } from '@wordpress/data';

const { PAYMENT_STORE_KEY } = window.wc.wcBlocksData;

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-edit-save/#edit
 *
 * @return {Element} Element to render.
 */
const youseemeData = window.wc.wcSettings.getSetting( 'youseeme_data', {} );

export default function View() {
	const youseemeCryptocurrencies = youseemeData.settings.cryptos;

	const [ selectedCrypto, setSelectedCrypto ] = useState();
	const [ cryptoAddressCopied, setCryptoAddressCopied ] = useState( false );

	const [ cryptoAddress, setCryptoAddress ] = useState();
	const [ cryptoTotal, setCryptoTotal ] = useState();

	useEffect( () => {
		let address = '';
		let rate = '';
		let total = '';
		if ( selectedCrypto ) {
			const cryptoObj = youseemeCryptocurrencies.find(
				( item ) => item.name === selectedCrypto
			);
			if ( cryptoObj ) {
				address = cryptoObj.key;
				rate = cryptoObj.rate;
				total = cryptoObj.total;
			}
		}

		setCryptoAddress( address );
		setCryptoAddressCopied( false );
		setCryptoTotal( total );

		dispatch( PAYMENT_STORE_KEY ).__internalSetPaymentMethodData( {
			youseeme_data: JSON.stringify( {
				youseeme_total: total,
				youseeme_rate: rate,
				youseeme_crypto: selectedCrypto,
			} ),
		} );
	}, [ selectedCrypto, youseemeCryptocurrencies ] );

	const onCryptoChange = ( cryptoValue ) => {
		setSelectedCrypto( cryptoValue );
	};

	return (
		<>
			<div className="payment_box payment_method_youseeme">
				<Flex>
					<FlexItem className="ysm_col">
						<p>{ youseemeData.description }</p>
						<p>
							<SelectControl
								id="cryptoSelect"
								options={ [
									...[
										{
											label: __(
												'Please select your favorite Crypto',
												'youseeme'
											),
											value: '',
										},
									],
									...youseemeCryptocurrencies.map(
										( item ) => {
											return {
												label: item.name,
												value: item.name,
											};
										}
									),
								] }
								onChange={ onCryptoChange }
							/>
						</p>
						<div
							id="crypto_price"
							style={ {
								display: selectedCrypto ? 'block' : 'none',
								marginBottom: '30',
							} }
						>
							<b style={ { textTransform: 'uppercase' } }>
								{ ' ' }
								{ __( 'Amount', 'youseeme' ) }:
							</b>
							<br /> { cryptoTotal } { selectedCrypto }
						</div>
						<p
							id="crypto_amount_label"
							style={ {
								display: selectedCrypto ? 'block' : 'none',
							} }
						>
							{ __(
								'Please SCAN THE QR CODE to make your payment or COPY THE ADDRESS BELOW and paste it into your wallet to complete the payment.',
								'youseeme'
							) }
						</p>
						<div id="cryptoAddress">
							{ cryptoAddress ? (
								<p>
									<label htmlFor="cryptoAddress_input">
										{ __( 'Public address', 'youseeme' ) }:
									</label>
									<div
										style={ {
											display: 'flex',
											alignItems: 'center',
										} }
									>
										<input
											style={ { margin: 0 } }
											type="text"
											id="cryptoAddress_input"
											readOnly
											value={ cryptoAddress }
										/>
										<CopyToClipboard
											text={ cryptoAddress }
											onCopy={ () =>
												setCryptoAddressCopied( true )
											}
										>
											<button
												style={ {
													padding: '0.8rem 1rem',
												} }
												id="cryptoAddress_copy"
												className="button"
												type="button"
											>
												{ __( 'Copy', 'youseeme' ) }
											</button>
										</CopyToClipboard>
									</div>
									{ cryptoAddressCopied ? (
										<span style={ { color: 'red' } }>
											{ __( 'Copied', 'youseeme' ) }
										</span>
									) : null }
								</p>
							) : null }
						</div>
					</FlexItem>
					<FlexItem className="ysm_col">
						<img
							alt="Youpay logo"
							src={
								youseemeData.plugin_url +
								'/assets/YouPay_cryptopayment.png'
							}
							width="154"
						/>
						<QrSvg
							value={ cryptoAddress }
							level="H"
							style={ {
								display: selectedCrypto ? 'block' : 'none',
								width: '154',
								margin: 20,
							} }
						/>
					</FlexItem>
				</Flex>
			</div>
		</>
	);
}

/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-i18n/
 */
import { __ } from '@wordpress/i18n';
import React, { useState } from 'react';

/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-block-editor/#useblockprops
 */
import { InspectorControls } from '@wordpress/block-editor';
/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * Those files can contain any CSS code that gets applied to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
import './editor.scss';

import {
	TextControl,
	__experimentalNumberControl as NumberControl,
	FormToggle,
	SelectControl,
	PanelBody,
	Button,
} from '@wordpress/components';

const cryptoList = [
	'UNI',
	'FTM',
	'SHIB',
	'USDC',
	'dogecoin',
	'CARDANO',
	'BINANCE USD',
	'SOLANA',
	'TRON',
	'bitcoincash',
	'EUREC',
	'BTC',
	'ETH',
	'LTC',
	'USDT',
	'YOUSY',
	'WBTC',
	'FLOWS',
	'DASH',
	'DAI',
	'1INCH',
	'BNB',
	'MKR',
	'SUSHI',
	'MATIC',
	'OMG',
	'AUDIO',
	'OP',
	'ALICE',
	'AVAX',
	'UBN',
];

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-edit-save/#edit
 *
 * @return {Element} Element to render.
 */
export default function Edit() {
	const [ youseemeCryptocurrencies, setYouseemeCryptocurrencies ] = useState(
		window.youseemeCryptoData.youseeme_cryptocurrencies
	);
	const [ apiKey, setApiKey ] = useState(
		window.youseemeCryptoData.settings.api_key
	);
	const [ enabled, setEnabled ] = useState(
		window.youseemeCryptoData.settings.enabled
	);
	const [ commission, setCommission ] = useState(
		window.youseemeCryptoData.settings.commission
	);

	const onChangeApiKey = ( newApiKey ) => {
		setApiKey( newApiKey );
		window.youseemeCryptoData.settings.api_key = newApiKey;
	};

	const onChangeIsActive = ( newEnabled ) => {
		setEnabled( newEnabled );
		window.youseemeCryptoData.settings.enabled = newEnabled;
	};

	const onChangeCommission = ( newCommission ) => {
		setCommission( newCommission );
		window.youseemeCryptoData.settings.commission = newCommission;
	};

	const onCryptoAdd = ( newCrypto ) => {
		if ( newCrypto.length > 0 ) {
			const newCryptosObj = [ ...youseemeCryptocurrencies ];
			newCryptosObj.push( {
				crypto_name: newCrypto,
				crypto_address: '',
			} );
			newCryptosObj.sort( ( a, b ) => a.crypto_name > b.crypto_name );
			setYouseemeCryptocurrencies( newCryptosObj );
			window.youseemeCryptoData.youseeme_cryptocurrencies =
				youseemeCryptocurrencies;
		}
	};

	const onChangeAddress = ( newAddress, item ) => {
		const newCryptos = youseemeCryptocurrencies.map( ( cryptoObj ) => {
			if ( item.crypto_name === cryptoObj.crypto_name ) {
				cryptoObj.crypto_address = newAddress;
			}
			return cryptoObj;
		} );

		setYouseemeCryptocurrencies( newCryptos );
		window.youseemeCryptoData.youseeme_cryptocurrencies = newCryptos;
	};

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Configurations' ) }>
					<label htmlFor="enabled">
						{ __( 'Activer/desactiver' ) }
					</label>
					<FormToggle
						id="enabled"
						checked={ enabled }
						onChange={ onChangeIsActive }
					/>
					<TextControl
						label={ __( "Votre clé d'api" ) }
						placeHolder={ __( "Votre clé d'api" ) }
						value={ apiKey }
						help={ __(
							'Merci de souscrire sur https://user.youseeme.io pour l’ouverture d’un wallet gratuit pour vos encaissements et un Clé API- pour les Taux. Ou contact@youseeme.pro'
						) }
						onChange={ onChangeApiKey }
					/>
					<NumberControl
						label={ __( 'Commission (%)' ) }
						value={ commission }
						onChange={ onChangeCommission }
					/>
					<SelectControl
						label={ __( 'Ajouter une nouvelle crypto' ) }
						options={ [
							...[ { label: 'Choisir une crypto', value: '' } ],
							...cryptoList
								.filter(
									( option ) =>
										! youseemeCryptocurrencies.find(
											( item ) =>
												item.crypto_name === option
										)
								)
								.map( ( item ) => {
									return { label: item, value: item };
								} ),
						] }
						onChange={ onCryptoAdd }
					/>
					{ youseemeCryptocurrencies.map( ( item, index ) => (
						<PanelBody
							key={ 'crypto-' + index }
							title={ item.crypto_name }
							initialOpen={ false }
						>
							<TextControl
								style={ { width: '100%' } }
								placeHolder={ __( 'Adresse' ) }
								value={ item.crypto_address }
								onChange={ ( e ) => onChangeAddress( e, item ) }
							/>
							<Button
								variant="tertiary"
								style={ { color: 'red' } }
							>
								{ __( 'Supprimer' ) }
							</Button>
						</PanelBody>
					) ) }
				</PanelBody>
			</InspectorControls>
			<ul>
				<li className="wc_payment_method payment_method_youseeme">
					<input
						id="payment_method_youseeme"
						type="radio"
						className="input-radio"
						name="payment_method"
						value="youseeme"
						data-order_button_text=""
					/>
					<label htmlFor="payment_method_youseeme">
						{ ' ' }
						Payer avec des crypto-monnaies{ ' ' }
					</label>
					<div className="payment_box payment_method_youseeme">
						<div className="ysm_row">
							<div className="ysm_col">
								<p>Payez avec votre crypto-monnaie préférée</p>
								<p>
									<select
										onChange="updatePriceAndQr()"
										id="cryptoSelect"
									>
										<option value="-">
											Veuillez sélectionner votre crypto
											préféré
										</option>
										<option value="1INCH">1INCH</option>
										<option value="ALICE">ALICE</option>
										<option value="AUDIO">AUDIO</option>
									</select>
								</p>
								<div id="crypto_price"></div>
								<p id="crypto_amount_label">
									Veuillez SCANNEZ LE CODE QR pour effectuer
									votre paiement ou COPIEZ L&aposADRESSE
									CI-DESSOUS et collez-la dans votre
									portefeuille pour compléter le paiement.
								</p>
								<div id="crypto_address"></div>
							</div>
							<div className="ysm_col">
								<img
									alt="Youpay logo"
									src="http:localhost:8888/test_wp/wp-content/plugins/youseeme/assets/YouPay_cryptopayment.png"
									width="154"
								/>
								<img src="" id="crypto_QR" alt="qr_code" />
							</div>
						</div>
					</div>
				</li>
			</ul>
		</>
	);
}

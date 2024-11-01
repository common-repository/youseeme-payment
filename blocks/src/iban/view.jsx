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
const youseemeData = window.wc.wcSettings.getSetting(
	'youseeme-iban_data',
	{}
);

export default function View() {
	const youseemeIbans = youseemeData.settings.ibans;
	const [ selectedIban, setSelectedIban ] = useState();

	const [ ibanName, setIbanName ] = useState();
	const [ ibanValue, setIbanValue ] = useState();
	const [ ibanTotal, setIbanTotal ] = useState();
	const [ ibanBic, setIbanBic ] = useState();

	useEffect( () => {
		let value = '';
		let bic = '';
		let total = '';
		let name = '';
		if ( selectedIban ) {
			const ibanObj = youseemeIbans.find(
				( item ) => item.name === selectedIban
			);
			if ( ibanObj ) {
				name = ibanObj.name;
				value = ibanObj.key;
				bic = ibanObj.bic;
				total = ibanObj.total;
			}
		}

		setIbanName( name );
		setIbanValue( value );
		setIbanTotal( total );
		setIbanBic( bic );

		dispatch( PAYMENT_STORE_KEY ).__internalSetPaymentMethodData( {
			youseeme_data: JSON.stringify( {
				youseeme_total: total,
				youseeme_bank: selectedIban,
			} ),
		} );
	}, [ selectedIban, youseemeIbans ] );

	const onCryptoChange = ( cryptoValue ) => {
		setSelectedIban( cryptoValue );
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
											label: window.wp.i18n.__(
												'Please select your favorite Crypto',
												'youseeme'
											),
											value: '',
										},
									],
									...youseemeIbans.map( ( item ) => {
										return {
											label: item.name,
											value: item.name,
										};
									} ),
								] }
								onChange={ onCryptoChange }
							/>
						</p>

						{ selectedIban ? (
							<>
								<div
									id="crypto_price"
									style={ { marginBottom: '30' } }
								>
									<b style={ { textTransform: 'uppercase' } }>
										{ ' ' }
										{ __( 'Amount', 'youseeme' ) }:
									</b>
									<br /> { ibanTotal } €
								</div>
								<p id="crypto_amount_label">
									{ __(
										'You will need to transfer the total amount of your purchase to our account. You will receive your order confirmation via email, which will include our bank details and the order number.',
										'youseeme'
									) }
								</p>

								<div id="iban">
									<span>
										<b>IBAN:</b> { ibanValue }
									</span>
								</div>
								<div id="bic">
									<span>
										<b>BIC:</b>
										{ ibanBic }
									</span>
								</div>
							</>
						) : null }
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
							value={
								'BCD%0A001%0A1%0ASCT%0A' +
								ibanBic +
								'%0A' +
								ibanName +
								'%0A' +
								ibanValue
							}
							level="H"
							style={ {
								display: selectedIban ? 'block' : 'none',
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

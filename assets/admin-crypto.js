function updateQR() {
	let id    = event.target.id;
	let arr   = id.split( '_' );
	let x     = arr[arr.length - 1];
	let value = event.target.value;
	
	document.getElementById("qr_" + x).innerHTML = "";
	new QRCode(document.getElementById("qr_" + x), {
		text: value,
		width: 40,
		height: 40,
		colorDark : "#000000",
		colorLight : "#ffffff",
		correctLevel : QRCode.CorrectLevel.H
	});
}
function updateQRByIndex(x) {
	const name = document.getElementById( "mycryptosName_" + x ).value;
	if (name !== 'IBAN') {
		let value = document.getElementById( "mycryptosAddress_" + x ).value;
		document.getElementById("qr_" + x).innerHTML = "";
		new QRCode(document.getElementById("qr_" + x), {
			text: value,
			width: 40,
			height: 40,
			colorDark : "#000000",
			colorLight : "#ffffff",
			correctLevel : QRCode.CorrectLevel.H
		});
	}
}

function get_cryptos(){
	let available_cryptos_origin = ["UNI","FTM","SHIB","USDC","dogecoin","CARDANO","BINANCE USD","SOLANA","TRON","bitcoincash","EUREC","BTC","ETH","LTC","USDT","YOUSY","WBTC","FLOWS","DASH","DAI","1INCH","BNB","MKR","SUSHI","MATIC","OMG","AUDIO","OP","ALICE","AVAX","UBN"];
	available_cryptos_origin.sort();

	const existing_cryptos_list = document.querySelectorAll( '.crypto_name' );
	const existing_cryptos      = [];
	existing_cryptos_list.forEach(
		(input) => {
			existing_cryptos.push( input.value );
		}
	)

	return available_cryptos_origin.filter( (item) => ! existing_cryptos.includes( item ) )
}



jQuery(
	function () {
		let available_cryptos = get_cryptos()

		document.querySelectorAll( '.crypto_address' ).forEach(
			(input) => {
				let id  = input.id;
				let arr = id.split( '_' );
				let x   = arr[arr.length - 1];
				updateQRByIndex( x )
			}
		)

		const youseeme_cryptos = jQuery( '#youseeme_cryptos' );
		youseeme_cryptos.on(
			'click',
			'a.add',
			function () {
				let size = youseeme_cryptos.find( 'tbody .cryptos' ).length;

				let html = '<tr class="cryptos">\
									<td class="sort"></td>\
                                        <td><select class="crypto_name" name="crypto_name[' + size + ']" id="mycryptosName_' + size + '">';

				available_cryptos = get_cryptos()

				available_cryptos.forEach(
					(crypto) => {
						html += '<option value="' + crypto + '">' + crypto + '</option>';
					}
				)

				html += '</select></td>\
                                        <td id="cryptoAdressColumn_' + size + '"><input type="text" name="crypto_address[' + size + ']" id="mycryptosAddress_' + size + '" placeholder="ex :02f5e25778dcee9539b25799831277eb8e73" onChange="updateQR()"  /></td>\
                                        <td id="cryptoQrColumn_' + size + '"><img id = "qr_' + size + '" width="40" height="40" src="" alt="qr_code" /></td>\
                                    </tr>';

				jQuery( html ).appendTo( '#youseeme_cryptos table tbody' );

				return false;
			}
		);
		youseeme_cryptos.on(
			'click',
			'a.remove_rows',
			function () {
				available_cryptos = get_cryptos()
			}
		);

	}
);

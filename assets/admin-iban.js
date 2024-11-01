/**
 * Admin javascript interactions for gateway IBAN
 */
function updateIbanQR()
{
	let id         = event.target.id;
	let arr        = id.split( '_' );
	let x          = arr[arr.length - 1];
    const name     = document.getElementById( "myibansName_" + x ).value;
    const bic = document.getElementById( "myibansAddress_BIC_" + x )?.value
	let value      = event.target.value;
	
	document.getElementById("qr_" + x).innerHTML = "";
	new QRCode(document.getElementById("qr_" + x), {
		text: "BCD%0A001%0A1%0ASCT%0A" + bic + "%0A" + name + "%0A" + value,
		width: 40,
		height: 40,
		colorDark : "#000000",
		colorLight : "#ffffff",
		correctLevel : QRCode.CorrectLevel.H
	});


}
function updateIbanQRByIndex(x)
{
	const name = document.getElementById( "myibansName_" + x ).value;
	const bic  = document.getElementById( "myibansAddress_BIC_" + x )?.value;
	let value  = document.getElementById( "myibansAddress_" + x ).value;
	/* Use of https://developers.google.com/chart/infographics/docs/qr_codes for instant generation of qr code (cht for type qr, chs for size, chl for string to pass */
	//document.getElementById( "qr_" + x ).src = "https://chart.googleapis.com/chart?cht=qr&chs=60x60&choe=<UTF-8>&chl=BCD%0A001%0A1%0ASCT%0A" + bic + "%0A" + name + "%0A" + value;
	document.getElementById("qr_" + x).innerHTML = "";

	new QRCode(document.getElementById("qr_" + x), {
		text: "BCD%0A001%0A1%0ASCT%0A" + bic + "%0A" + name + "%0A" + value,
		width: 40,
		height: 40,
		colorDark : "#000000",
		colorLight : "#ffffff",
		correctLevel : QRCode.CorrectLevel.H
	});
}


function get_ibans()
{
	let available_ibans_origin = ["<?= __( 'Bank name', 'youseeme' )?>","Youseeme"];
	available_ibans_origin.sort();

	const existing_ibans_list = document.querySelectorAll( '.bank_name' );
	const existing_ibans      = [];
	existing_ibans_list.forEach( ( input ) => { existing_ibans.push( input.value ); } );

	return available_ibans_origin.filter( ( item) => ! existing_ibans.includes( item ) )
}

jQuery(
	function () {
		let available_ibans = get_ibans()

		document.querySelectorAll( '.iban' ).forEach(
          ( input ) => {
            let id  = input.id;
            let arr = id.split( '_' );
            let x   = arr[arr.length - 1];
            updateIbanQRByIndex( x )
          }
		)

		const youseeme_ibans = jQuery( '#youseeme_ibans' );
		youseeme_ibans.on(
			'click',
			'a.add,a.add_youseeme',
			function (e) {
				let size            = youseeme_ibans.find( 'tbody .ibans' ).length;
				const buttonClasses = e.target.classList;
				let html            = '<tr class="ibans">' +
				'<td class="sort"></td>';
				if (buttonClasses.contains( 'add_youseeme' )) {
					html += '<td id="bankColumn_' + size + '"><input type="text" readonly class="bank_name" name="bank_name[' + size + ']" id="myibansName_' + size + '" value="Youseeme" onChange="updateIbanQR()"></td>';
				} else {
					html += '<td id="bankColumn_' + size + '"><input type="text" class="bank_name" name="bank_name[' + size + ']" id="myibansName_' + size + '" placeholder="BNP Paribas" onChange="updateIbanQR()"></td>';
				}

				html += '<td id="ibanColumn_' + size + '"><input type="text" name="iban[' + size + ']" id="myibansAddress_' + size + '" placeholder="FR76 ...." onChange="updateIbanQR()"  /></td>' +
				'<td id="bicColumn_' + size + '"><input type="text" name="bic[' + size + ']" id="myibansBic_' + size + '" placeholder="XXXXXXX" onChange="updateIbanQR()"  /></td>' +
				'<td id="ibanQrColumn_' + size + '"><div id = "qr_' + size + '" alt="qr_code" ></div></td>' +
				'</tr>';

				jQuery( html ).appendTo( '#youseeme_ibans table tbody' );

				return false;
			}
		);
		youseeme_ibans.on(
			'click',
			'a.remove_rows',
			function () {
				available_ibans = get_ibans()
			}
		);

	}
);

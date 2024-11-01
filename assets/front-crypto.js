window.addEventListener(
	"DOMContentLoaded",
	() => {
		updatePriceAndQr();
	}
);

function updatePriceAndQr() {
	let cryptosObj        = JSON.parse( cryptosDatas.cryptosJson );
	let cryptoSelectValue = document.getElementById( "cryptoSelect" ).value;
	if (cryptosObj && cryptosObj.length > 0) {

		if (cryptoSelectValue === "-") {
			document.getElementById( "crypto_price" ).innerText            = "";
			document.getElementById( "crypto_QR" ).src                     = "";
			document.getElementById( "crypto_address" ).innerHTML          = "";
			document.getElementById( "crypto_amount_label" ).style.display = "none";
			document.getElementById( "crypto_QR" ).style.display           = "none";
			document.getElementsByName( "youseeme_total" )[0].value        = "";
			document.getElementsByName( "youseeme_rate" )[0].value         = "";
			document.getElementsByName( "youseeme_crypto" )[0].value       = "";
		} else {
			for (let i = 0; i < cryptosObj.length; i++) {
				if (cryptosObj && cryptosObj[i] && cryptoSelectValue === cryptosObj[i]["name"]) {
					document.getElementById( "crypto_price" ).innerHTML = "<b style=\"text-transform: uppercase\">" + cryptosDatas.labelAmount + "</b> " + cryptosObj[i]["total"] + " " + cryptoSelectValue;
					
					document.getElementById("crypto_QR").innerHTML = "";
					new QRCode(document.getElementById("crypto_QR"), {
						text:  cryptosObj[i]["key"],
						width: 154,
						height: 154,
						colorDark : "#000000",
						colorLight : "#ffffff",
						correctLevel : QRCode.CorrectLevel.H
					});
					
					document.getElementById( "crypto_amount_label" ).style.display = "block";
					document.getElementsByName( "youseeme_total" )[0].value        = cryptosObj[i]["total"];
					document.getElementsByName( "youseeme_rate" )[0].value         = cryptosObj[i]["rate"];
					document.getElementsByName( "youseeme_crypto" )[0].value       = cryptoSelectValue;
					document.getElementById( "crypto_address" ).innerHTML          = "<p><label>" + cryptosDatas.labelAddress + ":</label> " +
																							"<div style='display:flex;align-items:center'>" +
																								"<input style='margin:0' type='text' id='crypto_address_input' readonly value='" + cryptosObj[i]['key'] + "'><button style='padding: 0.8rem 1rem;' id='crypto_address_copy' class='button' type='button' onclick='copy()'>Copy</button>" +
																							"</div></p>";

					return;
				}
			}
		}

	}

}
function copy(){
	const copyText = document.getElementById( "crypto_address_input" );
	const button   = document.getElementById( "crypto_address_copy" );
	copyText.select();
	copyText.setSelectionRange( 0, 99999 ); // For mobile devices
	navigator.clipboard.writeText( copyText.value );
	button.innerText        = 'Copied';
	button.style.background = '#00c062'
}

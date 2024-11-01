window.addEventListener(
	"DOMContentLoaded",
	() => {
		updateIbanPriceAndQr();
	}
);

function updateIbanPriceAndQr() {
	let ibansObj        = JSON.parse( ibansDatas.ibansJson );
	let ibanSelectValue = document.getElementById( "ibanSelect" ).value;
	if (ibansObj && ibansObj.length > 0) {

		if (ibanSelectValue === "-") {
			document.getElementById( "iban_price" ).innerText            = "";
			document.getElementById( "iban_QR" ).src                     = "";
			document.getElementById( "iban" ).innerHTML                  = "";
			document.getElementById( "bic" ).innerHTML                   = "";
			document.getElementById( "iban_amount_label" ).style.display = "none";
			document.getElementById( "iban_QR" ).style.display           = "none";
			document.getElementsByName( "youseeme_iban_total" )[0].value = "";
			document.getElementsByName( "youseeme_iban_bank" )[0].value  = "";
		} else {
			for (let i = 0; i < ibansObj.length; i++) {
				if (ibansObj && ibansObj[i] && ibanSelectValue === ibansObj[i]["name"]) {
					document.getElementById( "iban_price" ).innerHTML = "<b style=\"text-transform: uppercase\">" + ibansDatas.labelAmount + "</b> " + ibansObj[i]["total"] + " €";

					document.getElementById("iban_QR" ).innerHTML = "";
					new QRCode(document.getElementById("iban_QR"), {
						text: "BCD%0A001%0A1%0ASCT%0A" + ibansObj[i]["bic"] + "%0A" + ibansObj[i]["name"] + "%0A" + ibansObj[i]["key"],
						width: 154,
						height: 154,
						colorDark : "#000000",
						colorLight : "#ffffff",
						correctLevel : QRCode.CorrectLevel.H
					});
					document.getElementById( "iban_amount_label" ).style.display = "block";
					document.getElementsByName( "youseeme_iban_total" )[0].value = ibansObj[i]["total"];
					document.getElementsByName( "youseeme_iban_bank" )[0].value  = ibanSelectValue;
					document.getElementById( "iban" ).innerHTML                  = "<span><b>IBAN:</b> " + ibansObj[i]["key"] + "</span>";
					document.getElementById( "bic" ).innerHTML                   = "<span><b>BIC:</b> " + ibansObj[i]["bic"] + "</span>";

					return;
				}
			}
		}

	}

}

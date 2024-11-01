=== YOUSEEME ===
Contributors: youseemepay
Donate link: https://youseeme.fr/youpay/
Tags: crypto, payment, woocommerce, payment request
Requires at least: 6.0
Tested up to: 6.4
Stable tag: 1.1.4
Requires PHP: 8.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

YouPay is an IBAN account and payment plugin that enables credit card payments to be instantly credited to your IBAN. It also accepts crypto payments, converting them into euros, and offers instant transfers to your IBAN via the YOUSEEME FINANCE mobile app.

== Description ==

YouPay crypto plugin offers:

**Wider reach & sales**: Attract crypto users & save on fees.
**Secure transactions**: Encrypted, low fraud risk.
**Simplified global sales**: Avoid FX and restrictions.
**Innovative image**: Stand out with cutting-edge payments.
**Customizable commissions**: Set your rates (1-2%).

**Youseeme** is a company registered with the AMF (French Financial Markets Authority) as a prestataire de services sur actifs numériques, or digital asset service provider (PSAN or DASP) under the registration number E2022-50 and Electronic Money Distributors (EMD) appointed by the company Treezor EME registered with the ACPR (TheFrench Prudential Supervision and Resolution Authority) under number 63512.

**Note**: YouPay by Youseeme only supports EUR in WooCommerce.

== Advantages ==

**YouPay** facilitates B2B commerce
Here are some features of the YouPay cryptocurrency payment plugin:

* **Cryptocurrency Payments**:Customers can make payments using cryptocurrencies such as Bitcoin, Ethereum, or other altcoins.
* **Fiat payments**:Customers can also choose to pay with their regular bank cards for more flexibility.
* **Automatic Conversion**: Cryptocurrency payments can be automatically converted to fiat currency, enabling merchants to accept cryptocurrency payments without worrying about currency conversion.
* **User-Friendly Interface**: The plugin is designed to be easy to use, featuring an intuitive user interface for customers.
* **Enhanced Security**: Cryptocurrency payments are encrypted and anonymous, providing an increased level of security for both customers and merchants.
* **Lower transaction fees**: Transaction fees for cryptocurrency payments are generally lower than those for credit card or wire transfer payments, which can save merchants money.
* **Multi-Language Support**: The plugin is available in multiple languages to meet the needs of international customers.
* **Technical Support**: The plugin comes with technical support to assist merchants with any technical issues.


== Recommandations ==

You can also sign up on ([Youseeme Crypto](https://user.youseeme.io)) to obtain your free wallet for crypto collection and to obtain your crypto public address or alternatively, you can email us at ([customer support](support@youseeme.fr)).

== Installation ==

**Prerequisites**

To install and configure **WooCommerce YouPay**, you will need:

- WordPress Version 6.4.3 or higher (installed)
- WooCommerce Version 7.2.0 or higher (installed and activated)
- PHP Version 8.0 or higher
- YouPay Plug In

**Installation instructions**

- Log in to WordPress admin.
- Go to Extensions > Add.
- Search for YouPay plugin.
- Click on Install Now and wait until the plugin is installed successfully.
- You can activate the plugin immediately by clicking on Activate now on the success page. If you want to activate it later, you can do so via Plugins > Installed Plugins.

**Installation and Configuration**

Follow the steps below to activate the YouPay plugin

- Go to WooCommerce > Settings.
 - **1** Click the **Payments** tab.
 - **2**The Payment methods list may include two YouPay options. 
 - **3**To setup YouPay Crypto, click on "**Youseeme - Cryptocurrencies**" – Pay using Crypto.

**Crypto Setting**
 - **1.** **Activate/Désactivate**
Check that "**Activate payment by cryptocurrencies**" is ticked
 - **2** **API key** 
Copy and paste the API key after requesting it via email from our customer service at contact@youseeme.pro, 
or alternatively, sign up for an account on ([Youseeme Crypto](https://user.youseeme.io/login)) to obtain your free wallet for cash collection and obtain the API key for rates.
 - **3** **Commission** (%)
enter your commission amount in %.
 - **4** **Cryptocurrencies**
   - **5** Click "**+ Add crypto**", select the cryptocurrency you wish to accept for payment, copy the public address from your cryptocurrency wallet, and paste it into the address field.
**To remove the crypto**, select it in the address field and click on "Remove selected crypto."

 - **6** To complete your configuration, click the "**Save Changes**" button.

**Note** :The cryptocurrency QR Code will be automatically displayed on the payment page after you have saved your setup. 

** IBAN setting**
 - **4** To setup IBAN as payment method, click on **"Youseeme-IBAN"** - Pay using IBAN
   - **1** Click on "+ Add IBAN" to enter your bank account details: input your **Bank Name**, **IBAN**, **BIC/SWIFT**, and the **QR Code** will be automatically displayed once you have saved your setup**(2)**.

**Tips** : You can subscribe a IBAN account on ([Youseeme Finance](https://user.youseeme.fr)), which allows you to manage your online sales separately from your usual deposit account. Please visit :([Youseeme Finance web site](https://youseeme.fr)).

==What is the QR Code used for when paying by IBAN ?==
The QR Code facilitates payment via express SEPA bank transfer by simply scanning the QR Code with the client's banking application EPC QR Code protocol(The European Payments Council Quick Response Code protocol). For more information on it, please visit([EPC QR Code] (https://www.europeanpaymentscouncil.eu/document-library/guidance-documents/standardisation-qr-codes-mscts))

If you get stuck, you can ask for help at:([Customer service](mailto:support@youseeme.fr))

== Frequently Asked Questions ==
= Does this support recurring payments, like for subscriptions? =
- Yes!

= Where can I get support or talk to other users? =
-If you get stuck, you can ask for help in the Plugin Forum.

= What is the QR Code used for when paying by IBAN ? =
- The QR Code facilitates payment via express SEPA bank transfer by simply scanning the QR Code with the client's banking application EPC QR Code protocol(The European Payments Council Quick Response Code protocol). For more information on it, please visit([EPC QR Code](https://www.europeanpaymentscouncil.eu/document-library/guidance-documents/standardisation-qr-codes-mscts))

== Upgrade Notice ==

Automatic updates should work smoothly; however, as always, make sure to back up your site just in case


== Screenshots ==

1. Setting Page
2. Crypto setting page
3. IBAN Setting


== Changelog ==

= 1.1.3 =
* Security updates

= 1.1.3 =
* Security updates

= 1.1.2 =
* Security updates and code style

* Add IBANS integration
* Backoffice and frontoffice UI improvements
= 1.1.0 =

* Add IBANS integration
* Backoffice and frontoffice UI improvements

 = 1.0.0 =
* Plugin first submission

== Third party services ==

= Google Chart =

We use https://chart.googleapis.com/ for instant generation of qr code
Inputs: 
    - cht for type: always qr, 
    - chs for size: eg: 40x40, 
    - chl for youseeme wallet id
Output: a qr_code image
See full description of API here https://developers.google.com/chart/infographics/docs/qr_codes
For more informations see ([Privacy Policy](https://policies.google.com/privacy)) and ([Terms of Service](https://policies.google.com/terms))

= Youseeme.io = 

We use https://api.youseeme.io/ API (especifically https://api.youseeme.io/v1/ecommerce/plugins/crypto/prices) 
Inputs:
    - Youseeme API key
    - serial-id made of hash generated from your API key and your website url
Output:
    - json list of cryptocurrencies price conversion
For more informations see ([Privacy Policy](https://youseeme.io/protection-des-donnees-personnelles-rgpd/)) and ([Terms of Service](https://youseeme.io/conditions-generales-de-ventes/))
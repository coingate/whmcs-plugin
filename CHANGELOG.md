WHMCS CoinGate Payment Gateway
============================

v2.0.2
----
* Removed deprecated “Receive Currency” setting. Settlement currency is now managed under API App → Currency Settings.
* Added option to pass billing information to the payment processor, improving shopper experience by prefilling details during checkout.

v2.0.1
----
* Directory name fix for CoinGate library

v2.0.0
----
* Plugin was completely refactored
* CoinGate PHP SDK library updated to v4.1.0
* Added possibility to auto-cancel order on canceled, expired or invalid status events

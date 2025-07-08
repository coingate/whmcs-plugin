# WHMCS CoinGate Payment Gateway

Accept cryptocurrency payments on your WHMCS website.

Read the module installation instructions below to get started with CoinGate payment gateway for your shop.

## Install

Sign up for CoinGate account at <https://coingate.com> for production and <https://sandbox.coingate.com> for testing (sandbox) environment.

Please note, that for "Test" mode you **must** generate separate API credentials on <https://sandbox.coingate.com>. API credentials generated on <https://coingate.com> will **not** work for "Test" mode.

### via FTP

1. Download <https://github.com/coingate/whmcs-plugin/releases/download/v2.0.2/coingate-whmcs-2.0.2.zip>
2. Extract ZIP archive and upload files to web root.
3. In admin panel, go to **Setup » Payments » Payment Gateways** and select **All Payment Gateways**. Click **CoinGate**.
4. Inside *CoinGate* block fill the [API Credentials](https://support.coingate.com/en/42/how-can-i-create-coingate-api-credentials) (*Auth Token*) and other fields. Click **Save Changes**.


### Upgrading from v1.* to v2.*
If you're upgrading from version 1.* to 2.*, please read the upgrade guide here: [How to Upgrade CoinGate WHMCS Plugin from Version 1.* to 2.*](https://github.com/coingate/whmcs-plugin/wiki/How-to-Upgrade-CoinGate-WHMCS-Plugin-from-Version-1.*-to-2.*)

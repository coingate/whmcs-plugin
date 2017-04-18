# WHMCS CoinGate Plugin

CoinGate bitcoin payment gateway WHMCS plugin.

## Install

Sign up for CoinGate account at <https://coingate.com> for production and <https://sandbox.coingate.com> for testing (sandbox) environment.

Please note, that for "Test" mode you **must** generate separate API credentials on <https://sandbox.coingate.com>. API credentials generated on <https://coingate.com> will **not** work for "Test" mode.

### via FTP

1. Download [coingate-whmcs.zip](https://github.com/coingate/whmcs-plugin/releases/download/v1.0.1/coingate-whmcs-1.0.1.zip).
2. Extract ZIP archive and upload files to web root.
3. In admin panel, go to **Setup » Payments » Payment Gateways** and select **All Payment Gateways**. Click **CoinGate**.
4. Inside *CoinGate* block fill the [API Credentials](http://support.coingate.com/knowledge_base/topics/how-can-i-create-coingate-api-credentials) (App ID, Api Key, Api Secret) and other fields. Click **Save Changes**

# WHMCS CoinGate Plugin

Accept cryptocurrency payments on your WHMCS website.

Read the module installation instructions below to get started with CoinGate payment gateway for your shop.

## Install

Sign up for CoinGate account at <https://coingate.com> for production and <https://sandbox.coingate.com> for testing (sandbox) environment.

Please note, that for "Test" mode you **must** generate separate API credentials on <https://sandbox.coingate.com>. API credentials generated on <https://coingate.com> will **not** work for "Test" mode.

Also note, that *Receive Currency* parameter in your module configuration window defines the currency of your settlements from CoinGate. Set it to BTC, USDT, EUR or USD, depending on how you wish to receive payouts. To receive settlements in **Euros** or **U.S. Dollars** to your bank, you have to verify as a merchant on CoinGate (login to your CoinGate account and click *Verification*). If you set your receive currency to **Bitcoin**, verification is not needed.

### via FTP

1. Download <https://github.com/coingate/whmcs-plugin/releases/download/v2.0.0/coingate-whmcs-2.0.0.zip>
2. Extract ZIP archive and upload files to web root.
3. In admin panel, go to **Setup » Payments » Payment Gateways** and select **All Payment Gateways**. Click **CoinGate**.
4. Inside *CoinGate* block fill the [API Credentials](https://support.coingate.com/en/42/how-can-i-create-coingate-api-credentials) (*Auth Token*) and other fields, also set your **Receive Currency**. Click **Save Changes**

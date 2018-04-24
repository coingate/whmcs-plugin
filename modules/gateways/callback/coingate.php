<?php

include('../../../includes/functions.php');
include('../../../includes/gatewayfunctions.php');
include('../../../includes/invoicefunctions.php');

if (file_exists('../../../dbconnect.php'))
    include '../../../dbconnect.php';
else if (file_exists('../../../init.php'))
    include '../../../init.php';
else
    die('[ERROR] In modules/gateways/callback/coingate.php: include error: Cannot find dbconnect.php or init.php');

$gatewaymodule = 'coingate';
$GATEWAY = getGatewayVariables($gatewaymodule);

if (!$GATEWAY['type']) {
    logTransaction($GATEWAY['name'], $_POST, 'Not activated');
    die('[ERROR] In modules/gateways/callback/coingate.php: CoinGate module not activated.');
}

$order_id = $_REQUEST['order_id'];
$invoice_id = checkCbInvoiceID($order_id, $GATEWAY['coingate']);

if (!$invoice_id)
    throw new Exception('Order #' . $invoiceid . ' does not exists');

$trans_id = $_REQUEST['id'];

checkCbTransID($trans_id);

$fee = 0;
$amount = '';

require_once('../CoinGate/init.php');
require_once('../CoinGate/version.php');

$authentication = array(
    'auth_token'    => empty($GATEWAY['ApiAuthToken']) ? $GATEWAY['ApiSecret'] : $GATEWAY['ApiAuthToken'],
    'environment'   => $GATEWAY['Environment'],
    'user_agent'    => 'CoinGate - WHMCS Extension v' . COINGATE_PLUGIN_VERSION
);

$coingate_order = \CoinGate\Merchant\Order::findOrFail($_REQUEST['id'], array(), $authentication);

switch ($coingate_order->status) {
    case 'paid':
        addInvoicePayment($invoice_id, $trans_id, $amount, $fee, $gatewaymodule);
        logTransaction($GATEWAY['name'], $response, 'Payment is confirmed by the network, and has been credited to the merchant. Purchased goods/services can be securely delivered to the buyer.');
        break;
    case 'pending':
        logTransaction($GATEWAY['name'], $response, 'Buyer selected payment currency. Awaiting payment.');
        break;
    case 'confirming':
        logTransaction($GATEWAY['name'], $response, 'Buyer transferred the payment for the invoice. Awaiting blockchain network confirmation.');
        break;
    case 'canceled':
        logTransaction($GATEWAY['name'], $response, 'Buyer canceled the invoice.');
        break;
    case 'expired':
        logTransaction($GATEWAY['name'], $response, 'Buyer did not pay within the required time and the invoice expired.');
        break;
    case 'invalid':
        logTransaction($GATEWAY['name'], $response, 'Payment rejected by the network or did not confirm.');
        break;
    case 'refunded':
        logTransaction($GATEWAY['name'], $response, 'Payment was refunded to the buyer.');
        break;
}

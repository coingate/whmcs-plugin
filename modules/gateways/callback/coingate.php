<?php

// Require libraries needed for gateway module functions.
require_once __DIR__ . '/../../../init.php';
require_once __DIR__ . '/../../../includes/gatewayfunctions.php';
require_once __DIR__ . '/../../../includes/invoicefunctions.php';

use WHMCS\Database\Capsule;

// Detect module name from filename.
$gatewayModuleName = basename(__FILE__, '.php');

// Fetch gateway configuration parameters.
$gatewayParams = getGatewayVariables($gatewayModuleName);

// Die if module is not active.
if (! $gatewayParams['type']) {
    die('Module not activated.');
}

$transactionId = $_POST['id'];

/**
 * Check Callback Transaction ID.
 *
 * Performs a check for any existing transactions with the same given
 * transaction number.
 *
 * Performs a die upon encountering a duplicate.
 *
 * @param string $transactionId Unique Transaction ID
 */
checkCbTransID($transactionId);

// ------------------------------------------------------------------
// ------------------------------------------------------------------

require_once __DIR__ . '/../coingate/client.php';

$client = new CoinGate\Client(
    $gatewayParams['apiAuthToken'],
    $gatewayParams['useSandboxEnv']
);

$order = $client->order->get($transactionId);

/**
 * Validate Callback Invoice ID.
 *
 * Checks invoice ID is a valid invoice number. Note it will count an
 * invoice in any status as valid.
 *
 * Performs a die upon encountering an invalid Invoice ID.
 *
 * Returns a normalised invoice ID.
 *
 * @param int $invoiceId Invoice ID
 * @param string $gatewayName Gateway Name
 */
$invoiceId = checkCbInvoiceID($order->order_id, $gatewayParams['name']);

/**
 * Log Transaction.
 *
 * Add an entry to the Gateway Log for debugging purposes.
 *
 * The debug data can be a string or an array. In the case of an
 * array it will be
 *
 * @param string $gatewayName        Display label
 * @param string|array $debugData    Data to log
 * @param string $transactionStatus  Status
 */

$transactionStatusMessage = [
    'paid' => 'Payment is confirmed by the network, and has been credited to the merchant. Purchased goods/services can be securely delivered to the buyer.',
    'pending' => 'Buyer selected payment currency. Awaiting payment.',
    'confirming' => 'Buyer transferred the payment for the invoice. Awaiting blockchain network confirmation.',
    'canceled' => 'Buyer canceled the invoice.',
    'expired' => 'Buyer did not pay within the required time and the invoice expired.',
    'invalid' => 'Payment rejected by the network or did not confirm.',
    'refunded' => 'Payment was refunded to the buyer.',
];

logTransaction($gatewayParams['name'], $_POST, $transactionStatusMessage[$order->status]);


if (in_array($order->status, ['canceled', 'expired', 'invalid'])) {

    $action = $gatewayParams['action_on_' . $order->status];

    if ($action == 'canceled') {
        localAPI('CancelOrder', [
            'orderid' => Capsule::table('tblorders')
                ->where('paymentmethod', $gatewayModuleName)
                ->where('invoiceid', $invoiceId)
                ->value('id')
        ]);
    }

}

elseif ($order->status == 'paid') {

    /**
     * Add Invoice Payment.
     *
     * Applies a payment transaction entry to the given invoice ID.
     *
     * @param int $invoiceId         Invoice ID
     * @param string $transactionId  Transaction ID
     * @param float $paymentAmount   Amount paid (defaults to full balance)
     * @param float $paymentFee      Payment fee (optional)
     * @param string $gatewayModule  Gateway module name
     */
    addInvoicePayment(
        $invoiceId,
        $transactionId,
        null,
        0,
        $gatewayModuleName
    );

}

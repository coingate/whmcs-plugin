<?php

if (! defined('WHMCS')) {
    die('This file cannot be accessed directly');
}

require_once __DIR__ . '/coingate/client.php';

/**
 * Define module related meta data.
 *
 * Values returned here are used to determine module related capabilities and
 * settings.
 *
 * @see https://developers.whmcs.com/payment-gateways/meta-data-params/
 *
 * @return array
 */
function coingate_MetaData()
{
    return [
        'DisplayName' => 'CoinGate Payment Gateway Module',
        'APIVersion' => '1.1', // Use API Version 1.1
        'DisableLocalCreditCardInput' => true,
        'TokenisedStorage' => false,
    ];
}

/**
 * Define gateway configuration options.
 *
 * The fields you define here determine the configuration options that are
 * presented to administrator users when activating and configuring your
 * payment gateway module for use.
 *
 * @see https://developers.whmcs.com/payment-gateways/configuration/
 *
 * @return array
 */
function coingate_config()
{
    return [
        'FriendlyName' => [
            'Type' => 'System',
            'Value' => 'CoinGate Payment Gateway Module',
        ],

        'apiAuthToken' => [
            'FriendlyName' => 'Auth Token',
            'Type' => 'password',
            'Description' => 'Auth Token from CoinGate API Apps at https://coingate.com',
        ],

        'useSandboxEnv' => [
            'FriendlyName' => 'Sandbox Mode',
            'Type' => 'yesno',
            'Description' => 'Enable to use Sandbox for testing purpose. Please note, that for Sandbox you must generate separate API credentials at https://sandbox.coingate.com',
        ],

        'receiveCurrency' => [
            'FriendlyName' => 'Payout Currency',
            'Type' => 'dropdown',
            'Options' => 'BTC,USDT,ETH,LTC,EUR,USD,DO_NOT_CONVERT',
            'Description' => 'Currency you want to receive when making withdrawal at CoinGate. Please take a note what if you choose EUR or USD you will be asked to verify your business before making a withdrawal at CoinGate',
        ],

        'action_on_canceled' => [
            'FriendlyName' => 'Order Status on Canceled',
            'Type' => 'dropdown',
            'Options' => ['ignore' => 'No Action', 'canceled' => 'Canceled'],
            'Description' => 'What order status should be when buyer canceled the invoice?'
        ],

        'action_on_expired' => [
            'FriendlyName' => 'Order Status on Expired',
            'Type' => 'dropdown',
            'Options' => ['ignore' => 'No Action', 'canceled' => 'Canceled'],
            'Description' => 'What order status should be when buyer did not pay within the required time and the invoice expired?'
        ],

        'action_on_invalid' => [
            'FriendlyName' => 'Order Status on Invalid',
            'Type' => 'dropdown',
            'Options' => ['ignore' => 'No Action', 'canceled' => 'Canceled'],
            'Description' => 'What order status should be when payment rejected by the network or did not confirm?'
        ],

    ];
}

/**
 * @throws Exception
 */
function coingate_config_validate(array $params)
{
    $apiAuthToken = $params['apiAuthToken'];
    $useSandboxEnv = $params['useSandboxEnv'];

    $valid = CoinGate\Client::testConnection($apiAuthToken, $useSandboxEnv);

    if (! $valid) {
        throw new Exception('Auth Token is invalid. Get one from CoinGate API Apps at https://coingate.com');
    }
}

/**
 * Payment link.
 *
 * Required by third party payment gateway modules only.
 *
 * Defines the HTML output displayed on an invoice. Typically consists of an
 * HTML form that will take the user to the payment gateway endpoint.
 *
 * @param array $params Payment Gateway Module Parameters
 *
 * @return string
 *@see https://developers.whmcs.com/payment-gateways/third-party-gateway/
 *
 */
function coingate_link(array $params)
{
    // -------------------------------------------
    // -------------------------------------------

    // Gateway Configuration Parameters
    $apiAuthToken = $params['apiAuthToken'];
    $useSandboxEnv = $params['useSandboxEnv'];
    $receiveCurrency = $params['receiveCurrency'];

    // Invoice Parameters
    $invoiceId = $params['invoiceid'];
    $description = $params['description'];
    $amount = $params['amount'];
    $currencyCode = $params['currency'];

    // System Parameters
    $companyName = $params['companyname'];
    $systemUrl = $params['systemurl'];
    $langPayNow = $params['langpaynow'];
    $moduleName = $params['paymentmethod'];

    // -------------------------------------------
    // -------------------------------------------
    // -------------------------------------------

    $client = new CoinGate\Client($apiAuthToken, $useSandboxEnv);

    try {

        $order = $client->order->create([
            'order_id' => $invoiceId,
            'price_amount' => number_format($amount, 8, '.', ''),
            'price_currency' => $currencyCode,
            'receive_currency'  => $receiveCurrency,

            'cancel_url'        => $systemUrl . '/viewinvoice.php?id=' . $invoiceId . '&paymentfailed=1',
            'callback_url'      => $systemUrl . '/modules/gateways/callback/' . $moduleName . '.php',
            'success_url'       => $systemUrl . '/viewinvoice.php?id=' . $invoiceId . '&paysuccess=1',
            'title'             => $companyName,
            'description'       => $description
        ]);

        $htmlOutput = '<form action="' . $order->payment_url . '" method="GET">';
        $htmlOutput .= '<input type="submit" value="' . $langPayNow . '" />';
        $htmlOutput .= '</form>';

    } catch (Exception $e) {

        $htmlOutput = '<h2>' . $e->getMessage() . '</h2>';
        $htmlOutput .= '<h3>Please contact merchant for further details.</h3>';

    }

    return $htmlOutput;
}

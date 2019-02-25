<?php

require_once(dirname(__FILE__) . '/CoinGate/init.php');
require_once(dirname(__FILE__) . '/CoinGate/version.php');

function coingate_config()
{
    return array(
        'FriendlyName' => array(
            'Type' => 'System',
            'Value' => 'CoinGate'
        ),
        'ApiAuthToken' => array(
            'FriendlyName' => 'API Auth Token',
            'Description' => 'API Auth Token from CoinGate API Apps.',
            'Type' => 'text',
        ),
        'Environment' => array(
            'FriendlyName' => 'Environment',
            'Description' => 'Live (https://coingate.com) is for production and Sandbox (https://sandbox.coingate.com) is for testing purpose. Please note, that for Sandbox you must generate separate API credentials on sandbox.coingate.com. API credentials generated on coingate.com will not work for Sandbox',
            'Type' => 'dropdown',
            'Options' => 'live,sandbox',
        ),
        'ReceiveCurrency' => array(
            'FriendlyName' => 'Payout Currency',
            'Description' => 'Currency you want to receive when making withdrawal at CoinGate. Please take a note what if you choose EUR or USD you will be asked to verify your business before making a withdrawal at CoinGate.',
            'Type' => 'dropdown',
            'Options' => 'BTC,USDT,ETH,LTC,EUR,USD,DO_NOT_CONVERT',
        )
    );
}

function coingate_link($params)
{
    if (false === isset($params) || true === empty($params)) {
        die('[ERROR] In modules/gateways/coingate.php::coingate_link() function: Missing or invalid $params data.');
    }

    if (substr($params['systemurl'], -1) != "/") {
      $returnlink = $params['systemurl'] . "/";
    } else {
      $returnlink = $params['systemurl'];
    }

    $coingate_params = array(
        'order_id'          => $params['invoiceid'],
        'price_amount'      => number_format($params['amount'], 8, '.', ''),
        'price_currency'    => $params['currency'],
        'receive_currency'  => $params['ReceiveCurrency'],
        'cancel_url'        => $returnlink . 'clientarea.php',
        'callback_url'      => $returnlink . 'modules/gateways/callback/coingate.php',
        'success_url'       => $returnlink . 'viewinvoice.php?id=' . $params['invoiceid'],
        'title'             => $params['companyname'],
        'description'       => $params['description']
    );

    $authentication = array(
        'auth_token' => empty($params['ApiAuthToken']) ? $params['ApiSecret'] : $params['ApiAuthToken'],
        'environment' => $params['Environment'],
        'user_agent' => 'CoinGate - WHMCS Extension v' . COINGATE_PLUGIN_VERSION,
    );

    


    $order = \CoinGate\Merchant\Order::createOrFail($coingate_params, array(), $authentication);

    if($order->payment_url ) {

        $form = '<form action="' . $order->payment_url . '" method="GET">';
        $form .= '<input type="submit" value="' . $params['langpaynow'] . '" />';
        $form .= '</form>';
        return $form;

    } else {
        $form = '<h2>' . $order->reason . '</h2>';
        $form .= '<h3>Please contact merchant for further details</h3>';
        return $form;
    }
}

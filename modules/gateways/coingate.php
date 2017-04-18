<?php

require_once(dirname(__FILE__) . '/CoinGate/init.php');
require_once(dirname(__FILE__) . '/CoinGate/version.php');

function coingate_config() {
  return array(
    'FriendlyName' => array(
      'Type'       => 'System',
      'Value'      => 'CoinGate'
    ),
    'AppID' => array(
        'FriendlyName' => 'App ID',
        'Description'  => 'App ID from CoinGate API Apps.',
        'Type'         => 'text'
    ),
    'ApiKey' => array(
      'FriendlyName' => 'API Key',
      'Description'  => 'API Key from CoinGate API Apps.',
      'Type'         => 'text'
    ),
    'ApiSecret' => array(
      'FriendlyName' => 'API Secret',
      'Description'  => 'API Secret from CoinGate API Apps.',
      'Type'         => 'text'
    ),
    'Environment' => array(
      'FriendlyName' => 'Environment',
      'Description'  => 'Live (https://coingate.com) is for production and Sandbox (https://sandbox.coingate.com) is for testing purpose. Please note, that for Sandbox you must generate separate API credentials on sandbox.coingate.com. API credentials generated on coingate.com will not work for Sandbox',
      'Type'         => 'dropdown',
      'Options'      => 'live,sandbox',
    ),
    'ReceiveCurrency' => array(
      'FriendlyName'  => 'Receive Currency',
      'Description'   => 'Currency you want to receive when making withdrawal at CoinGate. Please take a note what if you choose EUR or USD you will be asked to verify your business before making a withdrawal at CoinGate.',
      'Type'          => 'dropdown',
      'Options'       => 'BTC,EUR,USD',
    )
  );
}

function coingate_link($params) {
  if (false === isset($params) || true === empty($params)) {
    die('[ERROR] In modules/gateways/coingate.php::coingate_link() function: Missing or invalid $params data.');
  }

  $coingate_params = array(
    'order_id'         => $params['invoiceid'],
    'price'            => number_format($params['amount'], 2, '.', ''),
    'currency'         => $params['currency'],
    'receive_currency' => $params['ReceiveCurrency'],
    'cancel_url'       => $params['systemurl'] . '/clientarea.php',
    'callback_url'     => $params['systemurl'] . '/modules/gateways/callback/coingate.php',
    'success_url'      => $params['systemurl'] . '/viewinvoice.php?id=' . $params['invoiceid'],
    'title'            => $params['companyname'],
    'description'      => $params['description']
  );

  $authentication = array(
    'app_id' => $params['AppID'],
    'api_key' => $params['ApiKey'],
    'api_secret' => $params['ApiSecret'],
    'environment' => $params['Environment'],
    'user_agent' => 'CoinGate - WHMCS Extension v'.COINGATE_PLUGIN_VERSION,
  );

  $order = \CoinGate\Merchant\Order::createOrFail($coingate_params, array(), $authentication);

  $form = '<form action="' . $order->payment_url . '" method="GET">';
  $form .= '<input type="submit" value="' . $params['langpaynow'] . '" />';
  $form .= '</form>';

  return $form;
}

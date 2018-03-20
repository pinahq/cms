<?php

namespace Pina\Modules\YandexKassa;

use Pina\Request;
use Pina\Response;
use Pina\Arr;

use Pina\Modules\Cart\PaymentMethodGateway;
use Pina\Modules\CMS\Config;

$paymentMethod = PaymentMethodGateway::instance()
	->enabled()
	->whereBy('title', 'yandex.kassa')
	->first();
if (empty($paymentMethod)) {
	return Response::notFound();
}

$paymentHandler = new PaymentHandler(Request::input('orderNumber'), 'processed');

$config = Config::getNamespace(__NAMESPACE__);

$validator = new Validator(new Logger(), $paymentHandler);
$validator->set('action', 'checkOrder');
$validator->set('shopId', $config['shopId']);
$validator->set('shopPassword', $config['shopPassword']);

$validator->set('receivedAction', Request::input('action'));
$validator->set('receivedShopId', Request::input('shopId'));
$validator->set('receivedCustomerNumber', Request::input('customerNumber'));
$validator->set('receivedOrderSumAmount', Request::input('orderSumAmount'));
$validator->set('receivedOrderSumCurrencyPaycash', Request::input('orderSumCurrencyPaycash'));
$validator->set('receivedOrderSumBankPaycash', Request::input('orderSumBankPaycash'));
$validator->set('receivedInvoiceId', Request::input('invoiceId'));
$validator->set('receivedMD5', Request::input('md5'));

header('HTTP/1.0 200');
header('Content-Type: application/xml');
echo $validator->process();
exit;

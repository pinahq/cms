<?php

namespace Pina\Modules\YandexKassa;

use Pina\Modules\Cart\PaymentGateway;
use Pina\Modules\Cart\PaymentMethodGateway;
use Pina\Modules\Cart\OrderGateway;

class PaymentHandler
{
    private $paymentId = 0;
    private $status = '';

    public function __construct($paymentId, $status)
    {
        $this->paymentId = $paymentId;
        $this->status = $status;
    }

    public function get()
    {
        $payment = PaymentGateway::instance()
            ->innerJoin(
                PaymentMethodGateway::instance()
                    ->on('id', 'payment_method_id')
                    ->enabled()
                    ->whereBy('title', 'yandex.kassa')
            )
            ->innerJoin(OrderGateway::instance()->on('id', 'order_id')->selectAs('email', 'customerNumber'))
            ->whereId($this->paymentId)
            ->whereBy('status', $this->status)
            ->selectAs('total', 'orderSumAmount')
            ->first();

        if (empty($payment)) {
            return ['customerNumber' => '', 'orderSumAmount' => ''];
        }

        return $payment;
    }

    public function processed()
    {
        $this->updateStatus('processed');
    }

    public function failed()
    {
        $this->updateStatus('failed');
    }

    public function payed()
    {
        $this->updateStatus('payed');
    }

    public function canceled()
    {
        $this->updateStatus('canceled');
    }

    private function updateStatus($status)
    {
        PaymentGateway::instance()
            ->whereId($this->paymentId)
            ->update(['status' => $status]);
    }
}

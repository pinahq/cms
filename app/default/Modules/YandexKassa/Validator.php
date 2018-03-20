<?php

namespace Pina\Modules\YandexKassa;

class Validator
{
    private $logger = null;
    private $paymentHandler = null;

    private $action = '';
    private $shopId = '';
    private $shopPassword = '';
    private $customerNumber = '';
    private $orderSumAmount = '';

    private $receivedPerformedDatetime = '';
    private $receivedAction = '';
    private $receivedShopId = '';
    private $receivedCustomerNumber = '';
    private $receivedOrderSumAmount = '';
    private $receivedOrderSumCurrencyPaycash = '';
    private $receivedOrderSumBankPaycash = '';
    private $receivedInvoiceId = '';
    private $receivedMD5 = '';

    public $date = '';

    private $error = false;

    public function __construct($logger, $paymentHandler)
    {
        $this->logger = $logger;
        $this->paymentHandler = $paymentHandler;

        $payment = $this->paymentHandler->get();
        $this->customerNumber = $payment['customerNumber'];
        $this->orderSumAmount = $payment['orderSumAmount'];

        $this->date = date('c');
    }

    public function __set($name, $value)
    {
        $this->$name = $value;
    }

    public function process()
    {
        $this->logger->log("Start $this->action");
        $this->logger->log("Request: ". json_encode([
            'receivedPerformedDatetime' => $this->receivedPerformedDatetime,
            'receivedAction' => $this->receivedAction,
            'receivedShopId' => $this->receivedShopId,
            'receivedCustomerNumber' => $this->receivedCustomerNumber,
            'receivedOrderSumAmount' => $this->receivedOrderSumAmount,
            'receivedOrderSumCurrencyPaycash' => $this->receivedOrderSumCurrencyPaycash,
            'receivedOrderSumBankPaycash' => $this->receivedOrderSumBankPaycash,
            'receivedInvoiceId' => $this->receivedInvoiceId,
            'receivedMD5' => $this->receivedMD5
        ]));
        $this->logger->log("Shop params: ". json_encode([
            'action' => $this->action,
            'shopId' => $this->shopId,
            'customerNumber' => $this->customerNumber,
            'orderSumAmount' => $this->orderSumAmount
        ]));

        if (!$this->isValidateSign()) {
            $this->paymentHandler->failed();
            $mes = "Значение параметра md5 не совпадает с результатом расчета хэш-функции";

            $response = $this->createResponse(Status::AUTH_ERROR, $mes);
        } else {
            if ($this->action == 'checkOrder') {
                $this->paymentHandler->processed();
            }

            if ($this->action == 'paymentAviso') {
                $this->paymentHandler->payed();
            }

            $response = $this->createResponse(Status::SUCCESS);
        }

        $this->logger->log("Response :". $response);
        return $response;
    }

    public function getHash()
    {
        $data = [
            $this->action,
            $this->orderSumAmount,
            $this->receivedOrderSumCurrencyPaycash,
            $this->receivedOrderSumBankPaycash,
            $this->shopId,
            $this->receivedInvoiceId,
            $this->customerNumber,
            $this->shopPassword
        ];
        
        $str = implode($data, ';');
        return strtoupper(md5($str));
    }

    private function createResponse($code, $message = '')
    {
        return '<?xml version="1.0" encoding="UTF-8"?><'. 
            $this->action .'Response performedDatetime="'. $this->date  
            .'" code="'. $code .'"'. (!empty($message) ? ' message="'. $message .'"' : "") 
            .' invoiceId="'. $this->receivedInvoiceId .'" shopId="'. $this->shopId .'"/>';
    }

    private function isValidateSign()
    {
        $md5 = $this->getHash();
        if ($md5 == $this->receivedMD5) {
            return true;
        }

        $this->logger->log("Wait for md5: $md5, recieved md5: $this->receivedMD5");
        return false;
    }
}

<?php

namespace Pina\Modules\PayPal;

use Pina\App;
use Pina\Modules\Cart\OrderGateway;
use Pina\Modules\Cart\PaymentGateway;
use Pina\Modules\Cart\PaymentMethodGateway;
use Pina\Modules\CMS\Config;
use Pina\Modules\Images\ImageTag;
use PayPal\CoreComponentTypes\BasicAmountType;
use PayPal\EBLBaseComponents\AddressType;
use PayPal\EBLBaseComponents\BillingAgreementDetailsType;
use PayPal\EBLBaseComponents\PaymentDetailsItemType;
use PayPal\EBLBaseComponents\PaymentDetailsType;
use PayPal\EBLBaseComponents\SetExpressCheckoutRequestDetailsType;
use PayPal\PayPalAPI\SetExpressCheckoutReq;
use PayPal\PayPalAPI\SetExpressCheckoutRequestType;
use PayPal\Service\PayPalAPIInterfaceServiceService;
use PayPal\EBLBaseComponents\DoExpressCheckoutPaymentRequestDetailsType;
use PayPal\PayPalAPI\DoExpressCheckoutPaymentReq;
use PayPal\PayPalAPI\DoExpressCheckoutPaymentRequestType;


class RequestBuilder
{

    private $payment = null;
    private $config = null;

    public function __construct($payment)
    {
        $this->payment = $payment;
        $this->config = Config::getNamespace(__NAMESPACE__);
    }

    public function hasConfig()
    {
        return !empty($this->config);
    }

    public function getDoRequestDetails($payerId, $token)
    {

        $DoECRequestDetails = new DoExpressCheckoutPaymentRequestDetailsType();
        $DoECRequestDetails->PayerID = $payerId;
        $DoECRequestDetails->Token = $token;
        $DoECRequestDetails->PaymentDetails[0] = $this->getShortPaymentDetails();
        $DoECRequest = new DoExpressCheckoutPaymentRequestType();
        $DoECRequest->DoExpressCheckoutPaymentRequestDetails = $DoECRequestDetails;
        $DoECReq = new DoExpressCheckoutPaymentReq();
        $DoECReq->DoExpressCheckoutPaymentRequest = $DoECRequest;
        
        return $DoECReq;
    }

    public function getInitRequestDetails()
    {

        $setECReqDetails = new SetExpressCheckoutRequestDetailsType();
        $setECReqDetails->PaymentDetails[0] = $this->getPaymentDetails();

        $setECReqDetails->CancelURL = $this->getCancelUrl();
        $setECReqDetails->ReturnURL = $this->getReturnUrl();

        /*
         * Determines where or not PayPal displays shipping address fields on the PayPal pages. For digital goods, this field is required, and you must set it to 1. It is one of the following values:
          0 – PayPal displays the shipping address on the PayPal pages.
          1 – PayPal does not display shipping address fields whatsoever.
          2 – If you do not pass the shipping address, PayPal obtains it from the buyer's account profile.
         */
        $setECReqDetails->NoShipping = 1;
        /*
         *  (Optional) Determines whether or not the PayPal pages should display the shipping address set by you in this SetExpressCheckout request, not the shipping address on file with PayPal for this buyer. Displaying the PayPal street address on file does not allow the buyer to edit that address. It is one of the following values:
          0 – The PayPal pages should not display the shipping address.
          1 – The PayPal pages should display the shipping address.
         */
        $setECReqDetails->AddressOverride = 0;

        /*
         * Indicates whether or not you require the buyer's shipping address on file with PayPal be a confirmed address. For digital goods, this field is required, and you must set it to 0. It is one of the following values:
          0 – You do not require the buyer's shipping address be a confirmed address.
          1 – You require the buyer's shipping address be a confirmed address.
         */
        $setECReqDetails->ReqConfirmShipping = 0;

        // Billing agreement details
        #$billingAgreementDetails = new BillingAgreementDetailsType($_REQUEST['billingType']);
        #$billingAgreementDetails->BillingAgreementDescription = $_REQUEST['billingAgreementText'];
        #$setECReqDetails->BillingAgreementDetails = array($billingAgreementDetails);
        // Display options
        /*
          $setECReqDetails->cppheaderimage = $_REQUEST['cppheaderimage'];
          $setECReqDetails->cppheaderbordercolor = $_REQUEST['cppheaderbordercolor'];
          $setECReqDetails->cppheaderbackcolor = $_REQUEST['cppheaderbackcolor'];
          $setECReqDetails->cpppayflowcolor = $_REQUEST['cpppayflowcolor'];
          $setECReqDetails->cppcartbordercolor = $_REQUEST['cppcartbordercolor'];
          $setECReqDetails->cpplogoimage = $_REQUEST['cpplogoimage'];
          $setECReqDetails->PageStyle = $_REQUEST['pageStyle'];
         */
        $cmsConfig = Config::getNamespace('Pina\\Modules\\CMS');
        $logo = new ImageTag(['id' => $cmsConfig['logo'], 'return' => 'src']);
        #$setECReqDetails->cpplogoimage = $logo->render();
        $setECReqDetails->BrandName = $cmsConfig['company_title'];
        $setECReqDetails->InvoiceID = $this->payment['id'];

        // Advanced options
        /*
          $setECReqDetails->AllowNote = $_REQUEST['allowNote'];
         */
        $setECReqType = new SetExpressCheckoutRequestType();
        $setECReqType->SetExpressCheckoutRequestDetails = $setECReqDetails;
        $setECReq = new SetExpressCheckoutReq();
        $setECReq->SetExpressCheckoutRequest = $setECReqType;

        return $setECReq;
    }
    
    private function getShortPaymentDetails()
    {
        // details about payment
        $paymentDetails = new PaymentDetailsType();

        $paymentDetails->OrderTotal = $this->getPaymentAmount();
        $paymentDetails->PaymentAction = 'Sale';
        $paymentDetails->NotifyURL = $this->getNotifyUrl();
        
        return $paymentDetails;
    }

    private function getPaymentDetails()
    {

        // details about payment
        $paymentDetails = new PaymentDetailsType();

        $itemDetails = new PaymentDetailsItemType();
        $itemDetails->Name = $this->getPaymentTitle();
        $itemDetails->Amount = $this->getPaymentAmount();
        $itemDetails->Quantity = 1;
        /*
         * Indicates whether an item is digital or physical. For digital goods, this field is required and must be set to Digital. It is one of the following values:
          Digital
          Physical
         */
        #$itemDetails->ItemCategory = 'Physical';
        $itemDetails->Tax = $this->getAmount(0);

        $paymentDetails->PaymentDetailsItem[] = $itemDetails;

        $paymentDetails->ShipToAddress = $this->getAddress();
        print_r($paymentDetails->ShipToAddress);
        $paymentDetails->ItemTotal = $this->getPaymentAmount();
        $paymentDetails->TaxTotal = $this->getAmount(0);
        $paymentDetails->OrderTotal = $this->getPaymentAmount();

        $paymentDetails->PaymentAction = 'Sale';
        $paymentDetails->HandlingTotal = $this->getAmount(0);
        $paymentDetails->InsuranceTotal = $this->getAmount(0);
        $paymentDetails->ShippingTotal = $this->getAmount(0);

        $paymentDetails->NotifyURL = $this->getNotifyUrl();
        
        return $paymentDetails;
    }

    private function getPaymentTitle()
    {
        return implode(' ', [$this->config['description'], '(Order #' . $this->payment['order_id'] . ')']);
    }

    private function getPaymentAmount()
    {
        return $this->getAmount($this->payment['total']);
    }

    private function getAmount($amount)
    {
        return new BasicAmountType($this->config['currency'], $amount);
    }

    private function getAddress()
    {
        $address = new AddressType();
        $address->CityName = $this->payment['city'];
        $address->Name = implode(' ', array_filter([$this->payment['firstname'], $this->payment['middlename'], $this->payment['lastname']]));
        $address->Street1 = $this->payment['street'];
        $address->StateOrProvince = $this->payment['region'];
        $address->PostalCode = $this->payment['zip'];
        $address->Country = $this->payment['country_key'];
        $address->Phone = $this->payment['phone'];

        return $address;
    }

    private function getReturnUrl()
    {
        return App::link('paypal/:id/do', ['id' => $this->payment['id']]);
    }

    private function getNotifyUrl()
    {
        return App::link('paypal/:id/notify', ['id' => $this->payment['id']]);
    }

    private function getCancelUrl()
    {
        return App::link('paypal/:id/cancel', ['id' => $this->payment['id']]);
    }
    
    public function getUrl($token)
    {
        return 'https://www'.($this->config['mode']=='sandbox'?'.sandbox':'').'.paypal.com/webscr?cmd=_express-checkout&token=' . $token;
    }

    public function getConfig()
    {
        $config = array(
            // values: 'sandbox' for testing
            //		   'live' for production
            //         'tls' for testing if your server supports TLSv1.2
            "mode" => $this->config['mode'],
            // TLSv1.2 Check: Comment the above line, and switch the mode to tls as shown below
            // "mode" => "tls"
            'log.LogEnabled' => true,
            'log.FileName' => App::path() . '/../var/log/paypal.log',
            'log.LogLevel' => 'FINE'

            // These values are defaulted in SDK. If you want to override default values, uncomment it and add your value.
            // "http.ConnectionTimeOut" => "5000",
            // "http.Retry" => "2",
        );
        return $config;
    }

    // Creates a configuration array containing credentials and other required configuration parameters.
    public function getAcctAndConfig()
    {
        $config = array(
            // Signature Credential
            "acct1.UserName" => $this->config['acct1.UserName'],
            "acct1.Password" => $this->config['acct1.Password'],
            "acct1.Signature" => $this->config['acct1.Signature'],
            // Subject is optional and is required only in case of third party authorization
            // "acct1.Subject" => "",
            // Sample Certificate Credential
            // "acct1.UserName" => "certuser_biz_api1.paypal.com",
            // "acct1.Password" => "D6JNKKULHN3G5B8A",
            // Certificate path relative to config folder or absolute path in file system
            // "acct1.CertPath" => "cert_key.pem",
            // Subject is optional and is required only in case of third party authorization
            // "acct1.Subject" => "",
        );

        return array_merge($config, $this->getConfig());
    }

}

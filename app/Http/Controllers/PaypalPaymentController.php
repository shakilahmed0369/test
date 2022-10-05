<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PayPal\Api\Amount;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use PayPal\Rest\ApiContext;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Api\PaymentExecution;



class PaypalPaymentController extends Controller
{
    private $apiContext;
    public function __construct()
    {
        $this->apiContext = new ApiContext(
            new OAuthTokenCredential(
              'AQAFuKjnJEvLTK_P4imXKcE9I505nrmqHroVyyfs6yXBY7PCBneXLed1L-7pIPuls07R2vGhkAGdqjg_',
              'EGtdCDtmXZ2eUReFSl5VDePDkitHDDoZwVv9F7O1G3UKo3VLiaW429Bp6OCxqWHOHT1epClWE9_YBNTt'
            )
        );

        $setting = array(
            'mode' => 'sandbox',
            'http.ConnectionTimeOut' => 30,
            'log.LogEnabled' => true,
            'log.FileName' => storage_path() . '/logs/paypal.log',
            'log.LogLevel' => 'ERROR'
        );
        $this->apiContext->setConfig($setting);
    }

    public function paypal_pay(Request $request)
    {

        // set payer
        $payer = new Payer();
        $payer->setPaymentMethod("paypal");
        // set amount total
        $amount = new Amount();
        $amount->setCurrency('USD')
            ->setTotal(200);
        // transaction
        $transaction = new Transaction();
        $transaction->setAmount($amount)
            ->setDescription('This is a test payment');
        // redirect url
        $redirectUrls = new RedirectUrls();

        $redirectUrls->setReturnUrl( route('payment.success'))
            ->setCancelUrl(route('paypal-payment-cancled'));
        // payment
        $payment = new Payment();
        $payment->setIntent("sale")
            ->setPayer($payer)
            ->setRedirectUrls($redirectUrls)
            ->setTransactions(array($transaction));
        try {
            $payment->create($this->apiContext);
        } catch (\PayPal\Exception\PPConnectionException $ex) {

        }

        // get paymentlink
        $approvalUrl = $payment->getApprovalLink();

        return redirect($approvalUrl);
    }

    public function paypal_success(Request $request)
    {


        $payment_id = $request->get('paymentId');
        $payment = Payment::get($payment_id, $this->apiContext);
        $execution = new PaymentExecution();
        $execution->setPayerId($request->get('PayerID'));
        /**Execute the payment **/
        $result = $payment->execute($execution, $this->apiContext);

        dd($result);


    }

    public function paypal_cancel()
    {
        toast(trans('frontend.Payment Cancelled!'), 'warning')->width('350px');
        $notification = ['message' => trans('frontend.Payment Cancelled'), 'alert-type' => 'error'];
        return  redirect()->back()->with($notification);
    }

}

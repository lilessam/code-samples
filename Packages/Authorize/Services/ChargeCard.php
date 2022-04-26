<?php

namespace App\Authorize\Services;

use App\Authorize\Contracts\Service;
use net\authorize\api\controller as AnetController;
use net\authorize\api\contract\v1 as AnetAPI;

class ChargeCard implements Service
{
    /**
     * Execute the service.
     *
     * @return void
     */
    public function execute(...$args)
    {
        $refId = 'ref' . time();
        // Create the payment data for a credit card
        $creditCard = new AnetAPI\CreditCardType();
        $creditCard->setCardNumber($args[1]['card']);

        $expiry = $args[1]['year'] . '-' . $args[1]['month'];
        $creditCard->setExpirationDate($expiry);
        $paymentOne = new AnetAPI\PaymentType();
        $paymentOne->setCreditCard($creditCard);
        // Create a transaction
        $transactionRequestType = new AnetAPI\TransactionRequestType();
        $transactionRequestType->setTransactionType('authCaptureTransaction');
        $transactionRequestType->setAmount($args[1]['amount']);
        $transactionRequestType->setPayment($paymentOne);
        $request = new AnetAPI\CreateTransactionRequest();
        $request->setMerchantAuthentication($args[0]);
        $request->setRefId($refId);
        $request->setTransactionRequest($transactionRequestType);
        $controller = new AnetController\CreateTransactionController($request);
        $response = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::SANDBOX);
        if ($response != null) {
            if ($response->getMessages()->getResultCode() == 'Ok') {
                $tresponse = $response->getTransactionResponse();

                if ($tresponse != null && $tresponse->getMessages() != null) {
                    return $tresponse->getTransId();
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}

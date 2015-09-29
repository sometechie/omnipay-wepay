<?php

namespace Omnipay\WePay\Message;

/**
 * WePay Purchase Request
 */

class PurchaseRequest extends AbstractRequest
{

    public function getData()
    {
        $this->validate('transactionId', 'amount', 'currency', 'type', 'description', 'accountId');

        $data               = array();
        $data['account_id'] = $this->getAccountId();

        // unique_id must be used with a preapproval or a tokenized credit card
        // $data['unique_id']                       = $this->getTransactionId();

        $data['reference_id']                    = $this->getTransactionId();
        $data['amount']                          = $this->getAmount();
        $data['type']                            = $this->getType();
        $data['currency']                        = $this->getCurrency();
        $data['short_description']               = $this->getDescription();
        $data['long_description']                = $this->getDescription();
        $data['fee']                             = array("fee_payer" => $this->getFeePayer());
        $data['hosted_checkout']                 = array();
        $data['hosted_checkout']['redirect_uri'] = $this->getReturnUrl();

        if ($this->getCard()) {
            $data['hosted_checkout']['prefill_info'] = array(
                'name'  => $this->getCard()->getName(),
                'email' => $this->getCard()->getEmail(),
            );
        }

        return $data;
    }

    public function sendData($data)
    {

        try {

            $response = $this->httpClient->post($this->getEndpoint(), $this->getApiHeader(), json_encode($data))->send();

            return new PurchaseResponse($this, $response->json());
        }
        catch (\Exception $e) {
            $response = $e->getResponse();
            return new PurchaseResponse($this, $response->json());
        }
    }
}
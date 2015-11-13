<?php

namespace Qhp\AmazonPayments;

class AmazonPayments
{
    public $client;

    public function __construct(AmazonPaymentsClient $client)
    {
        $this->client = $client;
    }

    /**
     * Sets order reference details such as the order total and a description for the order.
     *
     * @param array $params
     * @return array
     */
    public function SetOrderReferenceDetails(array $params)
    {
        $params = [
            'AmazonOrderReferenceId' => $params['orderReferenceId'],
            'OrderReferenceAttributes.OrderTotal.Amount' => $params['amount'],
            'OrderReferenceAttributes.OrderTotal.CurrencyCode' => isset($params['currencyCode']) ? $params['currencyCode'] : 'USD',
            'OrderReferenceAttributes.SellerOrderAttributes.SellerOrderId' => isset($params['orderId']) ? $params['orderId'] : '',
            'OrderReferenceAttributes.SellerOrderAttributes.StoreName' => isset($params['storeName']) ? $params['storeName'] : '',
            'AuthorizationReferenceId' => uniqid(),
            'SellerAuthorizationNote' => isset($params['authNote']) ? $params['authNote'] : '',
        ];

        return $this->client->request('SetOrderReferenceDetails', $params);
    }

    /**
     * Returns details about the Order Reference object and its current state.
     *
     * @param array $params
     * @return array
     */
    public function GetOrderReferenceDetails(array $params)
    {
        $params = [
            'AmazonOrderReferenceId' => $params['referenceId'],
            'AddressConsentToken' => $params['token']
        ];

        return $this->client->request('GetOrderReferenceDetails', $params);
    }

    /**
     * Confirms that the order reference is free of constraints and all required information has been set on the order reference.
     *
     * @param array $params
     * @return array
     */
    public function ConfirmOrderReference(array $params)
    {
        return $this->client->request('ConfirmOrderReference', ['AmazonOrderReferenceId'=>$params['referenceId']]);
    }

    /**
     * Reserves a specified amount against the payment methods stored in the order reference.
     *
     * @param array $params
     * @return array
     */
    public function authorize(array $params)
    {
        $params = [
            'AmazonOrderReferenceId' => $params['referenceId'],
            'AuthorizationAmount.Amount' => $params['amount'],
            'AuthorizationAmount.CurrencyCode' => isset($params['currencyCode']) ? $params['currencyCode'] : 'USD',
            'AuthorizationReferenceId' => uniqid('AUTHORIZE-')
        ];

        return $this->client->request('Authorize', $params);
    }

    /**
     * Captures funds from an authorized payment instrument.
     *
     * @param array $params
     * @return array
     */
    public function capture(array $params)
    {
        $params = [
            'AmazonAuthorizationId' => $params['authorizationId'],
            'CaptureAmount.Amount' => $params['amount'],
            'CaptureAmount.CurrencyCode' => isset($params['currencyCode']) ? $params['currencyCode'] : 'USD',
            'CaptureReferenceId' => uniqid('CAPTURE-')
        ];

        return $this->client->request('Authorize', $params);
    }
}
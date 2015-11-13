<?php

namespace Qhp\AmazonPayments;

use GuzzleHttp\Client;

class AmazonPaymentsClient
{
    protected $serviceUrl;
    protected $sellerId;
    protected $clientId;
    protected $accessKey;
    protected $secretKey;

    public function __construct($config)
    {
        $this->serviceUrl = $config['isSandbox'] ? 'https://mws.amazonservices.com/OffAmazonPayments_Sandbox/2013-01-01' : 'https://mws.amazonservices.com/OffAmazonPayments/2013-01-01';
        $this->sellerId   = $config['sellerId'];
        $this->clientId   = $config['clientId'];
        $this->accessKey  = $config['accessKey'];
        $this->secretKey  = $config['secretKey'];
    }

    /**
     * Make request to Amazon
     *
     * @param $action
     * @param $params
     * @return mixed
     */
    public function request($action, $params)
    {
        $basicParams = [
            'AWSAccessKeyId' => $this->accessKey,
            'SellerId' => $this->sellerId,
            'ClientId' => $this->clientId,
            'SignatureMethod' => 'HmacSHA256',
            'SignatureVersion' => 2,
            'Timestamp' => gmdate("Y-m-d\TH:i:s.\\0\\0\\0\\Z", time()),
            'TransactionTimeout' => 60,
            'Version' => '2013-01-01',
            'Action' => $action
        ];

        $params = array_merge($basicParams, $params);
        $params['Signature'] = $this->getSignature($params);
        $client = new Client;

        $response = $client->post($this->serviceUrl, ['exceptions' => false, 'body' => $params]);
        $response = $response->xml();

        return json_decode(json_encode($response), true);
    }

    private function getSignature(array $params)
    {
        $data = 'POST';
        $data .= "\n";
        $endpoint = parse_url($this->serviceUrl);
        $data .= $endpoint['host'];
        $data .= "\n";
        $uri = isset($endpoint['path']) ? $endpoint['path'] : '/';
        $uriEncoded = implode("/", array_map('urlencode', explode("/", $uri)));
        $data .= $uriEncoded;
        $data .= "\n";
        uksort($params, 'strcmp');
        $data .= http_build_query($params);

        return base64_encode(
            hash_hmac('sha256', $data, $this->secretKey, true)
        );
    }
}
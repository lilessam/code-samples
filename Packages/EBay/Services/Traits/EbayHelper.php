<?php

namespace App\Ebay\Services\Traits;

trait EbayHelper
{
    /**
     * Send ebay request and get the XML response.
     *
     * @param string $requestBody
     * @return array
     */
    public function eBaySend($requestBody)
    {
        //build eBay headers using variables passed via constructor
        $headers = [
            //Regulates versioning of the XML interface for the API
            'X-EBAY-API-COMPATIBILITY-LEVEL: ' . $this->_eBayApiVersion,
            //set the keys
            'X-EBAY-API-DEV-NAME: ' . config('ebay.authentication.credentials.devId'),
            'X-EBAY-API-APP-NAME: ' . config('ebay.authentication.credentials.appId'),
            'X-EBAY-API-CERT-NAME: ' . config('ebay.authentication.credentials.certId'),
            //the name of the call we are requesting
            'X-EBAY-API-CALL-NAME: ' . $this->_call,
            //SiteID must also be set in the Request's XML
            //SiteID = 0  (US) - UK = 3, Canada = 2, Australia = 15, ....
            //SiteID Indicates the eBay site to associate the call with
            'X-EBAY-API-SITEID: ' . 3,
        ];

        //initialise a CURL session
        $connection = curl_init();
        //set the server we are using (could be Sandbox or Production server)
        curl_setopt($connection, CURLOPT_URL, config('ebay.authentication.ServerUrl'));

        //stop CURL from verifying the peer's certificate
        curl_setopt($connection, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($connection, CURLOPT_SSL_VERIFYHOST, 0);

        //set the headers using the array of headers
        curl_setopt($connection, CURLOPT_HTTPHEADER, $headers);

        //set method as POST
        curl_setopt($connection, CURLOPT_POST, 1);

        //set the XML body of the request
        curl_setopt($connection, CURLOPT_POSTFIELDS, $requestBody);

        //set it to return the transfer as a string from curl_exec
        curl_setopt($connection, CURLOPT_RETURNTRANSFER, 1);

        //Send the Request
        $response = curl_exec($connection);

        //close the connection
        curl_close($connection);

        //return the response
        $response = str_replace('&', ' &amp; ', $response);

        return  $this->xmlToArray($response);
    }

    /**
     * Convert XML to php array.
     *
     * @param string $xml
     * @return array mixed
     */
    private function xmlToArray($xml)
    {
        $xml = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
        $json = json_encode($xml);

        return json_decode($json, true);
    }
}

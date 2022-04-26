<?php

namespace App\Flipkart\Services;

use App\Flipkart\Contracts\Service;

class GetOrders implements Service
{
    protected $token;

    protected $results;

    /**
     * Execute the service.
     *
     * @return void
     */
    public function execute(...$args)
    {
        $this->token = $args[0]['access_token'];

        $url = 'https://api.flipkart.net/sellers/v2/orders/search';
        $curl = curl_init();
        $searchData = [
            'filter' => ['orderDate' => ['fromDate' => '2013-08-05T11:26:53.827Z', 'toDate' => '2119-09-06T11:26:53.827Z']]
        ];
        $searchData = json_encode($searchData);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $searchData);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);//try to make it as true. making ssl verifyer as false will lead to secruty issues
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Content-Type:application/json',
            'Authorization:Bearer ' . $this->token,
        ]);

        $response = curl_exec($curl);
        curl_close($curl);

        $json_decode = json_decode($response);

        foreach ($json_decode->orderItems as $order) {
            $this->results[] = $order;
        }

        if (isset($json_decode->nextPageUrl)) {
            $this->getMoreResults($json_decode->nextPageUrl);
        }

        return collect($this->results);
    }

    /**
     * Get more pages' results.
     *
     * @param string $nextPageUrl
     * @return void
     */
    public function getMoreResults($nextPageUrl)
    {
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://api.flipkart.net/sellers/v2' . $nextPageUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_POSTFIELDS => '',
            CURLOPT_HTTPHEADER => [
                'Accept: */*',
                'Authorization: Bearer ' . $this->token,
                'Cache-Control: no-cache',
                'Connection: keep-alive',
                'Host: api.flipkart.net',
                'accept-encoding: gzip, deflate',
                'cache-control: no-cache'
            ],
        ]);

        $response = curl_exec($curl);

        $results = json_decode($response);

        foreach ($results->orderItems as $order) {
            $this->results[] = $order;
        }

        if (isset($results->nextPageUrl)) {
            $this->getMoreResults($results->nextPageUrl);
        }
    }
}

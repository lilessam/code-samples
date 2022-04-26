<?php

namespace App\Kashoo\Lib;
define('DEBUG', false);

class Kashoo
{
    private static $base_url = 'https://api.kashoo.com/api';
    public $api_token;
    public $email;
    public $password;
    public $businessId;

    public function __construct($id = null, $password = null)
    {
        if (is_null($id)) {
            return;
        } else {
            if (!is_null($password)) {
                $this->$email = $id;
                $this->password = $password;
                $this->getApiToken();
            } else {
                $this->api_token = $id;
            }
        }
        $this->businessId = null;
    }

    public function createApiToken($email = null, $password = null)
    {
        $this->email = $email;
        $this->password = $password;
        $this->api_token = $response = $this->httpRequest(
            '/authTokens',
            [
                'email' => $this->email,
                'password' => $this->password
            ]
        );

        return $this->api_token;
    }

    public function listBusinesses()
    {
        $businesses = $this->httpRequest('/users/me/businesses', null, 'get', 'json');

        return $businesses;
    }

    public function createInvoice($invoice, $businessId = null)
    {
        if (is_null($businessId)) {
            $businessId = $this->businessId;
        }
        $response = $this->httpRequest(
            sprintf(
                '/businesses/%s/records/invoices',
                $businessId
            ),
            $invoice,
            'post',
            'json'
        );

        if($response)
        {
            return $response;
        }
        else
        {
            return false;
        }

    }

    public function createAccount($account, $businessId = null)
    {
        if (is_null($businessId)) {
            $businessId = $this->businessId;
        }
        $this->httpRequest(
            sprintf(
                '/businesses/%s/accounts',
                $businessId
            ),
            $account,
            'post',
            'json'
        );

        return true;
    }

    public function createBill($bill, $businessId = null)
    {
        if (is_null($businessId)) {
            $businessId = $this->businessId;
        }
        $this->httpRequest(
            sprintf(
                '/businesses/%s/records/bills',
                $businessId
            ),
            $bill,
            'post',
            'json'
        );

        return true;
    }

    public function createBillPayment($bill, $businessId = null)
    {
        if (is_null($businessId)) {
            $businessId = $this->businessId;
        }
        $this->httpRequest(
            sprintf(
                '/businesses/%s/records/billPayments',
                $businessId
            ),
            $bill,
            'post',
            'json'
        );

        return true;
    }

    public function createInvoicePayment($invoice, $businessId = null)
    {
        if (is_null($businessId)) {
            $businessId = $this->businessId;
        }

        $invoice_payment = $this->httpRequest(
            sprintf(
                '/businesses/%s/records/invoicePayments',
                $businessId
            ),
            $invoice,
            'post',
            'json'
        );

        if($invoice_payment)
        {
            return $invoice_payment;
            
        }
        else
        {
            return false;
        }

    }

    public function listRecords($type = null, $startDate = null, $endDate = null, $businessId = null, $limit = 100, $offset = 0)
    {
        // Clean up parameters
        $businessId = is_null($businessId) ? $this->businessId : $businessId;
        $startDate = is_null($startDate) ? date('Y-m-d', 0) : $startDate;
        $startDate = is_int($startDate) ? date('Y-m-d', $startDate) : $startDate;
        $endDate = is_null($endDate) ? date('Y-m-d') : $endDate;
        $endDate = is_int($endDate) ? date('Y-m-d', $endDate) : $endDate;
        $records = $this->httpRequest(
            sprintf(
                '/businesses/%s/records/%s',
                $businessId,
                $type
            ),
            [
                'startDate' => $startDate,
                'endDate' => $endDate,
                'limit' => $limit,
                'offset' => $offset,
            ],
            'get',
            'json'
        );

        return $records;
    }

    public function listInvoices($type = null, $startDate = null, $endDate = null, $businessId = null, $limit = 100, $offset = 0)
    {
        return $this->listRecords('invoices', $startDate, $endDate, $businessId, $limit, $offset);
    }

    public function listBills($type = null, $startDate = null, $endDate = null, $businessId = null, $limit = 100, $offset = 0)
    {
        return $this->listRecords('bills', $startDate, $endDate, $businessId, $limit, $offset);
    }

    public function listBillPayments($type = null, $startDate = null, $endDate = null, $businessId = null, $limit = 100, $offset = 0)
    {
        return $this->listRecords('billPayments', $startDate, $endDate, $businessId, $limit, $offset);
    }

    public function listInvoicePayments($type = null, $startDate = null, $endDate = null, $businessId = null, $limit = 100, $offset = 0)
    {
        return $this->listRecords('invoicePayments', $startDate, $endDate, $businessId, $limit, $offset);
    }

    public function listContacts($type = '', $businessId = null, $limit = 100)
    {
        $businessId = is_null($businessId) ? $this->businessId : $businessId;
        $sync = $this->httpRequest(
            '/sync',
            [
                'business' => $businessId,
                'description' => 'temporary sync session to request contacts',
            ],
            'post',
            'json',
            null,
            'raw'
        );
        $contacts = $this->httpRequest(
            sprintf(
                '/sync/%s/contacts/%s',
                $sync['id'],
                $type
            ),
            [
                'limit' => 100,
            ],
            'get',
            'json'
        );

        return $contacts;
    }

    public function listVendors($businessId = null, $limit = 100)
    {
        return $this->listContacts('vendors', $businessId, $limit);
    }

    public function listCustomers($businessId = null, $limit = 100)
    {
        return $this->listContacts('customers', $businessId, $limit);
    }

    public function listAccounts($type = null, $businessId = null, $limit = 100, $offset = 0)
    {
        // Clean up parameters
        $businessId = is_null($businessId) ? $this->businessId : $businessId;
        $invoices = $this->httpRequest(
            sprintf(
                '/businesses/%s/accounts',
                $businessId
            ),
            [
                'type' => $type,
                'limit' => $limit,
                'offset' => $offset,
            ],
            'get',
            'json'
        );

        return $invoices;
    }

    private function oldSchoolHttpRequest($end_point, $data = null, $method = 'post', $response_format = 'raw', $alt_url = '')
    {
        if (is_array($data)) {
            $data = http_build_query($data);
        }
        // Setup URL
        $url = $alt_url ? $alt_url : self::$base_url;
        $url .= $end_point;
        if ($method == 'get') {
            if (is_string($data)) {
                $url .= '?' . $data;
            }
        }
        // Setup header
        $http_header = '';
        if ($response_format != 'raw') {
            $http_header .= 'Accept: application/' . $response_format;
            if ($method == 'post') {
                $http_header .= 'Content-Type: application/' . $response_format;
                $http_header .= 'Content-Length: ' . strlen($data);
            }
        } else {
            $http_header .= 'Content-Type: multipart/form-data';
        }
        if ($this->api_token) {
            $http_header .= "Authorization: TOKEN uuid:{$this->api_token}";
        }
        // Send request
        $result = file_get_contents($url, null, stream_context_create(
            [
                'http' => [
                    'method' => strtoupper($method),
                    'header' => $http_header,
                    'content' => $data,
                ],
            ]
        ));
        print_r(
            [
                'http' => [
                    'method' => strtoupper($method),
                    'header' => $http_header,
                    'content' => $data,
                ],
            ]
        );
        echo $result;
        die();
    }

    private function httpRequest($end_point, $data = null, $method = 'post', $response_format = 'raw', $alt_url = '', $request_format = null)
    {
        $curl = curl_init();
        $url = $alt_url ? $alt_url : self::$base_url;
        $url .= $end_point;
        $request_format = is_null($request_format) ? $response_format : $request_format;
        // Add options based on on HTTP method
        switch ($method) {
            case 'post':
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                break;
            case 'get':
                if (is_array($data)) {
                    $data = http_build_query($data);
                }
                if (is_string($data)) {
                    $url .= '?' . $data;
                }
                break;
        }
        // Setup header
        $http_header = [];
        if ($response_format != 'raw') {
            $http_header[] = 'Accept: application/' . $response_format;
            if ($method == 'post' && in_array($request_format, ['json', 'xml'])) {
                $http_header[] = 'Content-Type: application/' . $request_format;
                //$http_header[] = 'Content-Length: '. strlen($data);
            }
        }
        if ($this->api_token) {
            $http_header[] = "Authorization: TOKEN uuid:{$this->api_token}";
        }
        curl_setopt($curl, CURLOPT_HTTPHEADER, $http_header);
        // Okay, let's do some curling!
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $http_header);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, true);
        curl_setopt($curl, CURLINFO_HEADER_OUT, true);

        if (DEBUG === true) {
            echo "\n-------------------------------BEGIN-HTTP-DEBUG--------------------------------\n";
            echo $url . "\n";
            //curl_setopt($curl, CURLOPT_VERBOSE, true);
        }
        $curl_response = curl_exec($curl);
        $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        $header = substr($curl_response, 0, $header_size);
        $body = substr($curl_response, $header_size);
        if (DEBUG === true) {
            echo "\n-------------------------------------------------------------------------------\n";
            if (curl_error($curl)) {
                echo 'ERROR: ' . curl_error($curl) . "\n";
            }
            $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            if ($http_code != 200) {
                echo 'ERROR: ' . $http_code . ' ' . $this->lookupHTTPCode($http_code) . "\n";
            }
            echo "\n-------------------------------------------------------------------------------\n";
            echo "\nREQUEST:\n";
            //echo curl_getinfo($curl, CURLINFO_REQUEST_SIZE);
            echo curl_getinfo($curl, CURLINFO_HEADER_OUT);
            echo "\n-------------------------------------------------------------------------------\n";
            echo "\nRESPONSE:\n";
            echo $curl_response;
            echo "\n--------------------------------END-HTTP-DEBUG---------------------------------\n\n";
        }
        if (curl_error($curl)) {
            throw new \Exception('Curl error: ' . curl_error($curl));
        }
        curl_close($curl);
        switch ($response_format) {
            case 'json':
                $body = json_decode($body, true);
                break;
            case 'xml':
                $body = new SimpleXMLElement($body);
                break;
            case 'raw':
            default:
                // Don't do anything special
                break;
        }

        return $body;
    }

    private function lookupHTTPCode($code)
    {
        $http_codes = [
            100 => 'Continue',
            101 => 'Switching Protocols',
            102 => 'Processing',
            200 => 'OK',
            201 => 'Created',
            202 => 'Accepted',
            203 => 'Non-Authoritative Information',
            204 => 'No Content',
            205 => 'Reset Content',
            206 => 'Partial Content',
            207 => 'Multi-Status',
            300 => 'Multiple Choices',
            301 => 'Moved Permanently',
            302 => 'Found',
            303 => 'See Other',
            304 => 'Not Modified',
            305 => 'Use Proxy',
            306 => 'Switch Proxy',
            307 => 'Temporary Redirect',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            406 => 'Not Acceptable',
            407 => 'Proxy Authentication Required',
            408 => 'Request Timeout',
            409 => 'Conflict',
            410 => 'Gone',
            411 => 'Length Required',
            412 => 'Precondition Failed',
            413 => 'Request Entity Too Large',
            414 => 'Request-URI Too Long',
            415 => 'Unsupported Media Type',
            416 => 'Requested Range Not Satisfiable',
            417 => 'Expectation Failed',
            418 => 'I\'m a teapot',
            422 => 'Unprocessable Entity',
            423 => 'Locked',
            424 => 'Failed Dependency',
            425 => 'Unordered Collection',
            426 => 'Upgrade Required',
            449 => 'Retry With',
            450 => 'Blocked by Windows Parental Controls',
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
            504 => 'Gateway Timeout',
            505 => 'HTTP Version Not Supported',
            506 => 'Variant Also Negotiates',
            507 => 'Insufficient Storage',
            509 => 'Bandwidth Limit Exceeded',
            510 => 'Not Extended'
        ];

        return $http_codes[$code];
    }
}

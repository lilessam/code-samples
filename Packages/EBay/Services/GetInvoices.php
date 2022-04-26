<?php

namespace App\Ebay\Services;

use App\Ebay\Contracts\Service;
use App\Ebay\Services\Traits\EbayHelper;

class GetInvoices implements Service
{
    use EbayHelper;

    /**
     * The user AuthnAuth Token.
     *
     * @var string
     */
    protected $_token;

    /**
     * The Ebay API version.
     *
     * @var int
     */
    protected $_eBayApiVersion;

    /**
     * The intended call name.
     *
     * @var string
     */
    protected $_call = 'GetOrders';

    /**
     * Execute the service.
     *
     * @return void
     */
    public function execute(...$args)
    {
        $this->_token = $args[0];

        $this->_eBayApiVersion = 613;

        $CreateTimeFrom = '2000-01-01' . gmdate("\TH:i:s");
        $CreateTimeTo = gmdate("Y-m-d\TH:i:s");

        $requestXmlBody = '<?xml version="1.0" encoding="utf-8" ?>';
        $requestXmlBody .= '<GetOrdersRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
        $requestXmlBody .= '<DetailLevel>ReturnAll</DetailLevel>';

        $requestXmlBody .= "<CreateTimeFrom>$CreateTimeFrom</CreateTimeFrom>
                            <CreateTimeTo>$CreateTimeTo</CreateTimeTo>";

        $requestXmlBody .= "<Pagination>
                                        <EntriesPerPage>100</EntriesPerPage>
                                        <PageNumber>$args[1]</PageNumber>
                            </Pagination>";
        $requestXmlBody .= '<OrderRole>Seller</OrderRole><OrderStatus>Active</OrderStatus>';
        $requestXmlBody .= "<RequesterCredentials><eBayAuthToken>$this->_token</eBayAuthToken></RequesterCredentials>";
        $requestXmlBody .= '</GetOrdersRequest>';

        return $this->eBaySend($requestXmlBody);
    }
}

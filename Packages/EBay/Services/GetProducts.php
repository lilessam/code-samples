<?php

namespace App\Ebay\Services;

use App\Ebay\Contracts\Service;
use App\Ebay\Services\Traits\EbayHelper;

class GetProducts implements Service
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
    protected $_call = 'GetSellerList';

    /**
     * Execute the service.
     *
     * @return void
     */
    public function execute(...$args)
    {
        $this->_token = $args[0];
        $this->_eBayApiVersion = 613;

        $CreateTimeFrom = date('Y-m-d\TH:i:s', strtotime('-119 days'));
        $CreateTimeTo = gmdate("Y-m-d\TH:i:s");
        $requestXmlBody = '<?xml version="1.0" encoding="utf-8" ?>';
        $requestXmlBody .= '<GetSellerListRequest  xmlns="urn:ebay:apis:eBLBaseComponents">';

        $requestXmlBody .= "<ErrorLanguage>en_US</ErrorLanguage>
                              <WarningLevel>High</WarningLevel>
                              <GranularityLevel>Coarse</GranularityLevel>
                              <StartTimeFrom>$CreateTimeFrom</StartTimeFrom>
                              <StartTimeTo>$CreateTimeTo</StartTimeTo>
                              <IncludeWatchCount>true</IncludeWatchCount>
                              <Pagination>
                                        <EntriesPerPage>100</EntriesPerPage>
                                        <PageNumber>$args[1]</PageNumber>
                            </Pagination>";
        $requestXmlBody .= "<RequesterCredentials><eBayAuthToken>$this->_token</eBayAuthToken></RequesterCredentials>";
        $requestXmlBody .= '</GetSellerListRequest>';

        return $this->eBaySend($requestXmlBody);
    }
}

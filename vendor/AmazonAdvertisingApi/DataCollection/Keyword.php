<?php
namespace AmazonAdvertisingApi\DataCollection;

use AmazonAdvertisingApi\Connection\Connection;
use AmazonAdvertisingApi\Table\AmazonAdsAdGroupTable;
use AmazonAdvertisingApi\Table\AmazonAdsCampaignTable;
use AmazonAdvertisingApi\Table\AmazonAdsKeywordTable;
use AmazonAdvertisingApi\Table\AmazonAdsPortfolioTable;
use AmazonAdvertisingApi\Table\AmazonAdsProfileTable;
use App\ApplicationModul\AppManagement\Controller\AppManagementController;
use Micho\Utilities\StringUtilities;
use Exception;

class Keyword extends Request
{
    /**
     * Nastavenia
     */
    protected $className = 'Keyword';
    protected $contentType = 'application/vnd.spKeyword.v3+json';
    protected $accept = 'application/vnd.spKeyword.v3+json';
    protected $prefer = 'return=representation';
    protected $endPoint = '/sp/keywords/list';
    protected $listMethod = 'POST';
    protected $getMethod = 'POST';
    protected $filter = 'keyword';


}
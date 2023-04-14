<?php
namespace AmazonAdvertisingApi\DataCollection;

use AmazonAdvertisingApi\Connection\Connection;
use AmazonAdvertisingApi\Table\AmazonAdsAdGroupTable;
use AmazonAdvertisingApi\Table\AmazonAdsCampaignTable;
use AmazonAdvertisingApi\Table\AmazonAdsPortfolioTable;
use AmazonAdvertisingApi\Table\AmazonAdsProfileTable;
use App\ApplicationModul\AppManagement\Controller\AppManagementController;
use Micho\Utilities\StringUtilities;
use Exception;

class ProductAds extends Request
{
    /**
     * Nastavenia
     */
    protected $className = 'ProductAds';
    protected $contentType = 'application/vnd.spProductAd.v3+json';
    protected $accept = 'application/vnd.spProductAd.v3+json';
    protected $endPoint = '/sp/productAds/list';
    protected $listMethod = 'POST';
    protected $getMethod = 'POST';
    protected $filter = 'productAds';


}
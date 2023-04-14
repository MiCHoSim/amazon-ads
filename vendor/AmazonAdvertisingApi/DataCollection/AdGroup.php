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

class AdGroup extends Request
{
    /**
     * Nastavenia
     */
    protected $className = 'AdGroup';
    protected $contentType = 'application/vnd.spAdGroup.v3+json';
    protected $accept = 'application/vnd.spAdGroup.v3+json';
    protected $endPoint = '/sp/adGroups/list';
    protected $listMethod = 'POST';
    protected $getMethod = 'POST';
    protected $filter = 'adGroup';


}
<?php
namespace AmazonAdvertisingApi\DataCollection;

use AmazonAdvertisingApi\Connection\Connection;
use AmazonAdvertisingApi\Table\AmazonAdsCampaignTable;
use AmazonAdvertisingApi\Table\AmazonAdsPortfolioTable;
use AmazonAdvertisingApi\Table\AmazonAdsProfileTable;
use App\ApplicationModul\AppManagement\Controller\AppManagementController;
use Micho\Utilities\StringUtilities;
use Exception;

class Campaign extends Request
{
    /**
     * Nastavenia
     */
    protected $className = 'Campaign';
    protected $contentType = 'application/vnd.spCampaign.v3+json';
    protected $accept = 'application/vnd.spCampaign.v3+json';
    protected $endPoint = '/sp/campaigns/list';
    protected $listMethod = 'POST';
    protected $getMethod = 'POST';
    protected $filter = 'campaign';

    /**
     * Konštanty
     */
    const BUDGET = 'budget';
    const DYNAMIC_BIDDING = 'dynamicBidding';
    const PLACEMENT_BIDDING = 'placementBidding';









}
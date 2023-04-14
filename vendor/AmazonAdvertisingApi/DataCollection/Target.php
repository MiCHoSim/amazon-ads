<?php
namespace AmazonAdvertisingApi\DataCollection;

use AmazonAdvertisingApi\Connection\Connection;
use AmazonAdvertisingApi\Table\AmazonAdsAdGroupTable;
use AmazonAdvertisingApi\Table\AmazonAdsCampaignTable;
use AmazonAdvertisingApi\Table\AmazonAdsKeywordTable;
use AmazonAdvertisingApi\Table\AmazonAdsPortfolioTable;
use AmazonAdvertisingApi\Table\AmazonAdsProfileTable;
use AmazonAdvertisingApi\Table\AmazonAdsTargetTable;
use App\ApplicationModul\AppManagement\Controller\AppManagementController;
use Micho\Utilities\StringUtilities;
use Exception;

class Target extends Request
{
    /**
     * Nastavenia
     */
    protected $className = 'Target';
    protected $contentType = 'application/vnd.spTargetingClause.v3+json';
    protected $accept = 'application/vnd.spTargetingClause.v3+json';
    protected $prefer = 'return=representation';
    protected $endPoint = '/sp/targets/list';
    protected $listMethod = 'POST';
    protected $getMethod = 'POST';
    protected $filter = 'target';

    /**
     * Konštanty
     */
    const EXPRESION = 'expression';
    const RESOLVED_EXPRESION = 'resolvedExpression';


}
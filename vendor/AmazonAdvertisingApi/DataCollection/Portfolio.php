<?php
namespace AmazonAdvertisingApi\DataCollection;

use AmazonAdvertisingApi\Connection\Connection;
use AmazonAdvertisingApi\Table\AmazonAdsPortfolioTable;
use AmazonAdvertisingApi\Table\AmazonAdsProfileTable;
use App\ApplicationModul\Amazon\Controller\AmazonAdsController;
use Micho\Utilities\StringUtilities;
use Exception;

class Portfolio extends Request
{
    /**
     * Nastavenia
     */
    protected $className = 'Portfolio';
    protected $contentType = 'application/json';

    protected $endPoint = '/v2/portfolios';
    protected $listMethod = 'GET';
    protected $getMethod = 'GET';


}
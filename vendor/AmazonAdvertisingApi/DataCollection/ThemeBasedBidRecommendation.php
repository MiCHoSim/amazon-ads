<?php
namespace AmazonAdvertisingApi\DataCollection;

use AmazonAdvertisingApi\Connection\Connection;
use AmazonAdvertisingApi\Table\AmazonAdsAdGroupTable;
use AmazonAdvertisingApi\Table\AmazonAdsCampaignTable;
use AmazonAdvertisingApi\Table\AmazonAdsKeywordTable;
use AmazonAdvertisingApi\Table\AmazonAdsPortfolioTable;
use AmazonAdvertisingApi\Table\AmazonAdsProfileTable;
use AmazonAdvertisingApi\Table\AmazonAdsTargetTable;
use App\ApplicationModul\Amazon\Controller\AmazonAdsController;
use App\ApplicationModul\AppManagement\Controller\AppManagementController;
use Micho\Utilities\StringUtilities;
use Exception;

/**
 * Bid Recommendation verzia V3
 * Example values of keyword_text where match_type is TARGETING_EXPRESSION_PREDEFINED (for auto targeted campaigns).
 * loose-match (auto-keyword targeting)
 * close-match (auto-keyword targeting)
 * substitutes (auto-product targeting)
 * complements (auto-product targeting)
 */
class ThemeBasedBidRecommendation extends Request
{
    /**
     * Nastavenia
     */
    protected $className = 'ThemeBasedBidRecommendation';
    protected $contentType = 'application/json';
    protected $accept = 'application/json';
    protected $prefer = 'return=representation';
    protected $endPoint = '/sp/targets/bid/recommendations';
    protected $listMethod = 'POST';

    protected $dataRaw = [
        'recommendationType' => 'BIDS_FOR_EXISTING_AD_GROUP',
        'targetingExpressions' => [
            ['type' => null]
        ],
        'campaignId' => null,
        'adGroupId' => null
    ];

    /**
     * Konštanty
     */
    const BID_RECOMMENDATIONS = 'bidRecommendations';
    //const THEME = 'theme';const IMPACT_METRICS = 'impactMetrics';const CLICKS = 'clicks';const ORDERS = 'orders';const VALUES = 'values';const LOWER = 'lower'; // intconst UPPER = 'upper'; // int
    const BID_RECOMMENDATIONS_FOR_TARGETING_EXPRESSIONS = 'bidRecommendationsForTargetingExpressions';
    const TARGETING_EXPRESSION = 'targetingExpression';
        const TARGETING_EXPRESSION_ARRAY_TYPE = 'targetingExpressionArrayType';
        const TARGETING_EXPRESSION_ARRAY_VALUE = 'targetingExpressionArrayValue';
    const BID_VALUES = 'bidValues';
        const SUGGESTED_BID = 'suggestedBid'; // double
            const BID_VALUES_ARRAY_LOW_ARRAY_SUGGESTED_BID = 'bidValuesArrayLowArraySuggestedBid'; // double
            const BID_VALUES_ARRAY_MEDIAN_ARRAY_SUGGESTED_BID = 'bidValuesArrayMedianArraySuggestedBid'; // double
            const BID_VALUES_ARRAY_HIGH_ARRAY_SUGGESTED_BID = 'bidValuesArrayHighArraySuggestedBid'; // double

    const MATCH_TYPE_OPTIONS = ['TARGETING_EXPRESSION_PREDEFINED'];

    /**
     * @param Connection $connection Spojenie s Amazon
     * @param string $campaignId Id Kampane
     * @param string $adGroupId Id reklamenj skupiny
     * @param string $targetingExpressionTypeKeyword Typ zamiereného Kľučového slová / Kľučové slovo
     */
    public function __construct(Connection $connection, string $campaignId, string $adGroupId, string $targetingExpressionTypeKeyword)
    {
        $this->dataRaw['campaignId'] = $campaignId;
        $this->dataRaw['adGroupId'] = $adGroupId;
        $this->dataRaw['targetingExpressions'] = [['type' => $targetingExpressionTypeKeyword]];

        parent::__construct($connection);
    }

    /**
     ** Stiahne a pripravy Dáta
     * @return array Pole upravených dát pripravených na uloženie do DB
     * @throws Exception
     */
    public function prepareData() : array
    {
        $requestData = $this->list();
        //echo "theme<br>";
        //AmazonAdsController::view($requestData);die();

        // uprava pre kompatibilitu
        $separatedData[self::BID_RECOMMENDATIONS] = json_decode($requestData[Connection::RESPONSE],true)[self::BID_RECOMMENDATIONS][0][self::BID_RECOMMENDATIONS_FOR_TARGETING_EXPRESSIONS];

        // vytiahnutie konkretných hodnot
        $separatedData[self::BID_RECOMMENDATIONS][0][self::BID_VALUES]['low'] = isset($separatedData[self::BID_RECOMMENDATIONS][0][self::BID_VALUES]) ? array_shift($separatedData[self::BID_RECOMMENDATIONS][0][self::BID_VALUES]) : null;
        $separatedData[self::BID_RECOMMENDATIONS][0][self::BID_VALUES]['median'] = isset($separatedData[self::BID_RECOMMENDATIONS][0][self::BID_VALUES]) ? array_shift($separatedData[self::BID_RECOMMENDATIONS][0][self::BID_VALUES]) : null;
        $separatedData[self::BID_RECOMMENDATIONS][0][self::BID_VALUES]['high'] = isset($separatedData[self::BID_RECOMMENDATIONS][0][self::BID_VALUES]) ? array_shift($separatedData[self::BID_RECOMMENDATIONS][0][self::BID_VALUES]) : null;

        $requestData[Connection::RESPONSE] = json_encode($separatedData);

        return $this->setTableData($requestData);
    }
}
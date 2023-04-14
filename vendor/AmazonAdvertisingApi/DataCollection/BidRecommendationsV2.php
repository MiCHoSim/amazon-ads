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
 * Bid Recommendations verzia V2
 * Example values of keyword_text where match_type is TARGETING_EXPRESSION (for manual targeted campaigns)
 * category="3764301" (category targeting)
 * asin=“B086PQR1Y3” (ASIN targeting)
 */
class BidRecommendationsV2 extends Request
{
    /**
     * Nastavenia
     */
    protected $className = 'BidRecommendationsV2';
    protected $contentType = 'application/json';
    protected $accept = 'application/json';
    protected $prefer = 'return=representation';
    protected $endPoint = '/v2/sp/targets/bidRecommendations';
    protected $listMethod = 'POST';

    protected $dataRaw = [
        'adGroupId' => null,
        'expressions' => [
            [
                ['value' => null, 'type' => null]
            ]
        ]
    ];

    /**
     * Konštanty
     */
    const BID_RECOMMENDATIONS = 'bidRecommendations';
    const AD_GROUP_ID = 'adGroupId';    //string
    const RECOMMENDATIONS = 'recommendations';
        const SUGGESTED_BID = 'suggestedBid';
            const RECOMMENDATIONS_ARRAY_SUGGESTED_BID_ARRAY_SUGGESTED = 'recommendationsArraySuggestedBidArraySuggested';//double
            const RECOMMENDATIONS_ARRAY_SUGGESTED_BID_ARRAY_RANGE_START = 'recommendationsArraySuggestedBidArrayRangeStart';//double
            const RECOMMENDATIONS_ARRAY_SUGGESTED_BID_ARRAY_RANGE_END = 'recommendationsArraySuggestedBidArrayRangeEnd';//double
        const EXPRESSION = 'Expression';
            const RECOMMENDATIONS_ARRAY_EXPRESSION_ARRAY_VALUE = 'recommendationsArrayExpressionArrayValue';
            const RECOMMENDATIONS_ARRAY_EXPRESSION_ARRAY_TYPE = 'recommendationsArrayExpressionArrayType';
        const RECOMMENDATIONS_ARRAY_CODE = 'code';

    const MATCH_TYPE_OPTIONS = ['TARGETING_EXPRESSION'];
    const EXPRESSION_TYPE = ['asin' => 'asinSameAs', 'category' => 'asinCategorySameAs'];

    /**
     * @param Connection $connection Spojenie s Amazon
     * @param string $adGroupId Id reklamenj skupiny
     * @param string $value Hodnota Asin/category
     * @param string $type Typ asin alebo category
     */
    public function __construct(Connection $connection, string $adGroupId, string $value, string $type)
    {
        $this->dataRaw['adGroupId'] = $adGroupId;
        $this->dataRaw['expressions'] = [[['value' => $value, 'type' => self::EXPRESSION_TYPE[$type]]]];

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

        // uprave pre kompatibilitu
        $separatedData[self::BID_RECOMMENDATIONS][] = json_decode($requestData[Connection::RESPONSE],true);
        $requestData[Connection::RESPONSE] = json_encode($separatedData);

        //echo "recom V2<br>";
        //AmazonAdsController::view($requestData);
        return $this->setTableData($requestData);
    }
}
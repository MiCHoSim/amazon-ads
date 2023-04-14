<?php
namespace AmazonAdvertisingApi\DataCollection;

use AmazonAdvertisingApi\Connection\Connection;
use AmazonAdvertisingApi\Table\AmazonAdsAdGroupTable;
use AmazonAdvertisingApi\Table\AmazonAdsCampaignTable;
use AmazonAdvertisingApi\Table\AmazonAdsKeywordTable;
use AmazonAdvertisingApi\Table\AmazonAdsPortfolioTable;
use AmazonAdvertisingApi\Table\AmazonAdsProfileTable;
use AmazonAdvertisingApi\Table\AmazonAdsTargetTable;
use AmazonAdvertisingApi\Table\Table;
use App\ApplicationModul\Amazon\Controller\AmazonAdsController;
use App\ApplicationModul\AppManagement\Controller\AppManagementController;
use Micho\Utilities\StringUtilities;
use Exception;

/**
 * KeywordRecommendations verzia V4
 * For keywords, match_type is set to either BROAD, PHRASE, or EXACT.
 * The keyword_text field contains information on the exact keyword.
 */
class KeywordRecommendations extends Request
{
    /**
     * Nastavenia
     */
    protected $className = 'KeywordRecommendations';
    protected $contentType = 'application/vnd.spkeywordsrecommendation.v4+json';
    protected $accept = 'application/vnd.spkeywordsrecommendation.v4+json';
    protected $prefer = 'return=representation';
    protected $endPoint = '/sp/targets/keywords/recommendations';
    protected $listMethod = 'POST';

    protected $dataRaw = [
        'recommendationType' => 'KEYWORDS_FOR_ADGROUP',
        'maxRecommendations' => 0,
        'campaignId' => null,
        'adGroupId' => null,
        'locale' => 'en_GB',
        'targets' => [
            ['matchType' => null, 'keyword' => null]
        ]];

    /**
     * Konštanty
     */
    const KEYWORD_TARGET_LIST = 'keywordTargetList';
    const KEYWORD = 'keyword';
    const TRANSLATION = 'translation';
    const USER_SELECTED_KEYWORD = 'userSelectedKeyword'; //bool
    const SEARCH_TERM_IMPRESSION_RANK = 'searchTermImpressionRank';// number
    const SEARCH_TERM_IMPRESSION_SHARE = 'searchTermImpressionShare';//double
    const REC_ID = 'recId';
    const BID_INFO = 'bidInfo';
        const MATCH_TYPE = 'matchType';
        const RANK = 'rank'; // number
        const BID = 'bid'; //double
        const SUGGESTED_BID = 'suggestedBid';
            const RANGE_START = 'rangeStart';//double
            const SUGGESTED_MEDIAN = 'rangeMedian';//double
            const RANGE_END = 'rangeEnd';//double
            const BID_REC_ID = 'bidRecId';

    const MATCH_TYPE_OPTIONS = ['BROAD', 'EXACT', 'PHRASE'];

    /**
     * @param Connection $connection Spojenie s Amazon
     * @param string $selectedBy Typ podla ktoreho načitavam udaje z Amazon adGroup, asin
     * @param string $campaignId Id Kampane
     * @param string $adGroupId Id reklamenj skupiny
     * @param string $matchType Match Type sklucového slová
     * @param string $keyword Kľučové slovo
     */
    public function __construct(Connection $connection, string $campaignId, string $adGroupId, string $matchType, string $keyword)
    {
        $this->dataRaw['campaignId'] = $campaignId;
        $this->dataRaw['adGroupId'] = $adGroupId;
        $this->dataRaw['targets'] = [['matchType' => $matchType, 'keyword' => $keyword]];

        //print_r($this->dataRaw);
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
        /*
        echo "keyword<br>";
        AmazonAdsController::view($requestData);die;
*/
        return $this->setTableData($requestData);
    }

}
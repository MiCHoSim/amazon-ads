<?php

namespace AmazonAdvertisingApi\Table;

use AmazonAdvertisingApi\Connection\Connection;
use AmazonAdvertisingApi\DataCollection\BidRecommendationsV2;
use AmazonAdvertisingApi\DataCollection\Keyword;
use AmazonAdvertisingApi\DataCollection\KeywordRecommendations;
use AmazonAdvertisingApi\DataCollection\ThemeBasedBidRecommendation;
use AmazonAdvertisingApi\Report\ReportDictionary;
use App\AccountModul\Model\UserTable;
use App\ApplicationModul\Amazon\Controller\AmazonAdsController;
use Micho\Db;
use Micho\Utilities\ArrayUtilities;
use Micho\Utilities\StringUtilities;
use PSpell\Dictionary;

/**
 * Trieda pre tabuľku amazon_ads_sp_targeting
 */
class AmazonAdsSpTargetingTable extends AmazonAdsSpTable
{
    /**
     * Názov Tabuľky
     */
    const TABLE = 'amazon_ads_sp_targeting';

    /**
     * Konštanty Databázy
     */
    const AMAZON_ADS_SP_TARGETING_ID = 'amazon_ads_sp_targeting_id';

    const BID = 'bid'; // či už boli stahovené bidy

    const PORTFOLIO_ID = 'portfolio_id';
    const CAMPAIGN_ID = 'campaign_id';
    const AD_GROUP_ID = 'ad_group_id';

    const KEYWORD_ID = 'keyword_id';
    const TARGET_ID = 'target_id'; // Dorobiene kvoli tomu že v pripade že je to target smerovany inde tak ho rpesuniem tu a povodny dam NULL a by sa mi nebili FOREIGN KEY v DB
// Dorobene kvoli bidom bidi som odstranil elbo sa uz tak nepouzivaju ale su iodkazovane bidi na target kvolu cascade delete
    //const AMAZON_ADS_THEME_BASED_BID_RECOMMENDATION_ID = AmazonAdsThemeBasedBidRecommendationTable::AMAZON_ADS_THEME_BASED_BID_RECOMMENDATION_ID;
    //const AMAZON_ADS_RECOMMENDATIONS_V2_ID = AmazonAdsBidRecommendationsV2Table::AMAZON_ADS_RECOMMENDATIONS_V2_ID;
    //const AMAZON_ADS_KEYWORD_RECOMMENDATIONS_ID = AmazonAdsKeywordRecommendationsTable::AMAZON_ADS_KEYWORD_RECOMMENDATIONS_ID;
    const SUGEST_BID_LOW = 'sugested_bid_low';
    const SUGEST_BID_MEDIAN = 'sugested_bid_median';
    const SUGEST_BID_HIGH = 'sugested_bid_high';
    const TRANSLATION = AmazonAdsKeywordRecommendationsTable::TRANSLATION;
    const SEARCH_TERM_IMPRESSION_SHARE = AmazonAdsKeywordRecommendationsTable::SEARCH_TERM_IMPRESSION_SHARE;

    const DATE = 'date';
    const START_DATE = 'start_date';
    const END_DATE = 'end_date';

    const CAMPAIGN_NAME = 'campaign_name';
    const AD_GROUP_NAME = 'ad_group_name';
    const KEYWORD = 'keyword';
    const IMPRESSIONS = 'impressions';
    const CLICKS = 'clicks';
    const COST_PER_CLICK = 'cost_per_click';
    const CLICK_THROUGH_RATE = 'click_through_rate';
    const COST = 'cost';
    const PURCHASES1D = 'purchases1d';
    const PURCHASES7D = 'purchases7d';
    const PURCHASES14D = 'purchases14d';
    const PURCHASES30D = 'purchases30d';
    const PURCHASES_SAME_SKU1D = 'purchases_same_sku1d';
    const PURCHASES_SAME_SKU7D = 'purchases_same_sku7d';
    const PURCHASES_SAME_SKU14D = 'purchases_same_sku14d';
    const PURCHASES_SAME_SKU30D = 'purchases_same_sku30d';
    const UNITS_SOLD_CLICKS1D = 'units_sold_clicks1d';
    const UNITS_SOLD_CLICKS7D = 'units_sold_clicks7d';
    const UNITS_SOLD_CLICKS14D = 'units_sold_clicks14d';
    const UNITS_SOLD_CLICKS30D = 'units_sold_clicks30d';
    const SALES1D = 'sales1d';
    const SALES7D = 'sales7d';
    const SALES14D = 'sales14d';
    const SALES30D = 'sales30d';
    const ATTRIBUTED_SALES_SAME_SKU1D = 'attributed_sales_same_sku1d';
    const ATTRIBUTED_SALES_SAME_SKU7D = 'attributed_sales_same_sku7d';
    const ATTRIBUTED_SALES_SAME_SKU14D = 'attributed_sales_same_sku14d';
    const ATTRIBUTED_SALES_SAME_SKU30D = 'attributed_sales_same_sku30d';
    const UNITS_SOLD_SAME_SKU1D = 'units_sold_same_sku1d';
    const UNITS_SOLD_SAME_SKU7D = 'units_sold_same_sku7d';
    const UNITS_SOLD_SAME_SKU14D = 'units_sold_same_sku14d';
    const UNITS_SOLD_SAME_SKU30D = 'units_sold_same_sku30d';
    const KINDLE_EDITION_NORMALIZED_PAGES_READ14D = 'kindle_edition_normalized_pages_read14d';
    const KINDLE_EDITION_NORMALIZED_PAGES_ROYALTIES14D = 'kindle_edition_normalized_pages_royalties14d';
    const SALES_OTHER_SKU7D = 'sales_other_sku7d';
    const UNITS_SOLD_OTHER_SKU7D = 'units_sold_other_sku7d';
    const ACOS_CLICKS7D = 'acos_clicks7d';
    const ACOS_CLICKS14D = 'acos_clicks14d';
    const ROAS_CLICKS7D = 'roas_clicks7d';
    const ROAS_CLICKS14D = 'roas_clicks14d';
    const CAMPAIGN_BUDGET_CURRENCY_CODE = 'campaign_budget_currency_code';
    const CAMPAIGN_BUDGET_TYPE = 'campaign_budget_type';
    const CAMPAIGN_BUDGET_AMOUNT = 'campaign_budget_amount';
    const CAMPAIGN_STATUS = 'campaign_status';
    const KEYWORD_BID = 'keyword_bid';
    const KEYWORD_TYPE = 'keyword_type';
    const MATCH_TYPE = 'match_type';
    const TARGETING = 'targeting';
    const AD_KEYWORD_STATUS = 'ad_keyword_status';

    /**
     * @var array Kľuče
     */
    // Ostatné kluče
    const KEYS = [
        self::AMAZON_ADS_SP_TARGETING_ID,self::SELECT_DATE_ID,self::TIME_UNIT_ID,self::USER_ID,self::PROFILE_ID,
        self::KEYWORD_ID,self::TARGET_ID,self::BID,
        self::PORTFOLIO_ID,self::CAMPAIGN_ID,self::AD_GROUP_ID];
        //self::AMAZON_ADS_THEME_BASED_BID_RECOMMENDATION_ID,self::AMAZON_ADS_RECOMMENDATIONS_V2_ID,
        //self::AMAZON_ADS_KEYWORD_RECOMMENDATIONS_ID];

    //vyber ktory je zaškrutnutý
    const CHECKED_COL = [
        self::AD_KEYWORD_STATUS => true,
        self::MATCH_TYPE => true,
        self::KEYWORD => true,

        //suggestion pridané na zobrazenie
        self::TRANSLATION => true,
        self::SUGEST_BID_LOW => true,
        self::SUGEST_BID_MEDIAN => true,
        self::SUGEST_BID_HIGH => true,
        self::SEARCH_TERM_IMPRESSION_SHARE => true,

        self::KEYWORD_BID => true,

        self::IMPRESSIONS => true,self::CLICKS => true,self::CLICK_THROUGH_RATE => true,
        self::COST => true,self::COST_PER_CLICK => true,self::PURCHASES7D => true,self::SALES7D => true,self::ACOS_CLICKS14D => true,self::ROAS_CLICKS14D => true,
    ];

    // Hodnoty pre bidi
    const BIDS_VALUES = [self::SEARCH_TERM_IMPRESSION_SHARE, self::TRANSLATION,self::SUGEST_BID_LOW,self::SUGEST_BID_MEDIAN,self::SUGEST_BID_HIGH];

    //vyber ktory nieje zaškrutnuty
    const UNCHECKED_COL = [self::TARGETING,self::UNITS_SOLD_CLICKS1D,
        self::DATE,self::START_DATE,self::END_DATE,self::PURCHASES1D,self::PURCHASES14D,
        self::PURCHASES30D,self::PURCHASES_SAME_SKU1D,self::PURCHASES_SAME_SKU7D,self::PURCHASES_SAME_SKU14D,
        self::PURCHASES_SAME_SKU30D,
        self::UNITS_SOLD_CLICKS7D,self::UNITS_SOLD_CLICKS14D,self::SALES1D,
        self::UNITS_SOLD_CLICKS30D,self::SALES14D,self::SALES30D,
        self::ATTRIBUTED_SALES_SAME_SKU1D,self::ATTRIBUTED_SALES_SAME_SKU7D,
        self::ATTRIBUTED_SALES_SAME_SKU14D,self::ATTRIBUTED_SALES_SAME_SKU30D,self::UNITS_SOLD_SAME_SKU1D,
        self::UNITS_SOLD_SAME_SKU7D,self::UNITS_SOLD_SAME_SKU14D,self::UNITS_SOLD_SAME_SKU30D,
        self::KINDLE_EDITION_NORMALIZED_PAGES_READ14D,self::KINDLE_EDITION_NORMALIZED_PAGES_ROYALTIES14D,
        self::SALES_OTHER_SKU7D,self::UNITS_SOLD_OTHER_SKU7D,self::ACOS_CLICKS7D,self::ROAS_CLICKS7D,
        self::CAMPAIGN_BUDGET_CURRENCY_CODE,

        self::CAMPAIGN_NAME,self::CAMPAIGN_BUDGET_TYPE,self::CAMPAIGN_BUDGET_AMOUNT,
        self::CAMPAIGN_STATUS,self::AD_GROUP_NAME,self::KEYWORD_TYPE];

    //vŠetky kluče
    public $keys;

    public $keysWithBids;

    //Kluče na vyber do zašrtnutia
    public $checkBoxKeys;

    public function __construct(bool|string $id = false)
    {
        $this->checkBoxKeys = array_merge(array_keys(self::CHECKED_COL),self::UNCHECKED_COL);
        $keys = array_merge(self::KEYS,$this->checkBoxKeys);

        $this->keys = array_diff($keys, self::BIDS_VALUES);

        $this->keysWithBids = $keys;

        parent::__construct($id);
    }

    /**
     * @var null Atributy
     */
    protected $amazonAdsSpTargetingId = null;
    protected $bid = null;
    protected $selectDateId = null;
    protected $timeUnitId = null;
    protected $date = null;
    protected $startDate = null;
    protected $endDate = null;
    protected $impressions = null;
    protected $clicks = null;
    protected $costPerClick = null;
    protected $clickThroughRate = null;
    protected $cost = null;
    protected $purchases1d = null;
    protected $purchases7d = null;
    protected $purchases14d = null;
    protected $purchases30d = null;
    protected $purchasesSameSku1d = null;
    protected $purchasesSameSku7d = null;
    protected $purchasesSameSku14d = null;
    protected $purchasesSameSku30d = null;
    protected $unitsSoldClicks1d = null;
    protected $unitsSoldClicks7d = null;
    protected $unitsSoldClicks14d = null;
    protected $unitsSoldClicks30d = null;
    protected $sales1d = null;
    protected $sales7d = null;
    protected $sales14d = null;
    protected $sales30d = null;
    protected $attributedSalesSameSku1d = null;
    protected $attributedSalesSameSku7d = null;
    protected $attributedSalesSameSku14d = null;
    protected $attributedSalesSameSku30d = null;
    protected $unitsSoldSameSku1d = null;
    protected $unitsSoldSameSku7d = null;
    protected $unitsSoldSameSku14d = null;
    protected $unitsSoldSameSku30d = null;
    protected $kindleEditionNormalizedPagesRead14d = null;
    protected $kindleEditionNormalizedPagesRoyalties14d = null;
    protected $salesOtherSku7d = null;
    protected $unitsSoldOtherSku7d = null;
    protected $acosClicks7d = null;
    protected $acosClicks14d = null;
    protected $roasClicks7d = null;
    protected $roasClicks14d = null;

    protected $keywordId = null;
    protected $targetId = null;

    //protected $amazonAdsKeywordRecommendationsId = null;
    //protected $amazonAdsBidRecommendationsV2Id = null;
    //protected $amazonAdsThemeBasedBidRecommendationId = null;
    protected $sugestedBidLow = null;
    protected $sugestedBidMedian = null;
    protected $sugestedBidHigh = null;
    protected $translation = null;
    protected $searchTermImpressionShare = null;

    protected $keyword = null;
    protected $campaignBudgetCurrencyCode = null;
    protected $portfolioId = null;
    protected $campaignName = null;
    protected $campaignId = null;
    protected $campaignBudgetType = null;
    protected $campaignBudgetAmount = null;
    protected $campaignStatus = null;
    protected $keywordBid = null;
    protected $adGroupName = null;
    protected $adGroupId = null;
    protected $keywordType = null;
    protected $matchType = null;
    protected $targeting = null;
    protected $adKeywordStatus = null;

    /**
     * @var Data pre abstraktnú triedu
     */
    protected $table = self::TABLE;
    protected $id = self::AMAZON_ADS_SP_TARGETING_ID;
    protected $whereId = self::AMAZON_ADS_SP_TARGETING_ID;

    protected $getPairKeyKey = null;
    protected $getPairKeyValue = null;

    /**
     ** Prida potrebne kluče pre bidy, v pripade že su vybraté
     * @param array $keys Povodne poľe kľučov
     * @return array pole nových Klúčov
     */
    public function prepareKeys(array $keys) : array
    {
        if($pozition = array_search(self::SUGEST_BID_LOW,$keys))
        {
            unset($keys[$pozition]);
            $keys[] = AmazonAdsThemeBasedBidRecommendationTable::BID_VALUES_ARRAY_SUGGESTED_BID_LOW;
            $keys[] = AmazonAdsBidRecommendationsV2Table::RECOMMENDATIONS_ARRAY_SUGGESTED_BID_ARRAY_RANGE_START;
            $keys[] = AmazonAdsKeywordRecommendationsTable::BID_INFO_ARRAY_SUGGESTED_BID_ARRAY_RANGE_START;
        }
        if($pozition = array_search(self::SUGEST_BID_MEDIAN,$keys))
        {
            unset($keys[$pozition]);
            $keys[] = AmazonAdsThemeBasedBidRecommendationTable::BID_VALUES_ARRAY_SUGGESTED_BID_MEDIAN;
            $keys[] = AmazonAdsBidRecommendationsV2Table::RECOMMENDATIONS_ARRAY_SUGGESTED_BID_ARRAY_SUGGESTED;
            $keys[] = AmazonAdsKeywordRecommendationsTable::BID_INFO_ARRAY_SUGGESTED_BID_ARRAY_RANGE_MEDIAN;
        }
        if($pozition = array_search(self::SUGEST_BID_HIGH,$keys))
        {
            unset($keys[$pozition]);
            $keys[] = AmazonAdsThemeBasedBidRecommendationTable::BID_VALUES_ARRAY_SUGGESTED_BID_HIGH;
            $keys[] = AmazonAdsBidRecommendationsV2Table::RECOMMENDATIONS_ARRAY_SUGGESTED_BID_ARRAY_RANGE_END;
            $keys[] = AmazonAdsKeywordRecommendationsTable::BID_INFO_ARRAY_SUGGESTED_BID_ARRAY_RANGE_END;
        }

        // konflit mezdi nazvami preto
        $pozition = array_search(self::KEYWORD,$keys);
        if ($pozition !== false)
            unset($keys[$pozition]);

        array_splice( $keys, $pozition, 0,
            self::TABLE . '.' . self::KEYWORD);
        return $keys;
    }

    /**
     ** preda Potrebne tabulky bydov do žiadosti
     * @return string Vytvorený Join tabuliek
     */
    public function prepareJoins()
    {
        $joinQuery = ' LEFT JOIN ' . AmazonAdsThemeBasedBidRecommendationTable::AMAZON_ADS_THEME_BASED_BID_RECOMMENDATION_TABLE . ' 
                       USING (' . self::AMAZON_ADS_SP_TARGETING_ID . ')';
        $joinQuery .= ' LEFT JOIN ' . AmazonAdsBidRecommendationsV2Table::AMAZON_ADS_RECOMMENDATIONS_V2_TABLE . ' 
                        USING (' . self::AMAZON_ADS_SP_TARGETING_ID . ')';
        $joinQuery .= ' LEFT JOIN ' . AmazonAdsKeywordRecommendationsTable::AMAZON_ADS_KEYWORD_RECOMMENDATIONS_TABLE . '
                        USING (' . self::AMAZON_ADS_SP_TARGETING_ID . ')';

        return $joinQuery;
    }

    /**
     ** Premenuje názvy bídov na výpis do šablony
     * @param array $reports Dáta reportu
     * @param array $keys kľuče kore zobraujem
     * @return array
     */
    public function editBids(array $reports, array $keys) //: array
    {
        $low = null;
        $median = null;
        $high = null;
        $typeOfBids = [
            new AmazonAdsThemeBasedBidRecommendationTable,
            new AmazonAdsBidRecommendationsV2Table,
            new AmazonAdsKeywordRecommendationsTable
        ];

        foreach ($reports as $key => $report)
        {
            foreach ($typeOfBids as $typeOfBid)
            {
                if(array_search(self::SUGEST_BID_LOW,$keys) !== false &&
                    !empty($report[$typeOfBid::DICTIONARY_BID[self::SUGEST_BID_LOW]]))
                {
                    $low = $report[$typeOfBid::DICTIONARY_BID[self::SUGEST_BID_LOW]];

                    if ($typeOfBid instanceof AmazonAdsKeywordRecommendationsTable) // lebo mi prichádza ako celé číslo
                        $low = $low/100;
                }

                if(array_search(self::SUGEST_BID_MEDIAN,$keys) !== false &&
                    !empty($report[$typeOfBid::DICTIONARY_BID[self::SUGEST_BID_MEDIAN]]))
                {
                    $median = $report[$typeOfBid::DICTIONARY_BID[self::SUGEST_BID_MEDIAN]];

                    if ($typeOfBid instanceof AmazonAdsKeywordRecommendationsTable) // lebo mi prichádza ako celé číslo
                        $median = $median/100;
                }

                if(array_search(self::SUGEST_BID_HIGH,$keys) !== false &&
                    !empty($report[$typeOfBid::DICTIONARY_BID[self::SUGEST_BID_HIGH]]))
                {
                    $high = $report[$typeOfBid::DICTIONARY_BID[self::SUGEST_BID_HIGH]];

                    if ($typeOfBid instanceof AmazonAdsKeywordRecommendationsTable) // lebo mi prichádza ako celé číslo
                        $high = $high/100;
                }
            }
            $reports[$key][self::SUGEST_BID_LOW] = $low;
            $reports[$key][self::SUGEST_BID_MEDIAN] = $median;
            $reports[$key][self::SUGEST_BID_HIGH] = $high;
        }
        $reports = ArrayUtilities::filterKeys($reports, $keys);

        //AmazonAdsController::view($reports);
        return $reports;
    }

}
<?php

namespace AmazonAdvertisingApi\Table;

use App\AccountModul\Model\UserTable;
use Micho\Db;
use Micho\Utilities\ArrayUtilities;
use Micho\Utilities\StringUtilities;

/**
 * Trieda pre tabuľku amazon_ads_sp_search_term
 */
class AmazonAdsSpSearchTermTable extends AmazonAdsSpTable
{
    /**
     * Názov Tabuľky
     */
    const TABLE = 'amazon_ads_sp_search_term';

    /**
     * Konštanty Databázy
     */
    const AMAZON_ADS_SP_SEARCH_TERM_ID = 'amazon_ads_sp_search_term_id';

    const PORTFOLIO_ID = 'portfolio_id';
    const CAMPAIGN_ID = 'campaign_id';
    const AD_GROUP_ID = 'ad_group_id';

    const KEYWORD_ID = 'keyword_id';
    const TARGET_ID = 'target_id'; // Dorobiene kvoli tomu že v pripade že je to target smerovany inde tak ho rpesuniem tu a povodny dam NULL a by sa mi nebili FOREIGN KEY v DB

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
    const SEARCH_TERM = 'search_term';
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
    const KEYS = [self::AMAZON_ADS_SP_SEARCH_TERM_ID,self::SELECT_DATE_ID,self::TIME_UNIT_ID,self::USER_ID,self::PROFILE_ID,
        self::KEYWORD_ID,self::TARGET_ID, self::PORTFOLIO_ID,self::CAMPAIGN_ID,self::AD_GROUP_ID,
    ];

    //vyber ktory je zaškrutnutý
    const CHECKED_COL = [
        self::AD_KEYWORD_STATUS => true,self::KEYWORD => true,self::SEARCH_TERM => true,

        self::KEYWORD_BID => true,

        self::IMPRESSIONS => true,self::CLICKS => true,self::CLICK_THROUGH_RATE => true,

        self::COST => true,self::COST_PER_CLICK => true,self::PURCHASES7D => true,self::SALES7D => true,self::ACOS_CLICKS14D => true,self::ROAS_CLICKS14D => true,
        self::MATCH_TYPE => true,
    ];

    //vyber ktory nieje zaškrutnuty
    const UNCHECKED_COL = [
        self::DATE,self::START_DATE,self::SALES1D,self::ACOS_CLICKS7D,self::ROAS_CLICKS7D,
        self::END_DATE,self::TARGETING,
        self::PURCHASES1D,self::PURCHASES14D,self::PURCHASES30D,self::PURCHASES_SAME_SKU1D,
        self::PURCHASES_SAME_SKU7D,self::PURCHASES_SAME_SKU14D,self::PURCHASES_SAME_SKU30D,self::UNITS_SOLD_CLICKS1D,
        self::UNITS_SOLD_CLICKS7D,self::UNITS_SOLD_CLICKS14D,self::UNITS_SOLD_CLICKS30D,
        self::SALES14D,self::SALES30D,self::ATTRIBUTED_SALES_SAME_SKU1D,self::ATTRIBUTED_SALES_SAME_SKU7D,
        self::ATTRIBUTED_SALES_SAME_SKU14D,self::ATTRIBUTED_SALES_SAME_SKU30D,self::UNITS_SOLD_SAME_SKU1D,
        self::UNITS_SOLD_SAME_SKU7D,self::UNITS_SOLD_SAME_SKU14D,self::UNITS_SOLD_SAME_SKU30D,
        self::KINDLE_EDITION_NORMALIZED_PAGES_READ14D,self::KINDLE_EDITION_NORMALIZED_PAGES_ROYALTIES14D,
        self::SALES_OTHER_SKU7D,self::UNITS_SOLD_OTHER_SKU7D,
        self::CAMPAIGN_BUDGET_CURRENCY_CODE,


        self::CAMPAIGN_NAME,self::CAMPAIGN_BUDGET_TYPE,
        self::CAMPAIGN_BUDGET_AMOUNT,self::CAMPAIGN_STATUS,self::AD_GROUP_NAME,
        self::KEYWORD_TYPE];

    //vŠetky kluče
    public $keys;
    //Kluče na vyber do zašrtnutia
    public $checkBoxKeys;

    public function __construct(bool|string $id = false)
    {
        $this->checkBoxKeys = array_merge(array_keys(self::CHECKED_COL),self::UNCHECKED_COL);
        $this->keys = array_merge(self::KEYS,$this->checkBoxKeys);

        parent::__construct($id);
    }


    /**
     * @var null Atributy
     */
    protected $amazonAdsSpSearchTermId = null;
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

    protected $keyword = null;
    protected $campaignBudgetCurrencyCode = null;
    protected $portfolioId = null;
    protected $searchTerm = null;
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
    protected $id = self::AMAZON_ADS_SP_SEARCH_TERM_ID;
    protected $whereId = self::AMAZON_ADS_SP_SEARCH_TERM_ID;

    protected $getPairKeyKey = null;
    protected $getPairKeyValue = null;

}
<?php

namespace AmazonAdvertisingApi\Table;

use AmazonAdvertisingApi\AmazonAdvertisingApi;
use AmazonAdvertisingApi\ClientV3;
use App\AccountModul\Model\UserTable;
use Micho\Db;
use Micho\Utilities\ArrayUtilities;
use Micho\Utilities\StringUtilities;

/**
 * Trieda pre tabuľku amazon_ads_campaign
 */
class AmazonAdsCampaignTable extends Table
{
    /**
     * Názov Tabuľky
     */
    const AMAZON_ADS_CAMPAIGN_TABLE = 'amazon_ads_campaign';

    /**
     * Konštanty Databázy
     */
    const AMAZON_ADS_CAMPAIGN_ID = 'amazon_ads_campaign_id';
    const CAMPAIGN_ID = 'campaign_id';
    const PORTFOLIO_ID = AmazonAdsPortfolioTable::PORTFOLIO_ID;
    const NAME = 'name';
    const START_DATE = 'start_date';
    const STATE = 'state';
    const TARGETING_TYPE = 'targeting_type';

    const BUDGET_ARRAY_BUDGET = 'budget_array_budget';
    const BUDGET_ARRAY_BUDGET_TYPE = 'budget_array_budget_type';
    const DYNAMIC_BIDDING_ARRAY_PLACEMENT_BIDDING_ARRAY_PLACEMENT = 'dynamic_bidding_array_placement_bidding_array_placement';
    const DYNAMIC_BIDDING_ARRAY_PLACEMENT_BIDDING_ARRAY_PERCENTAGE = 'dynamic_bidding_array_placement_bidding_array_percentage';
    const DYNAMIC_BIDDING_ARRAY_STRATEGY = 'dynamic_bidding_array_strategy';


    /**
     * @var array Kľuče databázi
     */
    protected $keys = [self::AMAZON_ADS_CAMPAIGN_ID,self::CAMPAIGN_ID,self::USER_ID,self::PROFILE_ID,self::PORTFOLIO_ID,
        self::NAME,self::START_DATE,self::STATE,self::TARGETING_TYPE,self::BUDGET_ARRAY_BUDGET,self::BUDGET_ARRAY_BUDGET_TYPE,self::DYNAMIC_BIDDING_ARRAY_PLACEMENT_BIDDING_ARRAY_PLACEMENT,
        self::DYNAMIC_BIDDING_ARRAY_PLACEMENT_BIDDING_ARRAY_PERCENTAGE,self::DYNAMIC_BIDDING_ARRAY_STRATEGY];

    /**
     * @var null Atributy
     */
    protected $amazonAdsCampaignId = null;
    protected $campaignId = null;
    protected $portfolioId = null;
    protected $name = null;
    protected $startDate = null;
    protected $state = null;
    protected $targetingType = null;

    protected $budgetArrayBudget = null;
    protected $budgetArrayBudgetType = null;
    protected $dynamicBiddingArrayPlacementBiddingArrayPlacement = null;
    protected $dynamicBiddingArrayPlacementBiddingArrayPercentage = null;
    protected $dynamicBiddingArrayStrategy = null;

    /**
     * @var Data pre abstraktnú triedu
     */
    protected $table = self::AMAZON_ADS_CAMPAIGN_TABLE;
    protected $id = self::AMAZON_ADS_CAMPAIGN_ID;
    protected $whereId = self::CAMPAIGN_ID;

    protected $getPairKeyKey = self::NAME;
    protected $getPairKeyValue = self::CAMPAIGN_ID;

    protected $getKeys = [self::CAMPAIGN_ID, self::NAME];

}
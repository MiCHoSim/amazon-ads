<?php

namespace AmazonAdvertisingApi\Table;

use App\AccountModul\Model\UserTable;
use App\ApplicationModul\Amazon\Controller\AmazonAdsController;
use Micho\Db;
use Micho\Utilities\ArrayUtilities;
use Micho\Utilities\StringUtilities;

/**
 * Trieda pre tabuľku amazon_ads_keyword
 */
class AmazonAdsKeywordTable extends Table
{
    /**
     * Názov Tabuľky
     */
    const AMAZON_ADS_KEYWORD_TABLE = 'amazon_ads_keyword';

    /**
     * Konštanty Databázy
     */
    const AMAZON_ADS_KEYWORD_ID = 'amazon_ads_keyword_id';
    const KEYWORD_ID = 'keyword_id';
    const CAMPAIGN_ID = AmazonAdsCampaignTable::CAMPAIGN_ID;
    const AD_GROUP_ID = AmazonAdsAdGroupTable::AD_GROUP_ID;
    const KEYWORD_TEXT = 'keyword_text';
    const MATCH_TYPE = 'match_type';
    const STATE = 'state';
    const BID = 'bid';

    /**
     * @var array Kľuče databázi
     */
    protected $keys = [self::AMAZON_ADS_KEYWORD_ID,self::KEYWORD_ID,self::USER_ID,self::PROFILE_ID,self::CAMPAIGN_ID,
        self::AD_GROUP_ID,self::KEYWORD_TEXT,self::MATCH_TYPE,self::STATE,self::BID];

    /**
     * @var null Atributy
     */
    protected $amazonAdsKeywordId = null;
    protected $keywordId = null;
    protected $campaignId = null;
    protected $adGroupId = null;
    protected $keywordText = null;
    protected $matchType = null;
    protected $state = null;
    protected $bid = null;

    /**
     * @var Data pre abstraktnú triedu
     */
    protected $table = self::AMAZON_ADS_KEYWORD_TABLE;
    protected $id = self::AMAZON_ADS_KEYWORD_ID;
    protected $whereId = self::KEYWORD_ID;

    protected $getPairKeyKey = self::KEYWORD_TEXT;
    protected $getPairKeyValue = self::KEYWORD_ID;

    protected $getKeys = [self::KEYWORD_ID, self::KEYWORD_TEXT];


}
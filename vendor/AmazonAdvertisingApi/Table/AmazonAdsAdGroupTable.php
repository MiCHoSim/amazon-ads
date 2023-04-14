<?php

namespace AmazonAdvertisingApi\Table;

use AmazonAdvertisingApi\AmazonAdvertisingApi;
use AmazonAdvertisingApi\ClientV3;
use App\AccountModul\Model\UserTable;
use Micho\Db;
use Micho\Utilities\ArrayUtilities;
use Micho\Utilities\StringUtilities;

/**
 * Trieda pre tabuľku amazon_ads_ad_group
 */
class AmazonAdsAdGroupTable extends Table
{
    /**
     * Názov Tabuľky
     */
    const AMAZON_ADS_AD_GROUP_TABLE = 'amazon_ads_ad_group';

    /**
     * Konštanty Databázy
     */
    const AMAZON_ADS_AD_GROUP_ID = 'amazon_ads_ad_group_id';
    const AD_GROUP_ID = 'ad_group_id';
    const CAMPAIGN_ID = AmazonAdsCampaignTable::CAMPAIGN_ID;
    const NAME = 'name';
    const DEFAULT_BID = 'default_bid';
    const STATE = 'state';

    /**
     * @var array Kľuče databázi
     */
    protected $keys = [self::AMAZON_ADS_AD_GROUP_ID,self::AD_GROUP_ID,self::USER_ID,self::PROFILE_ID,self::CAMPAIGN_ID,self::NAME,
        self::DEFAULT_BID,self::STATE];

    /**
     * @var null Atributy
     */
    protected $amazonAdsAdGroupId = null;
    protected $adGroupId = null;
    protected $name = null;
    protected $campaignId = null;
    protected $defaultBid = null;
    protected $state = null;

    /**
     * @var Data pre abstraktnú triedu
     */
    protected $table = self::AMAZON_ADS_AD_GROUP_TABLE;
    protected $id = self::AMAZON_ADS_AD_GROUP_ID;
    protected $whereId = self::AD_GROUP_ID;

    protected $getPairKeyKey = self::NAME;
    protected $getPairKeyValue = self::AD_GROUP_ID;

    protected $getKeys = [self::AD_GROUP_ID, self::NAME];

}
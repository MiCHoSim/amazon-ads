<?php

namespace AmazonAdvertisingApi\Table;

use AmazonAdvertisingApi\ClientV3;
use Micho\Db;
use Micho\Utilities\ArrayUtilities;
use Micho\Utilities\StringUtilities;

/**
 * Trieda pre tabuľku amazon_ads_profile
 */
class TimeUnitTable extends Table
{
    /**
     * Názov Tabuľky
     */
    const TIME_UNIT_TABLE = 'time_unit';

    /**
     * Konštanty Databázy
     */
    const TIME_UNIT_ID = 'time_unit_id';
    const NAME = 'name';
    const TIME_UNIT_NAME = 'time_unit_name';

    /**
     * @var array Kľuče
     */
    protected $keys = [self::TIME_UNIT_ID,self::NAME,self::TIME_UNIT_NAME];

    /**
     * @var null Atributy
     */
    protected $timeUnitId = null;
    protected $name = null;
    protected $timeUnitName = null;

    /**
     * @var Data pre abstraktnú triedu
     */
    protected $table = self::TIME_UNIT_TABLE;
    protected $id = self::TIME_UNIT_ID;
    protected $whereId = self::TIME_UNIT_NAME;

    protected $getPairKeyKey = self::NAME;
    protected $getPairKeyValue = self::TIME_UNIT_NAME;

    protected $getKeys =[self::NAME,self::TIME_UNIT_NAME];

    /**
     * @return null
     */
    public function getTimeUnitId()
    {
        return $this->timeUnitId;
    }
}
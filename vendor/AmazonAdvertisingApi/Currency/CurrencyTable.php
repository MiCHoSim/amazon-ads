<?php

namespace AmazonAdvertisingApi\Currency;

use AmazonAdvertisingApi\ClientV3;
use AmazonAdvertisingApi\Connection\Connection;
use AmazonAdvertisingApi\DataCollection\Profile;
use AmazonAdvertisingApi\Table\Data;
use AmazonAdvertisingApi\Table\SelectDateTable;
use AmazonAdvertisingApi\Table\Table;
use App\AccountModul\Model\UserTable;
use App\ApplicationModul\Amazon\Model\AmazonMonthlySalesManager;
use App\ApplicationModul\AppManagement\Controller\AppManagementController;
use Micho\Db;
use Micho\Utilities\ArrayUtilities;
use Micho\Utilities\StringUtilities;
use Exception;
use const http\Client\Curl\Versions\IDN;

/**
 * Trieda pre tabuľku amazon_ads_profile
 */
class CurrencyTable extends Table
{
    /**
     * Názov Tabuľky
     */
    const TABLE = 'currency';

    /**
     * Konštanty Databázy
     */
    const CURRENCY_ID = 'currency_id';
    const DOWNLOAD_DATE = 'download_date';
    const FROM_CURRENCY = 'from_currency';
    const TO_CURRENCY = 'to_currency';
    const RATE = 'rate';

    /**
     * @var array Kľuče
     */
    protected $keys = [self::CURRENCY_ID,self::DOWNLOAD_DATE,self::FROM_CURRENCY,self::TO_CURRENCY,self::RATE];

    /**
     * @var null Atributy
     */
    protected $currencyId = null;
    protected $downloadDate = null;
    protected $fromCurrency = null;
    protected $toCurrency = null;
    protected $rate = null;

    /**
     * @var Data pre abstraktnú triedu
     */
    protected $table = self::TABLE;
    protected $id = self::CURRENCY_ID;
    protected $whereId = self::CURRENCY_ID;

    protected $getPairKeyKey = self::CURRENCY_ID;
    protected $getPairKeyValue = self::RATE;
}
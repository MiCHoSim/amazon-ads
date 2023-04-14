<?php

namespace AmazonAdvertisingApi\Table;

use AmazonAdvertisingApi\ClientV3;
use Micho\Db;
use Micho\Utilities\ArrayUtilities;
use Micho\Utilities\StringUtilities;

/**
 * Trieda pre tabuľku amazon_ads_profile
 */
class SelectDateTable extends Table
{
    /**
     * Názov Tabuľky
     */
    const SELECT_DATE_TABLE = 'select_date';

    /**
     * Konštanty Databázy
     */
    const SELECT_DATE_ID = 'select_date_id';
    const SELECT_START_DATE = 'select_start_date';
    const SELECT_END_DATE = 'select_end_date';

    /**
     * @var array Kľuče
     */
    protected $keys = [self::SELECT_DATE_ID,self::SELECT_START_DATE,self::SELECT_END_DATE];

    /**
     * @var null Atributy
     */
    protected $selectDateId = null;
    protected $selectStartDate = null;
    protected $selectEndDate = null;

    /**
     * @var Data pre abstraktnú triedu
     */
    protected $table = self::SELECT_DATE_TABLE;
    protected $id = self::SELECT_DATE_ID;
    protected $whereId = self::SELECT_DATE_ID;

    protected $getPairKeyKey = 'selected_date';
    protected $getPairKeyValue = self::SELECT_DATE_ID;

    protected $getKeys =[self::SELECT_DATE_ID,
        'CONCAT(COALESCE(' . self::SELECT_START_DATE . ', ""), " | ", COALESCE(' . self::SELECT_END_DATE . ', "")) AS selected_date'
    ];

    public function save(array $data, bool $updateOld = false): string
    {
        $data = ArrayUtilities::filterKeys($data, $this->keys);

        // ON DUPLICATE UPDATE nefunguje tam automaticke vrateni ID takže najskor overim a ak nieje tak pridam nový zaznam
        $selectDateId = $this->selectDateId($data[self::SELECT_START_DATE], $data[self::SELECT_END_DATE]);

        if ($selectDateId)
            return $selectDateId;
        else
            return Db::insert(self::SELECT_DATE_TABLE, $data);
    }

    /**
     ** Načita Id záznamu dátumu
     * @param string $startDate začaitok datumu
     * @param string $endDate koniec dátumu
     * @return string|false
     */
    public function selectDateId(string $startDate, string $endDate) : string|false
    {
        $selectDateId =  Db::queryOneRow('SELECT ' . self::SELECT_DATE_ID . '  
                            FROM ' . self::SELECT_DATE_TABLE . ' 
                            WHERE ' . self::SELECT_START_DATE . ' = ? AND ' . self::SELECT_END_DATE . ' = ?', [$startDate,$endDate]);
        return $selectDateId ? $selectDateId[self::SELECT_DATE_ID] : false;
    }
}
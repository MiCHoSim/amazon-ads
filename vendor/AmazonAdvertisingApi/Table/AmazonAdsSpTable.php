<?php

namespace AmazonAdvertisingApi\Table;

use AmazonAdvertisingApi\DataCollection\Portfolio;
use App\AccountModul\Model\UserTable;
use App\ApplicationModul\Amazon\Controller\AmazonAdsController;
use App\ApplicationModul\AppManagement\Controller\AppManagementController;
use Micho\Db;
use Micho\Utilities\ArrayUtilities;
use Micho\Utilities\StringUtilities;

/**
 * Rozhranie pre tabuľky na stahovenie údajov z amazon ads
 */
abstract class AmazonAdsSpTable extends Table
{
    /**
     * Konštanty Databázy
     */
    const SELECT_DATE_ID = SelectDateTable::SELECT_DATE_ID;
    const TIME_UNIT_ID = TimeUnitTable::TIME_UNIT_ID;

    //private $thisClass;

    public function __construct(bool|string $id = false)
    {
        //$this->thisClass = get_class($this);
        parent::__construct($id);
    }

    /**
     *
     * @param string $id
     * @return mixed
     */
    /**
     ** Načita data z DB
     * @param array $where Pole podmienky where
     * @param array $keys kluče ktore načitávam
     * @param bool $bidi Či chcem načitavať aj bidy
     * @return mixed
     */
    public function getReports(array $where, array $keys,bool $bidi = true) : mixed
    {
        $whereKeys = array_keys($where);
        $whereValues = array_values($where);
        $joinQuery = '';

        $whereQuery = ' WHERE ' . implode(' = ? AND ',$whereKeys) . ' = ?';
        $keys = array_merge([SelectDateTable::SELECT_START_DATE,SelectDateTable::SELECT_END_DATE],$keys);
        $selectedKeys = $keys;

        // načitavam aj bidy
        if($this instanceOf AmazonAdsSpTargetingTable && $bidi)
        {
            $keys = $this->prepareKeys($keys);
            $joinQuery .= $this->prepareJoins();
        }
        $selectQuery = 'SELECT ' . implode(', ', $keys);
        $fromQuery = ' FROM ' . $this->table;
        $joinQuery .= ' JOIN ' . SelectDateTable::SELECT_DATE_TABLE . ' USING (' . $this::SELECT_DATE_ID . ')';

        $orderQuery = ' ORDER BY ' . SelectDateTable::SELECT_START_DATE . ',' . SelectDateTable::SELECT_END_DATE . ',' . $this::CLICKS . ' DESC';

        $data = Db::queryAllRows($selectQuery . $fromQuery . $joinQuery . $whereQuery . $orderQuery,$whereValues);

        // načitavam aj bidy
        if($this instanceOf AmazonAdsSpTargetingTable && $bidi)
            $data = $this->editBids($data,$selectedKeys);

        // Načitanie dátumov z dôvodu keď k niektoremu z dátumov neexistuje bid ale chcu to vypisať ako enabled
        $dates = Db::queryAllRows('
            SELECT ' . SelectDateTable::SELECT_START_DATE . ',' .  SelectDateTable::SELECT_END_DATE .
            $fromQuery .
            ' JOIN ' . SelectDateTable::SELECT_DATE_TABLE . ' USING (' . $this::SELECT_DATE_ID . ')' .
            ' WHERE ' . self::USER_ID . ' = ? AND ' . self::PROFILE_ID . ' = ?  
            GROUP BY ' . self::SELECT_DATE_ID . ' 
            ORDER BY ' . SelectDateTable::SELECT_START_DATE . ',' . SelectDateTable::SELECT_END_DATE . ' DESC'
            , [$where[self::USER_ID], $where[self::PROFILE_ID]]);

        $vystup = [];
        foreach ($dates as $key => $date)
        {
            $vystup[$key]['date'] = $date;
            foreach ($data as $row)
            {
                if(array_search($date[SelectDateTable::SELECT_START_DATE],$row) !== false && array_search($date[SelectDateTable::SELECT_END_DATE],$row) !== false)
                {
                    array_shift($row);
                    array_shift($row);
                    $vystup[$key]['data'][] = $row;
                }
            }
        }
/*
        print_r($dates);echo "<br><br>";
        AmazonAdsController::view($data);echo "<br><br>";
        AmazonAdsController::view($vystup);
*/

        return $vystup;
    }

    /**
     ** Vytvory púodmienku pre načitanie reportu
     * @param string $userId Id uživatela
     * @param string $profileId Id profilu
     * @param string $portfolioId Id portfolia
     * @param string $campaignId Id kampane
     * @param string $adGroupId Id reklamenej skupiny
     * @return array Pole hodnôt
     */
    public function createWhere(string $userId, string $profileId, string $portfolioId, string $campaignId, string$adGroupId) : array
    {
        return [self::USER_ID => $userId, self::PROFILE_ID =>$profileId, $this::PORTFOLIO_ID => $portfolioId,
            $this::CAMPAIGN_ID => $campaignId, $this::AD_GROUP_ID => $adGroupId];
    }

    /**
     ** Vráti dátumi v ktorach uživateľ stahoval reporty v danej krajine
     * @param string $userId Id uzivatela
     * @param string $profileId Id profilu
     * @return array|false|null
     */
    public function getDownlaodingDates(string $userId, string $profileId)
    {
        $keys = [SelectDateTable::SELECT_START_DATE, SelectDateTable::SELECT_END_DATE, self::SELECT_DATE_ID];

        $dates = Db::queryAllRows('
                SELECT ' . implode(', ', $keys) . '
                FROM ' . $this->table . '
                JOIN ' . SelectDateTable::SELECT_DATE_TABLE . ' USING (' . self::SELECT_DATE_ID . ')
                WHERE ' . self::USER_ID . ' = ? AND  ' . self::PROFILE_ID . ' = ?
                GROUP BY ' . self::SELECT_DATE_ID . '
                ORDER BY  ' . SelectDateTable::SELECT_START_DATE . ',  ' . SelectDateTable::SELECT_END_DATE, [$userId,$profileId]);

        return $dates ? $dates : false;
    }

}
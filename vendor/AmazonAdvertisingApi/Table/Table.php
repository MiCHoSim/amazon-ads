<?php

namespace AmazonAdvertisingApi\Table;

use AmazonAdvertisingApi\DataCollection\Portfolio;
use App\AccountModul\Model\UserTable;
use App\ApplicationModul\AppManagement\Controller\AppManagementController;
use Micho\Db;
use Micho\Utilities\ArrayUtilities;
use Micho\Utilities\StringUtilities;

/**
 * Rozhranie pre tabuľky na stahovenie údajov z amazon ads
 */
abstract class Table
{
    /**
     * Konštanty Databázy
     */
    const PROFILE_ID = 'profile_id';
    const USER_ID = UserTable::USER_ID;

    /**
     * @var null Atributy
     */
    protected $profileId = null;
    protected $userId = null;

    /**
     * @var array Pole Atribútov
     */
    protected array $arrayData;

    /**
     ** Konštruktor, načita konkrétne dáta z DB
     * @param bool|string $id záznamu podľa nazvu profileId,portfolioId,...
     */
    public function __construct(bool|string $id = false)
    {
        $this->userId = UserTable::$user[UserTable::USER_ID];
        if ($id)
        {
            $data =  Db::queryOneRow('SELECT * FROM '. $this->table . ' 
                                            WHERE ' . $this->whereId . ' = ?', array($id));

            $this->setAtributes($data);
        }
        $this->setArrayData();
    }

    /**
     ** Načita data z DB
     * @param string $id
     * @return mixed
     */
    public function get(array|null $where = null, array|null $keys = null): mixed
    {
        $filterKeys = $keys;
        if(empty($keys))
        {
            $keys = $this->getKeys;
            $filterKeys = [$this->getPairKeyKey,$this->getPairKeyValue];
        }

        if(!empty($where))
        {
            $whereKeys = array_keys($where);
            $whereValues = array_values($where);

            $whereQuery = ' WHERE ' . implode(' = ? AND ',$whereKeys) . ' = ?';
        }
        else
        {
            $whereQuery = ' ';
            $whereValues = array();
        }

       return Db::queryAllRows('SELECT ' . implode(', ', $keys) .
            ' FROM ' . $this->table . $whereQuery . ' ORDER BY ' . $this->id, $whereValues);
    }

    /**
     ** Vráti páry pre zobrazenie v Select form
     * @param string $id
     * @return array|false
     */
    public function getPair(array|null $where = null, array|null $keys = null): array|false
    {
        return ArrayUtilities::getPairs($this->get($where, $keys),$this->getPairKeyKey, $this->getPairKeyValue);
    }


    /**
     ** Uloži načitané data do Databázi
     * @param array $data $data Data na uloŽenie
     * @param bool $updateOld Či chem v pripade dupkikatu upraviŤ starý
     * @return string
     */
    public function save(array $data, bool $updateOld = true): string
    {
        $data = ArrayUtilities::filterKeys($data, $this->keys);

        return Db::insert($this->table, $data, $updateOld);
    }

    /**
     ** automaticke ulozenie hodnot do premenných atributov
     * @param array $data Data na vyplnenie atribitou
     * @return void
     */
    public function setAtributes(array $data)
    {
        foreach ($data as $key => $dat)
        {
            $nameAtribute = StringUtilities::underlineToCamel($key);
            $this->$nameAtribute = $dat; //ulozi hodnotu atributu
        }
    }

    /**
     ** Vloži data do poľa
     * @return void
     */
    private function setArrayData(): void
    {
        // automaticke naplneni hodnot
        foreach ($this->keys as $key)
        {
            $nameAtribute = StringUtilities::underlineToCamel($key);
            $this->arrayData[$key] = $this->$nameAtribute;
        }
    }

    /**
     * @return array
     */
    public function getArrayData(): array
    {
        return $this->arrayData;
    }

}
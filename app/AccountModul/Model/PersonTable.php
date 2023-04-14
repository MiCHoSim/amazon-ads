<?php

namespace App\AccountModul\Model;

use Micho\Db;
use mysql_xdevapi\TableUpdate;

/**
 * Class PersonManager
 * @package App\AccountModul\Model
 */
class PersonTable
{
    /**
     * Názov Tabuľky pre Spracovanie Osoby detail
     */
    const PERSON_TABLE = 'person';

    /**
     * Konštanty Databázy 'osoba_detail'
     */
    const PERSON_ID = 'person_id';
    const PERSON_DETAIL_ID = PersonDetailTable::PERSON_DETAIL_ID;
    const USER_ID = UserTable::USER_ID;

    public static $personDataKeys = array(); // a tak ďalej


    public function savePerson($data) : int
    {
        return Db::insert(self::PERSON_TABLE, $data);
    }

    public function updatePerson($data)
    {
       // Db::update(self::PERSON_DETAIL_TABLE, $data, 'WHERE ' . self::PERSON_DETAIL_ID . ' = ?', array($personDetailId));
    }



    public function getPersonData()
    {
    }

}
/*
 * Autor: MiCHo
 */
<?php

namespace App\AccountModul\Model;

use App\ZakladModul\Kontroler\EmailController;
use Micho\Form\Form;
use Micho\ValidationException;
use Micho\Db;
use Micho\UserException;

use PDOException;

/**
 * Class PersonDetailManager
 * @package App\AccountModul\Model
 */
class PersonDetailTable
{
    /**
     * Názov Tabuľky pre Spracovanie Osoby detail
     */
    const PERSON_DETAIL_TABLE = 'person_detail';

    /**
     * Konštanty Databázy 'osoba_detail'
     */
    const PERSON_DETAIL_ID = 'person_detail_id';
    const NAME = 'name';
    const LAST_NAME = 'last_name';
    const TEL = 'tel';

    public static $personDetailDataKeys = array(self::NAME, self::LAST_NAME, self::TEL); // a tak ďalej

    /**
     ** uloži nový detail uživateľa do DB
     * @param array $data Data nma uloženie
     * @return int Id detailu osoby
     */
    public function savePersonDetail(array $data) : int
    {
        return Db::insert(self::PERSON_DETAIL_TABLE, $data);
    }

    /**
     ** upravy existujucej osoby
     * @param array $data Nové dáta na preuloženie
     * @param string $personDetailId d detailu uživateˇal koreho preukladam
     * @return void
     */
    public function updatePersonDetail(array $data, string $personDetailId)
    {
        Db::update(self::PERSON_DETAIL_TABLE, $data, 'WHERE ' . self::PERSON_DETAIL_ID . ' = ?', array($personDetailId));
    }

    /**
     ** Načitá osobné údaje z DB
     * @param $userId ID uživateľa
     * @param array $keys Poľe hodnot ktore chem z Db načitať
     * @return mixed Osobné údaje uživateľa
     */
    public function getPersonalData($userId, array $keys = array())
    {
        $keys = empty($keys) ? array_merge(array(self::PERSON_DETAIL_ID), self::$personDetailDataKeys) : $keys;
        $data = Db::queryOneRow('SELECT ' . implode(', ', $keys) . '
                                          FROM ' . self::PERSON_DETAIL_TABLE . '
                                          JOIN ' . PersonTable::PERSON_TABLE . ' USING ('. self::PERSON_DETAIL_ID .')
                                          WHERE ' . UserTable::USER_ID . ' = ?', array($userId));
        return is_array($data) ? array_intersect_key($data, array_flip($keys)) : $data;
    }

    /**
     ** Vymaže starý detail osoby, v prípade že sa neviaže na inú Tabuľku
     * @param int $osobaDetailId ID detailu osoby
     */
    public function vymazOsobaDetail($osobaDetailId)
    {
        try
        {
            Db::query('DELETE FROM osoba_detail WHERE osoba_detail_id = ?', array($osobaDetailId));
        }
        catch (PDOException $chy){} // položku sa nepodarilo odstrániť, pretože je napojená na inú tabuľku
    }

    /**
     ** Vráti email uživateľa
     * @param int $uzivatelId id uzivatela ktorej chem ziskať email
     * @return false|mixed email uživateľa
     */
    public function vratEmail($uzivatelId)
    {
        $email = Db::queryAlone('SELECT email
                                    FROM osoba_detail
                                    JOIN osoba USING (osoba_detail_id)
                                    WHERE uzivatel_id = ?', array($uzivatelId));
        return $email;
    }


}
/*
 * Autor: MiCHo
 */
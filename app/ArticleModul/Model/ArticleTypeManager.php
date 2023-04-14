<?php

namespace App\ArticleModul\Model;

use App\AdministraciaModul\Uzivatel\Model\OsobaManazer;
use App\AdministraciaModul\Uzivatel\Model\UserManager;
use Micho\UserException;
use Micho\Db;
use Micho\Obrazok;
use Micho\Files\Folder;
use Micho\Utilities\ArrayUtilities;
use Micho\Utilities\StringUtilities;
use PDOException;

/**
 * Class ClanokTypManazer
 * @package App\ClanokModul\Model
 */
class ArticleTypeManager
{
    /**
     * Názov Tabuľky pre Spracovanie člankov
     */
    const ARTICLE_TYPE_TABLE = 'article_type';

    /**
     * Konštanty Databázy 'clanok'
     */
    const ARTICLE_TYPE_ID = 'article_typ_id';
    const TITLE = 'title';
    const URL = 'url';

    /**
     * ID typov Článkov
     */
    const CLANOK_INFORMACIA = 1;
    const CLANOK_UVOD = 2;
    const CLANOK_SLUZBA = 3;
    const CLANOK_TRENING = 4;
    const CLANOK_STRAVA = 5;

    const TYPY_CLANKOV_URL_ID = array('clanok' => self::CLANOK_INFORMACIA, 'uvod' => self::CLANOK_UVOD, 'sluzba' => self::CLANOK_SLUZBA, 'trening' => self::CLANOK_TRENING, 'strava' => self::CLANOK_STRAVA);

    const TYPY_CLANKOV_URL_NAZOV = array('clanok' => 'Článok informácia', 'uvod' => 'Úvod', 'sluzba' => 'Služba', 'trening' => 'Tréning', 'strava' => 'Strava');


    /**
     ** Načíta všetky možnosti typov článkov ako pár Nazov=>Id
     * @return array uložené krajiny
     */
    public function vratTypyClankovNazovId()
    {
        return Db::dopytPary('SELECT clanok_typ_id, nazov FROM clanok_typ ORDER BY clanok_typ_id', self::TITLE, self::ARTICLE_TYPE_ID);
    }

    /**
     ** Načíta všetky možnosti typov článkov ako pár Nazov=>url
     * @return array uložené krajiny
     */
    public function vratTypyClankovNazovUrl()
    {
        return Db::dopytPary('SELECT url, nazov FROM clanok_typ ORDER BY nazov', self::TITLE, self::URL);
    }
}
/*
 * Autor: MiCHo
 */
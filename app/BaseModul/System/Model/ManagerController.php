<?php


namespace App\BaseModul\System\Model;

use Micho\Db;
use Micho\UserException;
use Micho\Utilities\ArrayUtilities;

use PDOException;


/**
 ** Trieda poskytuje metódy pre správu kontrolérov v redakčnom systéme
 * Class SpravaPoziadaviekManazer
 * @package App\ZakladModul\SpravaPoziadaviek\Model
 */
class ManagerController
{
    /**
     * Názov Tabuľky pre Spracovanie Kontrolérov
     */
    const TABLE_NAME = 'controller';

    /**
     * Konštanty Databázy 'kontroler'
     */
    const CONTROLLER_ID = 'controller_id';
    const TITLE = 'title';
    const URL = 'url';
    const DESCRIPTION = 'description';
    const CONTROLLER_PATH = 'controller_path';

    /**
     ** Načíta kontrolér z Db a uloží ho do statickej vlastnosti $kontroler
     * @param string $url Url kontroléra
     */
    public function loadController(string $url)
    {
        $keys = array (self::CONTROLLER_ID, self::TITLE, self::URL, self::DESCRIPTION, self::CONTROLLER_PATH); // názvy stĺpcov,ktoré chcem z tabuľky načitať

       return $this->getController($url, $keys);
    }

    /**
     ** Vráti kontrolér z db podľa jeho URL
     * @param string $url Url kontroléra
     * @param array $keys Klúče Ktoré chcem načitať
     * @return array|mixed Pole s kontrolérom alebo FALSE pri neúspechu
     */
    public function getController(string $url, array $keys) :array|false
    {
        return Db::queryOneRow('SELECT ' . implode(', ',$keys) . ' 
                                    FROM ' . self::TABLE_NAME . ' WHERE url = ?', array($url));
    }

    /**
     ** Uloži Kontroler. Pokiaľ je id false, Vloží nový, inak vykona editáciu
     * @param array $kontroler Pole s Kontrolérom
     * @throws UserException
     */
    public function ulozKontroler($kontroler)
    {
        if(!$kontroler[self::CONTROLLER_ID])
        {
            unset($kontroler[self::CONTROLLER_ID]); // aby prebehol autoinkrement, hodnota musi byť NULL, alebo stĺpec z dopytu musíme vynechať
            try
            {
                Db::insert(self::KONTROLER_TABULKA, $kontroler);
                return 'Kontrolér bol úspešne uložený.';
            }
            catch (PDOException $ex)
            {
                throw new UserException('Kontrolér s touto URL adresov už existuje');
            }
        }
        else
        {
            Db::update(self::KONTROLER_TABULKA, $kontroler, 'WHERE kontroler_id = ?', array($kontroler[self::CONTROLLER_ID]));
            return 'Kontrolér bol aktuálizovaný.';
        }
    }

    /**
     ** Vráti zoznam kontrolérov v db
     * @return mixed Zoznam kontrolérov
     */
    public function vratKontrolery()
    {
        return Db::queryAllRows('SELECT kontroler_id, titulok, url, popisok, kontroler
                                      FROM kontroler ORDER BY kontroler_id DESC ');
    }

    /**
     ** Odstráni kontrolér
     * @param string $url URL kontoléru
     */
    public function odstranKontroler($url)
    {
        Db::query('DELETE FROM kontroler WHERE url = ?', array($url));
    }
}
/*
 * Tento kód spadá pod licenci ITnetwork Premium - http://www.itnetwork.cz/licence
 * Je určen pouze pro osobní užití a nesmí být šířen ani využíván v open-source projektech.
 */

/*
 * Niektoré časti sú upravené
 * Autor: MiCHo
 */
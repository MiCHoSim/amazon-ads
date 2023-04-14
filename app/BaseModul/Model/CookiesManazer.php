<?php

namespace App\ZakladModul\Model;

use App\ClanokModul\Model\ArticleManazer;
use Micho\Db;
use Settings;

/**
 ** Trieda poskytuje metódy pre správu Cookies
 * Class CookiesManazer
 * @package App\ZakladModul\Model
 */
class CookiesManazer
{
    /**
     * Konštanty možnosti výberu typu ulozenie/ zobrazenie cookies ich nazvy
     **/
    const COOKIES = 'cookies';
    const UVOD_INFO = 'uvodne-info';
    const UVOD_INFO_INSTRUKCIE = 'instrukcie';

    /**
     * Vráťi popisok Teda Info o Cookias
     */
    public function vratPopisokCookies()
    {
        return Db::queryOneRow('SELECT popisok FROM kontroler WHERE url = "cookies"')[ArticleManazer::DESCRIPTION];
    }

    /**
     ** Zisti či uzvateľ zaklikol súhlas s použivaním cookies
     * @return bool Či sú cookies info uložené
     */
    public function vratUlozeneCookies($nazov)
    {
        if (isset($_COOKIE[$nazov]) && $_COOKIE[$nazov] === 'true')
                return true;
        return false;
    }

    /**
     * Uloží, potrvrdenie videnia do Cookies
     * @param string $nazov Názov uloženia Cookies
     */
    public function ulozCookies($nazov)
    {
        setcookie($nazov, 'true', time() + (3600 * 24 * 365), '/', '', null, true);
    }
}
/*
 * Autor: MiCHo
 */
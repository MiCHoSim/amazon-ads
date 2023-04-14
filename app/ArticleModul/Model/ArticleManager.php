<?php

namespace App\ArticleModul\Model;

use App\AdministraciaModul\Uzivatel\Model\PersonDetailManazer;
use App\AdministraciaModul\Uzivatel\Model\OsobaManazer;
use App\AdministraciaModul\Uzivatel\Model\UserManager;
use Micho\Exception\UserException;
use Micho\Db;
use Micho\Obrazok;
use Micho\Files\Folder;
use Micho\Files\File;
use Micho\Utilities\ArrayUtilities;
use Micho\Utilities\StringUtilities;
use PDOException;

/**
 ** Správca Článkov webu
 * Class ClanokManazer
 * @package App\ClanokModul\Model
 */
class ArticleManager
{
    /**
     * Názov Tabuľky pre Spracovanie člankov
     */
    const ARTICLE_TABLE = 'article';

    /**
     * Konštanty Databázy 'clanok'
     */
    const ARTICLE_ID = 'article_id';
    const TITLE = 'title';
    const ARTICLE_TYPE_ID = ArticleTypeManager::ARTICLE_TYPE_ID;
    const CONTENTS = 'contents';
    const URL = 'url';
    const LINK = 'link';
    const DESCRIPTION = 'description';
    const PUBLIC = 'public';
    const AUTHOR_ID = 'author_id';
    const EDITED_AUTHOR_ID = 'edited_author_id';
    const CREATION_DATE = 'creation_date';
    const MODIFICATION_DATE = 'modification_date';


    /**
     ** Zisti Či je článok možne zobraziť
     * @param string $url Url článku
     * @return bool Či je verejny
     */
    public function zistiVerejnostClanku($url)
    {
        return (bool) Db::queryAlone('SELECT verejny FROM clanok WHERE clanok.url = ?', array($url));
    }

    /**
     ** Vráti zoznam Článkov v db podla jeho typu
     * @param false $clanokTypUrl Url typu článku ktorý chem vrátit
     * @return array|mixed
     */
    public function vratClankyZoznam($clanokTypUrl = false)
    {
        $kluce = array('clanok_id', 'titulok', 'nazov', 'url', 'popisok','verejny', 'verejny_nazov', 'typ_url');

        $dopyt = 'SELECT clanok_id, titulok, nazov, clanok.url, popisok, verejny, IF(verejny, "Verejný", "Skrytý") AS verejny_nazov, 
                                           IF(clanok_typ.url = "trening" OR clanok_typ.url = "strava", "clanok", clanok_typ.url) AS typ_url
                                            FROM clanok 
                                            JOIN clanok_typ USING (clanok_typ_id) ';
        $parametre = array();
        if ($clanokTypUrl)
        {
            $dopyt .= 'WHERE clanok_typ.url = ? ';
            $parametre = array($clanokTypUrl);
        }

        $dopyt .= 'ORDER BY clanok_typ_id, nazov DESC ';

        return Db::queryAllRows($dopyt, $parametre);
    }

    /**
     ** Vráti zoznam Článkov v db podla jeho typu pre karty a teda clanky pre treningy a tak
     * @param false $clanokTypUrl Url typu článku ktorý chem vrátit
     * @return array|mixed
     */
    public function vratClankyZoznamKarty($clanokTypUrl)
    {
        $kluce = array('clanok_id', 'titulok', 'clanok.url', 'popisok', 'datum_vytvorenia');

        $dopyt = 'SELECT ' . implode(', ',$kluce) . '
                  FROM clanok 
                  JOIN clanok_typ USING (clanok_typ_id)
                  WHERE verejny AND clanok_typ.url = ?
                  ORDER BY datum_vytvorenia DESC';
        return Db::queryAllRows($dopyt, array($clanokTypUrl));
    }

    /**
     ** Uloži Článok. Pokiaľ je id false, Vloží nový, inak vykona editáciu
     * @param array $clanok Pole s Článkom
     * @throws UserException
     */
    public function ulozClanok($clanok)
    {
        if(!$clanok[self::ARTICLE_ID])
        {
            unset($clanok[self::ARTICLE_ID]); // aby prebehol autoinkrement, hodnota musí byť NULL, alebo stĺpec z dopytu musíme vynechať
            try
            {
                $clanok[self::AUTHOR_ID] = UserManager::$uzivatel[UserManager::USER_ID];
                Db::insert(self::ARTICLE_TABLE, $clanok);
                return 'Článok bol úspešne uložený.';
            }
            catch (PDOException $ex)
            {
                throw new UserException('Článok s touto URL adresov už existuje');
            }
        }
        else
        {
            $clanok[self::EDITED_AUTHOR_ID] = UserManager::$uzivatel[UserManager::USER_ID];
            $clanok[self::PUBLIC] = isset($clanok[self::PUBLIC]) ? : 0; // ak nieje zaškrtnuté tak sa neodosiela a preto ho musím prepisať na 0
            Db::update(self::ARTICLE_TABLE, $clanok, 'WHERE clanok_id = ?', array($clanok[self::ARTICLE_ID]));
            return 'Článok bol aktualizovaný.';
        }
    }

    /**
     ** Vráti článok z db podľa jeho URL
     * @param array $urls pole Url článkov
     * @param array $keys Klúče Ktoré chcem načitať
     * @return array|mixed Pole s článkom alebo FALSE pri neúspechu
     */
    public function loadArticles(array $urls, array $keys)
    {
        $querySelect = 'SELECT ' . implode(', ',$keys) . ', IF(public, "Public", "Hiddne") AS public_title FROM ' . self::ARTICLE_TABLE;

        $queryWhere = ' WHERE url = ? ';

        for($i=1; $i < count($urls); $i++)
        {
            $queryWhere .= ' || url = ? ';
        }

        $data = Db::queryAllRows($querySelect . $queryWhere, $urls);

        if(empty($data))
            throw new UserException('The article with the given url address does not exist');

        return $data;
    }
    /**
     ** Odstráni Článok
     * @param string $url URL článku
     */
    public function odstranClanok($url)
    {
        Db::query('DELETE FROM clanok WHERE url = ?', array($url));

        //vymaze priecinok aj z fotkami
        $src= 'obrazky/clanky/' . $url;
        Folder::deleteFolder($src);
    }

    /**
     ** Vráti zakladné info pre článok Na zobrazenie odkazu
     * @param string $clanokTypId url typu članku, ktorý chem zobraziť
     * @param bool $verejny Či chem zobraziť iba verejné články
     * @return array|mixed
     */
    public function vratClanky($clanokTypId = false, $vsetkyVerejne = true)
    {
        $kluce = array(self::TITLE, self::DESCRIPTION, self::URL);
        $parametre = array();

        $dopyt = 'SELECT titulok, clanok.url, popisok
                  FROM clanok 
                    ';
        if ($clanokTypId)
        {
            $dopyt .= 'WHERE clanok_typ_id = ? ';
            $parametre[] = $clanokTypId;
        }
        if ($vsetkyVerejne)
        {
            $dopyt .= 'AND verejny ';
        }
        $dopyt .= 'ORDER BY clanok_typ_id';

        return Db::queryAllRows($dopyt, $parametre);
    }

    /**
     ** Vráti zakladné info pre článok Na zobrazenie odkazu podla url
     * @param array $url pole clankov na nacitanie
     * @param bool $verejny Či chem zobraziť iba verejné články
     * @return array|mixed
     */
    public function vratClankyUrl(array $url, $vsetkyVerejne = true)
    {
        $kluce = array(self::TITLE, self::DESCRIPTION, self::URL);

        $dopyt = 'SELECT titulok, clanok.url, popisok
                  FROM clanok 
                    ';

        $dopyt .= 'WHERE ';

        for($i = 0 ; $i < count($url) -1 ; $i++)
        {
            $dopyt .= 'url = ? OR ';
        }
        $dopyt .= 'url = ? ';


        if ($vsetkyVerejne)
        {
            $dopyt .= 'AND verejny ';
        }

        return Db::queryAllRows($dopyt, $url);
    }



    /**
     ** Spracovanie obrázka ktorý príde v obsahu článku cez initTinyMce
     * @param array $clanok Pole z článkom
     * @param string $cesta cesta k ulozeniu obrazka
     * @return mixed|string|string[] Nový obsah pre uloženie do DB
     */
    public function ulozObrazky($clanok, $cesta)
    {
        $obsah = $clanok[self::CONTENTS];

        // robim to cez explode lebo to ide rychlejsie ako ked som to robil cez mb_substr(
        $castiObrazok = explode('<img ', $obsah); // rozsekanie retazca na podla img

        //Priecinok::vymazPriecinok($srcNove); // vŽdy vymaže rpeičinok a obrazky uloži nanovo .. nevimazujem rpetoze mi tov ymqaze aj titulny obrazok

        if (isset($castiObrazok[1]))
        {
            // Zostavenie nového src atributu pre tágu img pre obsah na uloženie do Databázy
            //Priecinok::vymazPriecinok($srcNove); nevimazujem rpetoze mi tov ymqaze aj titulny ob vymazem to v kontorley na zaciatku
            Folder::createFolder($cesta);
            foreach ($castiObrazok as $kluc => $cast)   // prechadzanie časti obrazkov
            {
                if (mb_strpos($cast, 'title=') !== false) // ak pole neobsahuje reťazec 'title=' znamená to, že to nieje obrazok
                {
                    $obrazok = explode(' />', $cast)[0]; // rozesekane pola podla /> a teda najdenie ukoncenia img a vratenie iba obrázka

                    $titulok = StringUtilities::returnStringBetween($obrazok,'title="', '"');
                    $src = StringUtilities::returnStringBetween($obrazok,'src="', '"');
                    $width = StringUtilities::returnStringBetween($obrazok,'width="', '"');
                    $height = StringUtilities::returnStringBetween($obrazok,'height="', '"');

                    // Nahradenie povodneho src novým ktory sa uloz do databazi z odkazom na obrazok
                    $obsah = str_replace('src="' . $src . '"', 'src="' . $cesta . '/' . $titulok  . '" class="img-fluid" ', $obsah);

                    // uloženie Obrázkov do priečinka
                    $obrazok = new Obrazok($src);
                    $obrazok->zmenRozmery($width, $height);
                    $obrazok->uloz($cesta . '/'. $titulok, $obrazok->vratObrazokTyp());
                }
            }
        }
        return $obsah; // obsah na ulozenie do DB
    }

    /**
     ** Spracovanie obrázka ktorý príde z databázi a chem ho zobraziť v initTinyMce
     * @param array $clanok Pole z článkom
     * @return mixed|string|string[] Nový obsah pre zobrazenie v initTinyMce
     */
    public function nacitajObrazky(array $clanok)
    {
        $obsah = $clanok[self::CONTENTS];

        $castiObrazok = explode('<img ', $obsah); // rozsekanie retazca na podla img

        if (isset($castiObrazok[1]))
        {
            foreach ($castiObrazok as $kluc => $cast)   // prechadzanie časti obrazkov
            {
                if (mb_strpos($cast, 'title=') !== false) // ak pole neobsahuje reťazec 'title=' znamená to, že to nieje obrazok
                {
                    $obrazok = explode(' />', $cast)[0]; // rozesekane pola podla /> a teda najdenie ukoncenia img a vratenie iba obrázka

                    $srcStare = explode('" ', explode('src="', $obrazok)[1])[0];

                    $srcBase64 = Obrazok::vratBase64($srcStare);

                    $obsah = str_replace($srcStare, $srcBase64, $obsah);

                }
            }
        }
        return $obsah; // obsah na ulozenie do DB
    }

    /**
     ** Vráti zakladné info pre články menu
     * @param bool $verejny Či chem zobraziť iba verejné články
     * @return array|mixed
     */
    public function vratClankyMenu($vsetkyVerejne = true)
    {
        $urlClanok = array('o-nas', 'gym', 'bobby');
        $kluce = array(self::TITLE, self::DESCRIPTION, self::URL, 'typ_url');

        $dopyt = 'SELECT titulok, clanok.url, popisok, clanok_typ.url AS typ_url
                  FROM clanok 
                  JOIN clanok_typ USING (clanok_typ_id) 
                  WHERE (clanok.url = ?';

        for($i = 1; $i < count($urlClanok); $i++)
        {
            $dopyt .= ' OR clanok.url = ?';
        }
        $dopyt.=')';
        if ($vsetkyVerejne)
        {
            $dopyt .= ' AND verejny ';
        }
        $dopyt .= 'ORDER BY clanok_id';
        return Db::queryAllRows($dopyt, $urlClanok);
    }

    /**
     ** Pridá k článku autora
     * @param array $clanok Pole z hodnotami článku
     * @return array Pole z článkom aj autorom
     */
    public function pridajAutora($clanok)
    {
        $osobaManazer = new OsobaManazer();

        $clanok['autor'] = $osobaManazer->vratOsobneUdaje($clanok[self::AUTHOR_ID], array(PersonDetailManazer::MENO, PersonDetailManazer::PRIEZVISKO));
        $clanok['upravil_autor'] = $osobaManazer->vratOsobneUdaje($clanok[self::EDITED_AUTHOR_ID], array(PersonDetailManazer::MENO, PersonDetailManazer::PRIEZVISKO));
        unset($clanok[self::AUTHOR_ID]);
        unset($clanok[self::EDITED_AUTHOR_ID]);
        return $clanok;
    }

    /**
     ** priradí člankom titulne OBrázky
     * @param array $clanky Pole článkov
     * @return array Pole Clánkov obsahujúce cestu k titulnému Obrázku
     */
    public function priradClankuObrazok(array $clanky)
    {
        foreach ($clanky as $kluc => $clanok)
        {
            $cesta = 'obrazky/clanky/' . $clanok[InformationManager::URL]; // cesta pre nacitanie obrázkov v prípade editacie
            if ($nazovTitObrazok = File::returnFileNameSubstring($cesta, 'titulna'))
            {
                $clanky[$kluc]['titulnyObrazok'] = $cesta . '/' . $nazovTitObrazok;
            }
        }

        return $clanky;
    }
}
/*
 * Autor: MiCHo
 */
<?php

namespace App\ZakladModul\Kontroler;


use App\ClanokModul\Model\ArticleManazer;
use App\ZakladModul\System\Kontroler\Controller;

/**
 ** Spracováva Menu Stránky
 * Class MenuKontroler
 * @package App\ZakladModul\Kontroler\MenuKontroler
 */
class MenuController extends Controller
{
    /**
     ** Spracuje Výpis Menu
     * @param string $url Url adresa otvorenej stranky
     */
    public function index($url)
    {
        $clanokManazer = new ArticleManazer();

        $this->data['menu'] = $clanokManazer->vratClankyMenu(true);

        // trieda či majú byť karty menu veľké alebo malé
        $this->data['trieda'] = $url === 'uvod' ? '' : 'karty-mensie';
        $this->pohlad = 'index';  //nastavenie pohladu
    }
}
/*
 * Autor: MiCHo
 */
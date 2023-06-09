<?php

namespace App\ZakladModul\Kontroler;

use App\AdministraciaModul\Uzivatel\Model\UserManager;
use App\RezervaciaModul\Model\PermanentkaManazer;
use App\ZakladModul\Model\UpozornenieManazer;
use App\ZakladModul\System\Kontroler\Controller;
use PDOException;

/**
 ** Spracováva Upozornenia na stránke
 * Class UpozornenieKontroler
 * @package App\ZakladModul\Kontroler\UpozornenieKontroler
 */
class UpozornenieController extends Controller
{
    /**
     ** Spracovanie zobrazenia Upozornenia na prepadnutú permanentku
     * @param int $uzivatelId Id uživateľa ktoré prepadnutu permanentku chem skontrolovať
     * @return void
     * @ Action Action oddelené o @ čim je znefunkčné, kvôli tomu, aby sa nemohlo volať URL
     */
    public function prepadnutiePermanentka($uzivatelId)
    {
        $permanentkaManazer = new PermanentkaManazer();
        $permanentka = $permanentkaManazer->nacitajPrepadnutuPermanentku($uzivatelId);

        if ($permanentka) // ak načitalo permanentku na ktorú prepadnutie chcem upozorniťtak načitam pohľad a aj jeho údaje na zobrazenie
        {
            $this->data['permanentka'] = $permanentka;
            $this->data['presmeruj'] = self::$currentUrl;

            $this->pohlad = 'prepadnutie-permanentka';
        }
    }

    /**
     ** Uloži potvrdenie ze uživatel potvrdil že mu prepadla permanentka
     * @param int $permanantkaId Id prepadnutej permanentky
     * @Action
     */
    public function uloz($permanantkaId)
    {
        $this->overUzivatela();
        $upozornenieManazer = new UpozornenieManazer();
        try
        {
            $upozornenieManazer->ulozPotvrdenie($permanantkaId, UserManager::$uzivatel[UserManager::USER_ID]);
        }
        catch (PDOException $chy)
        {
            $this->pridajSpravu('Nemáte povolenie na túto operáciu.', self::SPR_CHYBA);
        }
        $this->presmeruj();
    }
}
/*
 * Autor: MiCHo
 */

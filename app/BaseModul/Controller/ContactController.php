<?php

namespace App\BaseModul\Controller;

use App\BaseModul\System\Controller\Controller;
use Settings;

/**
 ** Spracováva kontaktný formulár
 * Class ContactController
 * @package App\BaseModul\Controller\ContactController
 */
class ContactController extends Controller
{
    /**
     ** Zobrazí infformácie pre kontakt
     * @Action
     */
    public function index()
    {

        $this->data['tel'] = Settings::$tel;
        $this->data['email'] = Settings::$email;

        $this->view = 'index';
    }

    /**
     ** Šablona pre odoslanie kontaktného emailu
     * @param array $emailData Data ktore chcem poslať emailom
     * @ Action
     */
    public function sablonaKontaktEmail($emailData)
    {
        $kontaktManazer = new KontaktManazer();
/*
         //testovacie data
        $emailData = array('meno' => 'Joži',
                            'priezvisko' => 'Podbrezovnik',
                            'email' => 'jozi.podbrezovnik@gmail.com',
                            'tel' => '0914278745',
                            'sprava' => 'Je to tu fajno');
*/
        $this->data['domainName'] = Settings::$domainName;
        $this->data['domain'] = Settings::$domain;
        $this->data['emailData'] = $emailData;
        $this->data['kontakty'] = $kontaktManazer->nacitajInfoKontakt();// Data pre Informácie o kontakte


        $this->pohlad = 'kontakt-email';
    }

    /**
     ** Šablona pre odoslanie skupinového emailu
     * @param string $sprava sprava
     * @ Action
     */
    public function sablonaSkupinovyEmail($sprava)
    {
        $this->data['domainName'] = Settings::$domainName;
        $this->data['domain'] = Settings::$domain;
        $this->data['sprava'] = $sprava;

        $this->pohlad = 'skupinovy-email';
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

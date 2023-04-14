<?php

namespace App\ZakladModul\Model;

use App\AdministraciaModul\Uzivatel\Kontroler\RegistraciaController;
use App\AdministraciaModul\Uzivatel\Model\PersonDetailManazer;
use App\AdministraciaModul\Uzivatel\Model\UserManager;
use App\ZakladModul\Kontroler\EmailController;
use App\ZakladModul\Kontroler\ContactController;
use Micho\UserException;
use Micho\Formular\Form;
use Micho\EmailSender;
use Settings;

/**
 ** Správca Kontaktu
 * Class KontaktManazer
 */
class ContactManager
{
    /**
     ** Zostavý šablonu správy pre email
     * @param array $emailData Data prijaté z kontaktného formuláru
     */
    public function odosliKontaktnyEmail($emailData)
    {
        $kontaktKontroler = new ContactController();
        $kontaktKontroler->sablonaKontaktEmail($emailData);

        // Šablona rozložena emailu a taktiez Štýly
        $emailKontroler = new EmailController();
        $emailKontroler->index($kontaktKontroler);

        ob_start();
        $emailKontroler->vypisPohlad();
        $sprava = ob_get_contents();
        ob_end_clean();

        $odosielacEmailov = new EmailSender();
        $odosielacEmailov->send(Settings::$email, 'Email z webu: ' . Settings::$domainName, $sprava, $emailData['kontakt_email']);
    }

    /**
     ** Nacitá hodnoty pre kontaktné informácie
     * @return array Pole hodnôt
     */
    public function nacitajInfoKontakt()
    {
        $osobaManazer = new UserManager();
        $kontakty = $osobaManazer->vratAdminov(array(UserManager::USER_ID, PersonDetailManazer::TEL, PersonDetailManazer::EMAIL));

        $slovnik = array('Technická podpora','Hlavný Tréner', 'Kontakt');

        if(UserManager::$uzivatel)
        {
            foreach ($kontakty as $kluc => $kontakt) // vytvori asociativne pole z pridelenou funkcion osobe
            {
                $kontaktyNove[$slovnik[$kontakt[UserManager::USER_ID] - 1]] = $kontakt;
            }
            $kontaktyNove = array_reverse($kontaktyNove);

        }
        return isset($kontaktyNove) ? $kontaktyNove : array();
    }

    /**
     ** Odošle skupinový email
     * @param array $adresat Pole adresátov
     * @param string $predmet Predmet Správy
     * @param string $sprava Správa
     * @throws UserException
     * @throws \ReflectionException
     */
    public function odosliSkupinovyEmail(array $adresat, $predmet, $sprava)
    {
        // Získanie obsahu emailu zo šablony kontroleru
        $kontakKontoler = new ContactController();
        $kontakKontoler->sablonaSkupinovyEmail($sprava);

        // Šablona rozložena emailu a taktiez Štýly
        $emailKontroler = new EmailController();
        $emailKontroler->index($kontakKontoler);

        ob_start();
        $emailKontroler->vypisPohlad();
        $sprava = ob_get_contents();
        ob_end_clean();

        $odosielacEmailov = new EmailSender();

        // odosielanie emailu
        foreach ($adresat as $email)
        {
            $odosielacEmailov->send($email, Settings::$domainName . ': ' . $predmet, $sprava, Settings::$email);
        }
    }
}
/*
 * Autor: MiCHo
 */

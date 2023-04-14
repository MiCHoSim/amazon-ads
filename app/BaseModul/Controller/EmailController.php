<?php

namespace App\BaseModul\Controller;

use App\AccountModul\Model\UserTable;
use App\BaseModul\System\Controller\Controller;
use Micho\EmailSender;
use Settings;

/**
 ** Spracuvava požiadavku pri odoslany emailu ... načita hlavný layout a hodi do neho údaje emailu
 * Class EmailKontroler
 * @package App\ZakladModul\Kontroler
 */
class EmailController extends Controller
{

    /**
     * Načitanie hlavného rozlozenia emailu
     * @param object $kontroler Instancie vnoreného kontroléra teda emailu konkretného odosielača emailu
     */
    private function layout()
    {
        $this->data['domain'] = Settings::$domain;
        $this->data['domainName'] = Settings::$domainName;
        $this->view = 'email-layout';
    }

    private function buildTemplate()
    {
        ob_start(); // Zapnite ukladanie výstupov do vyrovnávacej pamäte
        $this->writeView();
        $message = ob_get_contents(); //Vráti obsah výstupnej vyrovnávacej pamäte
        ob_end_clean(); //Vyčistenie (vymazanie) výstupnej vyrovnávacej pamäte a vypnutie výstupnej vyrovnávacej pamäte
        return $message;
    }
    /**
     ** Pripravý šablonu/Pohľad pre emailovú správu
     * @param array $userData Zadané osobné údaje
     * @throws \ReflectionException
     */
    public function registrationEmailTemplate($userData)
    {
        $this->layout();

        $this->data['userData'] = $userData;
        $this->data['subview'] = 'registration-template';

        $message = $this->buildTemplate();

        $emailSender = new EmailSender();
        $emailSender->send($userData[UserTable::EMAIL], 'Registration at ' . Settings::$domain, $message, Settings::$email);
    }

    /**
     ** Pripravý šablonu/Pohľad pre emailovú správu
     * @param string $email prihlasovycí email
     * @param string $password heslo
     * @return void
     * @throws \ReflectionException
     */
    public function forgotPasswordEmailTemplate(string $email, string $password)
    {
        $this->layout();

        $this->data['email'] = $email;
        $this->data['password'] = $password;

        $this->data['subview'] = 'forgot-password-email';

        $message = $this->buildTemplate();

        $emailSender = new EmailSender();
        $emailSender->send($email, 'Forgot password on ' . Settings::$domain, $message, Settings::$email);
    }

}

/*
 * Autor: MiCHo
 */
<?php

namespace App\AccountModul\Model;

use App\BaseModul\Controller\EmailController;
use Couchbase\User;
use Micho\Db;
use Micho\Exception\UserException;
use Micho\Form\Form;
use Micho\Utilities\StringUtilities;

class AccountManager
{

    public function savePersonalInformation($data)
    {
        // Porozdelovanie prijatých údajov na polia ktoré sa ukladajú do jednotlivých tabuliek
        $personDetailId = $data[PersonDetailTable::PERSON_DETAIL_ID];
        $personDetailData = array_intersect_key($data, array_flip(PersonDetailTable::$personDetailDataKeys));

        $personDetailManager = new PersonDetailTable();
        $personManager = new PersonTable();

        // ak nieje zadene person detail id tak vytvaram novy zaznam
        if(empty($personDetailId))
        {
            // uloži osoba detail
            $personDetailId = $personDetailManager->savePersonDetail($personDetailData);
            // uloži osobu
            $personData = array(PersonTable::PERSON_DETAIL_ID => $personDetailId,
                                PersonTable::USER_ID => UserTable::$user[UserTable::USER_ID]);
            print_r($personData);

            $personManager->savePerson($personData);
        }
        //inak updatujem stary
        else
            $personDetailManager->updatePersonDetail($data, $personDetailId);
    }


    /**
     ** VYtvor učet novému uživateľovy
     * @param array $userData Osobné údaje potrebné na registráciu
     * @return string Id práve registrovaného uživateľa
     * @throws UserException
     */
    public function createNewAccount(array $userData)
    {
        $userManager = new UserTable();

        unset($userData[UserTable::PASSWORD_AGAIN]); // odstránenie hodnoty ktorá sa neukladá do Databázi

        $userManager->saveUser($userData); // uloženie uživateľa do tabuľky uživateľov

        $emailKontroler = new EmailController();
        $emailKontroler->registrationEmailTemplate($userData);// odoslanie registračného Emailu
    }

    /**
     * Odošle objednávku emailem spolu zo zprávou
     * @param string $sprava Správa
     * @throws UserException
     */
    public function createNewPassword(string $email) //upraviť
    {
        $userManager = new UserTable();

        // Generovanie hesla
        $newPassword = StringUtilities::generateNewPassword(true);

        //zmeni heslo uzivateľovi
        $userManager->changePassword($newPassword, false , $email);

        $emailController = new EmailController();
        $emailController->forgotPasswordEmailTemplate($email, $newPassword);// odoslanie registračného Emailu
    }

    /**
     ** Prihlási uživateľa do systému
     * @param string $email Prihlasovací email uživateľa
     * @param string $password Prihlasovacie heslo uživateľa
     * @return string Chybová správa
     * @throws ChybaUzivatela
     */
    public function login(string $email, string $password)
    {
        $userManager = new UserTable();
        $userData = $userManager->loadUserData($email);

        if(!$userData)
            throw new UserException('The account for the specified email does not exist.');

        if(!password_verify($password, $userData[UserTable::PASSWORD]))
            throw new UserException('Invalid EMAIL or PASSWORD.');


        $userData[UserTable::LOGIN_DATE_TIME] = $userManager->updateLoginDateTime($userData[UserTable::USER_ID]);
        unset($userData[UserTable::PASSWORD]); // Odstránime heslo z poľa s uživateľov, aby sa nepredávalo na každej stránke webu

        $_SESSION[UserTable::USER] = $userData;

        $userManager->loadUser();

        session_regenerate_id(true); // Po prihlasení zemním SID // Ochrana => "Session hijacking -> session fixation"
    }
}
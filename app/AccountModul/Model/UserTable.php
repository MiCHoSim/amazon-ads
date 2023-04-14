<?php

namespace App\AccountModul\Model;


use Micho\Db;
use Micho\Exception\UserException;
use Micho\Utilities\ArrayUtilities;
use Micho\Utilities\DateTimeUtilities;

/**
 ** Správca uživateľov redakčného systému / Pracovanie z tabuľkou user
 * Class UserManager
 * @package App\AccountModul\Model
 */
class UserTable
{
    /**
     * Názov Tabuľky pre Spracovanie Uživateľa
     */
    const USER_TABLE = 'user';

    /**
     * Konštanty Databázy 'kontroler'
     */
    const USER_ID = 'user_id';
    const EMAIL = 'email';
    const PASSWORD = 'password';
    const REGISTRATION_DATE_TIME = 'registration_date_time';
    const LOGIN_DATE_TIME = 'login_date_time';
    const ADMIN = 'admin';
    const PROGRAMMER = 'programmer';

    /**
     * Ostatne pomocne konštanty
     */
    const PASSWORD_AGAIN = 'password_again';
    const USER = 'user';

    const USER_DATA = array(self::USER_ID, self::EMAIL, self::PASSWORD, self::REGISTRATION_DATE_TIME,
        self::LOGIN_DATE_TIME, self::ADMIN, self::PROGRAMMER);

    /**
     * @var array|null Aktualne prihlaseny uživateľ alebo null
     */
    public static $user;

    /**
     ** Uloží aktuálne prihlaseného uživateľa
     */
    public function loadUser()
    {
        self::$user = isset($_SESSION[self::USER]) ? $_SESSION[self::USER] : null;
    }

    /**
     ** Overý či sú registraČné hodnotý správne
     * @param array $data Dáta od uzivateľa
     * @return array Pole chybových správ
     * @throws UserException
     */
    public function checkRegistrationValues($data)
    {
        $messages = array();

        $messages[] = !$this->existEmail($data[self::EMAIL]) ? '' : 'An account with the entered email address already exists.';
        $messages[] = $this->matchPasswords($data[self::PASSWORD], $data[self::PASSWORD_AGAIN]);

        return array_filter($messages);
    }

    /**
     **Overí, či zadany email existuje
     * @param string $email Email k overeniu
     * @return bool či email existuje
     */
    public function existEmail($email)
    {
        return (bool) Db::queryAlone(('SELECT COUNT(*) FROM user 
                WHERE email = ? '),array($email));
    }

    /**
     ** Overý či sa zadané hesla zhodujú
     * @param string $password Heslo
     * @param string $passwordAgain Heslo znova
     * @throws UserException
     */
    private function matchPasswords($password, $passwordAgain)
    {
        if($password !== $passwordAgain)
            return 'Password and Password again do not match.';
    }

    /**
     ** Uloží nového uživateľa do tabuľky uživateľov
     * @param array $userData data  uzvateľa na ulozenie
     * @return string Id novo prihlaseného uživateľa
     * @throws UserException
     */
    public function saveUser(array $userData): string
    {
        $userData[self::PASSWORD] = $this->createPasswordHas($userData[self::PASSWORD]);

        try
        {
            Db::insert(self::USER_TABLE, $userData);
        }
        catch (PDOException)
        {
            throw new UserException('An error occurred during user registration.');
        }
        return Db::returnLastId();
    }

    /**
     ** Vráti odtlačok hesla
     * @param string $password Heslo
     * @return string Odlačok hesla
     */
    private function createPasswordHas($password): string
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    /**
     ** Uloží zmenu hesla do Databázi buď podľa Emailu alebo Id
     * @param string $password Nové heslo uživateľa
     * @param int $user_id Id uživateľa
     * @param string $email Email uživateľa
     */
    public function changePassword(string $password, int $user_id, string|false $email = false)
    {
        $passwordHas = $this->createPasswordHas($password);

        if($email)        //0,0067 / 0,0069 / 0,0080 / 0,0077 = 0,0073 Rýchlosť akcie v sekundách
            Db::query('UPDATE user                                              
                         SET password = ? WHERE email = ?', array($passwordHas, $email));
        else
            Db::query('UPDATE user                                                 
                         SET password = ? WHERE user_id = ?', array($passwordHas, $user_id));
    }

    /**
     ** Načitá data uživateľa podľa emailu
     * @param string $email email
     * @return array|mixed
     */
    public function loadUserData(string $email): array|false
    {
        $keys = self::USER_DATA;
        $userData =  Db::queryOneRow('SELECT ' . implode(', ', $keys) . '
                                          FROM '. self::USER_TABLE . ' 
                                          WHERE email = ?', array($email));

        return $userData;
    }
    
    /**
     ** Nastavi novú hodnotu pre dátum a čas prihlasenia Na aktuálny dátum a čas
     * @param int $userId ID uŽivatela ktorýmu menim dátum a Čas prihlásenia
     * @return string Dátum a Čas v DB formáte
     */
    public function updateLoginDateTime(int $userId) : string
    {
        $dateTimeDb = DateTimeUtilities::dbNow(); // Aktualný Čas v DB formate

        Db::query('UPDATE '. self::USER_TABLE . ' 
                         SET ' . self::LOGIN_DATE_TIME . ' = ? WHERE ' . self::USER_ID . ' = ?', array($dateTimeDb,$userId));
        return $dateTimeDb;
    }

    /**
     ** Odhlási uživateľa
     */
    public function logOut()
    {
        unset($_SESSION[self::USER]);
    }

    /**
     ** Zvaliduje zmena hesla údaje od uzivatela
     * @param array $data Dáta od uzivateľa
     * @return array
     * @throws UserException
     */
    public function checkChangePasswordValue($data): array
    {
        $messages = array();

        $messages[] = $this->verifyPassword(UserTable::$user[self::USER_ID],$data['old_password']);

        $messages[] = $this->matchPasswords($data[self::PASSWORD], $data['password_again']);

        $messages = array_filter($messages);

        return $messages;
    }

    /**
     ** Overi zhodsnoť zadaného hesla a hesla uloženého v DB
     * @param int $userId Id uživateľa ktoreho hesla chcem verifikovať
     * @param string $password Heslo na verfikáciu
     * @return string Chybová hlaška
     */
    private function verifyPassword($userId, $password)
    {
        $userPassword = Db::queryAlone('SELECT ' . self::PASSWORD . '
                                          FROM ' . self::USER_TABLE . ' WHERE ' . self::USER_ID  . ' = ?', array($userId));

        if(!password_verify($password, $userPassword))
            return ('The old password you entered does not match your password stored in the database.');
    }

    /**
     ** Vráti zoznam všetkých uživateľov
     * @return mixed
     */
    public function loadAllUsersPairs()
    {
        $keys = [self::USER_ID];
        $data = Db::queryAllRows('SELECT ' . implode(', ',$keys) . ',
                                                CONCAT(COALESCE(' . PersonDetailTable::NAME . ', ""), " ", COALESCE(' . PersonDetailTable::LAST_NAME . ', ""), " | ", COALESCE(' . self::EMAIL . ', "")) AS person
                                            FROM ' . self::USER_TABLE . '
                                            JOIN ' . PersonTable::PERSON_TABLE . ' USING (' . PersonTable::USER_ID . ')
                                            JOIN ' . PersonDetailTable::PERSON_DETAIL_TABLE . ' USING (' . PersonDetailTable::PERSON_DETAIL_ID . ')
        ');

        $keys[] = 'person';

        return ArrayUtilities::getPairs($data,'person', UserTable::USER_ID);
    }
















    /**
     ** Vráti všetkých administratorov
     * @return mixed
     */
    public function vratAdminov($kluce)
    {
        $data = Db::queryAllRows('SELECT ' . implode(', ',$kluce) . ', 
                                                CONCAT(COALESCE(meno, ""), " ", COALESCE(priezvisko, "")) AS osoba
                                            FROM uzivatel
                                            JOIN osoba USING (uzivatel_id)
                                            JOIN osoba_detail USING (osoba_detail_id)
                                            WHERE admin ORDER BY uzivatel_id
        ');
        return $data;
    }

    /**
     ** Spracuje požiadavku na priradenia administrátorských práv uživateľovy
     * @param $email
     * @throws UserException
     */
    public function pridajAdmina($email)
    {
        $osobaManazer = new OsobaManazer();
        if (!$osobaManazer->overExistenciuEmailu($email))
            throw new UserException('Osoba s týmto emailom nieje registovaná');

        $this->nastavAdmina($email);
    }

    /**
     ** Nastavý uživateľa ako Admina
     * @param string $email Email uživateľa
     */
    private function nastavAdmina($email)
    {
        $a = (bool) Db::query('UPDATE uzivatel 
                        JOIN osoba USING (uzivatel_id)
                        JOIN osoba_detail USING (osoba_detail_id)
                        SET admin = 1 WHERE email = ?', array($email));

        if (!$a)
            throw new UserException('Tento uživateľ už má nastavené administrátorske práva');
        $_SESSION[self::USER_ID]['admin'] = 1;
    }
    /**
     ** Zruší administrátorske práva uživateľa
     * @param int $uzivatelId ID uŽivatela ktorécho chem zrušit
     */
    public function odstranAdmina($uzivatelId)
    {
        Db::query('UPDATE uzivatel SET admin = 0 WHERE uzivatel_id = ?', array($uzivatelId));

        if(UserTable::$user[self::USER_ID] == $uzivatelId) // ak odstranuejm ako admina seba tak aj v session uzivatela to nastavim
            $_SESSION[self::USER_ID]['admin'] = 0;
    }


}

/*
 * Autor: MiCHo
 */
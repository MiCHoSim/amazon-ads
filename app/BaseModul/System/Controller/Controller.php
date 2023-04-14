<?php

namespace App\BaseModul\System\Controller;

use App\AccountModul\Model\UserTable;
use Micho\Utilities\StringUtilities;
use Settings;

use Exception;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;

/**
 ** Predok pre kontroléry v aplikácii
 * Class Kontroler
 * @package App\ZakladModul\System\Kontroler
 */
abstract class Controller
{
    /**
     ** Možnosti typov Správ
     */
    const MSG_INFO = 'info'; // nazvy sa zhodujú s triedami v Bootstrape
    const MSG_SUCCESS = 'success';
    const MSG_ERROR = 'error';

    public static $currentUrl = ' '; // Aktuálna URL
    /**
     * @var bool či bol kontroler vytvorený API miesto člankom
     */
    protected $cretedApi;

    /**
     * @var array Pole, ktorého indexi sú viditeľné v šablone ako bežné premenné
     */
    public $data = array();

    /**
     * @var string Názov šablony
     * Pokiaľ sa pohľad nachadzá v inej Časti treba zadať jeho cestu
     */
    public $view = '';

    /**
     * @var object Controller Instancia kontroléru
     */
    protected $subPageController;

    /**
     ** Inicializuje instanciu
     * Kontroler constructor.
     * @param false $vytvorilApi Ci bol kontroler vytvoreny API miesto  Člankom
     */
    public function __construct($vytvorilApi = false)
    {
        $this->cretedApi = $vytvorilApi;
    }

    /**
     ** Ošetri premennú pre výpis do HTML stránky
     * @param null $x Premenná na ošetrenie
     * @return array|mixed|string|null Ošetrená premenná
     */
    public function checkData($x = null)
    {
        if (!isset($x))  // aj nieje inicializované vrátime null
            return null;
        elseif (is_string($x))  //ak je reťazec
            return htmlspecialchars($x, ENT_QUOTES); // Ochrana proti => "XSS"
        elseif (is_array($x))   //ak pole
        {
            foreach($x as $k => $v) //Rekurzívne sa ošetria všetky položky poľa
            {
                $x[$k] = $this->checkData($v); //
            }
            return $x;
        }
        else
            return $x;
    }

    /**
     ** Vypiše pohľad / pokiaľ sa pohľad nachadzá v inej Časti treba zadať jeho cestu
     * Načíta pohľad podľa zostavenej cesty
     * @throws ReflectionException
     */
    public function writeView()
    {
        if ($this->view)
        {
            extract($this->checkData($this->data));
            extract($this->data, EXTR_PREFIX_ALL, '');  //extraktujeme + prida sa prefix _

            if (mb_strpos($this->view, '/') === FALSE) // Ak nieje zadaná cesta k pohľadu tak ju zostavim
            {
                // Nemôžeme použiť funkciu pre zistenie namespace pretože by vrátila ten z abstraktného kontroléra
                $reflect = new ReflectionClass(get_class($this));

                $path = str_replace('Controller', 'View', str_replace('\\', '/', $reflect->getNamespaceName()));

                $controllerName = str_replace('Controller', '', $reflect->getShortName());

                //zostavenie celej cesty k pohľadu, je potrebné previest aj App na app
                $path = '../a' . ltrim($path, 'A') . '/' . $controllerName . '/' . $this->view . '.phtml';
            }
            else
                $path = '../app/' .  $this->view  . '.phtml';

            //echo "<p style='background: blue'>" . get_class($this) . "<p>";
            require($path);
        }
    }

    /**
     ** Pridá správu do SESSION
     * @param string $contents Obsah správy
     * @param array|string $type Typ spravy
     */
    public function addMessage(array|string $contents, string $type = self::MSG_INFO)   //ukladanie správ
    {
        if(is_array($contents)) // ak je zadane hodnota obsahu pole tak rekurzivne prejdem polozky a pridam ich ako spravy ... tu chodia správy z validatora
        {
            foreach ($contents as $ob)
            {
                $this->addMessage($ob, $type);
            }
            return;
        }
        $message = array('contents' => $contents, 'type' => $type);

        if (isset($_SESSION['messages'])) //zisti či je vytvorené superglobalne pole ak nie tak ho vytvorí
            $_SESSION['messages'][] = $message;
        else
            $_SESSION['messages'] = array($message);
    }

    /**
     ** Vráti správu pre uživateľa
     * @return array|mixed Správy pre uživateľov
     */
    public function getMessages() //vratenie správ
    {
        if (isset($_SESSION['messages']))
        {
            $messages = $_SESSION['messages'];
            unset($_SESSION['messages']); //vyprazdnenie
            return $messages;
        }
        else
            return array(); //prázdne pole
    }

    /**
     ** Presmeruje na dané URL
     * @param string $url Url na ktorú chcem presmerovať
     */
    public function redirect($url = '')
    {
        if (!$url)
            $url = self::$currentUrl;
        if (isset($_GET['presmeruj']))
            $url = $_GET['presmeruj'];

        header("Location: /$url");
        header('Connection: close');
        exit;
    }

    /**
     ** Overí, či je uživateľ prihlasený a či spĺňa podmienku administrátora/programatora, prípadne presmeruje na Prihlásenie
     * @param string $userId id uživateľa ktore urpavujem... musi sa zhodovať z ID prihlaseneho aleob musi byť admin
     * @param bool $admin Musí byŤ admin
     * @return void
     */
    public function userVerification(int $userId = null, bool $admin = false)
    {
        $user = UserTable::$user;

        if(!$user)
            $this->redirectVerification();

        if(!empty($userId))
            if($userId != $user[UserTable::USER_ID] && !$user[UserTable::ADMIN])
                $this->redirectVerification();

        if ($admin && !$user[UserTable::ADMIN])
            $this->redirectVerification();
    }

    /**
     * @return void presmerovanie
     */
    private function redirectVerification()
    {
        $this->addMessage('You are not logged in or do not have sufficient permissions.', self::MSG_ERROR);
        $this->redirect(' ');
    }




    /**
     ** Overí, či je uživateľ prihlasený a či spĺňa podmienku Trénera, prípadne presmeruje na Prihlásenie
     */
    public function overTrenera()
    {
        $uzivatel = UserTable::$uzivatel;
        if(!$uzivatel || (!$uzivatel['trener'] && !$uzivatel[UserTable::PROGRAMMER]))
        {
            if (!$this->cretedApi)// Pokiaľ bola požiadavka na autentizaciu z článku, presmerujeme na prihlásenie
            {
                $this->addMessage('Nie ste prihlásený alebo nemáte dostatočné oprávnenia.', self::MSG_ERROR);
                $this->redirect(' ');
            }
            else //Pokiaľ bola požiadavka z API, vrátime time chybový  kód
            {
                header('HTTP/1.1 401 Unauthorized');
                die('Nedostatočné oprávnenie');
            }
        }
    }

    /**
     ** Spusti akciu kontoléra podľa parametrov z URL adresy
     * @param array $parameters Parametre z URL adresy: prvý  je nazov akcie, pokiaľ nie je uvedený, predpokladá sa s akcia index()
     * @param bool $api Či chcem renderovať ako API, teda bez layoutu
     * @throws ReflectionException
     */
    public function callActionFromParameters(array $parameters, bool $api = false)
    {
        $action = StringUtilities::hyphenatedToCamel($parameters ? array_shift($parameters) : 'index'); // volanie konkrétnej metódy v triede
        try //získanie informácii o metóde
        {
            $methodInfo = new ReflectionMethod(get_class($this), $action);
        }
        catch (ReflectionException $exception)
        {
            $this->throwRoutingException("Invalid action - $action");
        }

        $phpDoc = $methodInfo->getDocComment();// Kontorlá  práv pristupu pomocov PhpDoc
        $annotation = $api ? '@ApiAction' : '@Action';

        if (mb_strpos($phpDoc, $annotation) === FALSE) // zitenie či sa môže metóda zavolať
            $this->throwRoutingException("Invalid actionInvalid action - $action");

        $loadNumberOfParameters = $methodInfo->getNumberOfRequiredParameters(); // poCět parametrov

        if (count($parameters) < $loadNumberOfParameters) // zistenie či sme danej metóde zadali potrebný počet parametrov
            $this->throwRoutingException("The necessary parameters were not sold to the action. Number needed: $loadNumberOfParameters");

        $methodInfo->invokeArgs($this, $parameters); // zavolaníe konkrétnej metódy v Kontroléry
    }

    /**
     ** V ladiacom móde vyvolá výnimku, inak poresmeruje na 404 chybovu stránku
     * @param string $message Správa, ktorá sa ma zobraziť
     * @throws Exception
     */
    private function throwRoutingException($message)
    {
        if (Settings::$debug)
            throw new Exception($message);
        else
            $this->redirect('error');
    }
}


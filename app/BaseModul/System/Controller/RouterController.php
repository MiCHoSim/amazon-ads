<?php


namespace App\BaseModul\System\Controller;

use App\AccountModul\Model\UserTable;
use App\BaseModul\System\Model\ManagerController;
use Settings;
/**
 ** Smerovač, na ktorý sa uživateľ dostane po zadaní URL adresy Spracuje ceku app: Hlavné rozloženie stránky a podstránky
 * Class RouterController
 * @package App\BaseModul\System\Controller
 */
class RouterController extends Controller
{

    /**
     * @var array Načitaný kontrolér pole
     */
    public static $subPageControllerArray;

    /**
     ** Smerovanie načitavania stránky
     * @param string $parameters Reťazec url adresy
     */
    public function index(string $parameters)
    {
        $parsedUrl = $this->parseUrl($parameters);

        $userManager = new UserTable();
        $userManager->loadUser();

        if (empty($parsedUrl[0])) // ak je začiatok prázdný som na úvodnej stránke
            $parsedUrl = array('article','home-page');
        /*
        if ($parsedUrl[0] == 'api') // spracovávame požiadavky na "api"
        {
            array_shift($parsedUrl); // odstráni prvý parameter "api"
            $this->processApiRequest($parsedUrl);
        }
        else */

        $this->fullPageRequest($parsedUrl); // spracovávame požiadavky na kontroléry celej stránky
    }

    /**
     ** Spracuje žiadosť o kontroléry App
     * @param array $parsedUrl Pole parametrov z URL
     */
    private function fullPageRequest(array $parsedUrl)
    {
        //print_r($parsedUrl);echo"<br>";
        //print_r(UserManager::$user);echo"<br>";

        // Kontrolér pre podstránky
        $this->loadSubPageController($parsedUrl);
        $this->data['subpageController'] = $this->subPageController;


        // <head> nastaveni premenných pre šablonu
        $this->data['user'] = UserTable::$user;
        $this->data['title'] = self::$subPageControllerArray[ManagerController::TITLE];
        $this->data['domain'] = Settings::$domain;
        $this->data['description'] = self::$subPageControllerArray[ManagerController::DESCRIPTION];
        $this->data['author'] = Settings::$authorWebu;
        $this->data['domainName'] = Settings::$domainName;

        // <body> nastaveni premenných pre šablonu
        $this->data['messages'] = $this->getMessages();
        $this->view = 'layout'; // nastavenie hlavného pohladu/šablony




        /*
        // Rozlišnie munu pred proihlasenim a po prihlaseni
        $prihlasenieKontroler = new PrihlasenieKontroler();
        $uzivatel = UserManager::$uzivatel;
        if (!$uzivatel)//ak je uzivateľ prihlaseni zobrazujem ine veci ako Ked nieje
        {
            $this->data['prihlaseny'] = false;
            $prihlasenieKontroler->neprihlasenyMenu();
            // Volanie kontroléra, ktorý spracuje výpis Rychle info o stránke Pred registrácoiu/Mimo prihlasenia
            $uvodKontroler = new UvodKontroler();
            $uvodKontroler->uvodInfo(CookiesManazer::UVOD_INFO);
            $this->data['uvodInfoKontroler'] = $uvodKontroler;
        }
        else
        {
            $this->data['prihlaseny'] = true;
            $prihlasenieKontroler->prihlasenyMenu();

            // Volanie kontroléra, ktorý spracuje výpis Rychle info o stránke Po registrácií a prihlásení
            $uvodKontroler = new UvodKontroler();
            $uvodKontroler->uvodInfo(CookiesManazer::UVOD_INFO_INSTRUKCIE);
            $this->data['uvodInfoKontroler'] = $uvodKontroler;

            $prepadnutiePermanentka = new UpozornenieKontroler();
            $prepadnutiePermanentka->prepadnutiePermanentka($uzivatel[UserManager::UZIVATEL_ID]);
            $this->data['prepadnutiePermanentka'] = $prepadnutiePermanentka;

        }

        $this->data['prihlasenie_menu'] = $prihlasenieKontroler;

        // Volanie kontroléra, ktorý spracuváva Kontakt a taktieŽ kontaktný formulár
        $kontaktKontroler = new KontaktKontroler();
        $kontaktKontroler->index();
        $this->data['kontaktKontroler'] = $kontaktKontroler;

        // Volanie kontroléra ktorý spracuje výpis Menu stránky
        $menuKontroler = new MenuKontroler();
        $menuKontroler->index($parametre[0]);
        $this->data['menuKontroler'] = $menuKontroler;

        // Volanie kontroléra, ktorý spracuje výpis Cookies stránky
        $cookiesKontroler = new CookiesKontroler();
        $cookiesKontroler->index(CookiesManazer::COOKIES);
        $this->data['cookiesKontroler'] = $cookiesKontroler;

        $clanokManazer = new ClanokManazer();


        if(UserManager::$uzivatel) // informacny panel pre prihlaseneho uzivatela
            $clankyUrl = array('ochrana-osobnych-udajov', 'cookies', 'o-nas', 'gym-ceny', 'bobby-ceny', 'uvodne-info', 'instrukcie', 'pravidla-gymu', 'standalone-aplikacia');
        else
            $clankyUrl = array('ochrana-osobnych-udajov', 'cookies', 'o-nas', 'gym-ceny-neprihlaseny', 'bobby-ceny-neprihlaseny', 'uvodne-info', 'pravidla-gymu', 'standalone-aplikacia');

        $this->data['informacie'] = $clanokManazer->vratClankyUrl($clankyUrl,true);
*/


    }


    /**
     ** Spracuje žiadosť na API
     * @param string $parametre Pole paremetrov z URL
     */
    private function processApiRequest(string $parametre)
    {
        $polozky = explode('-', $parametre); //rozbitie mennych priestorov podľa "-"
        array_splice($polozky, count($polozky) - 1, 0, 'Kontroler'); // pridanie "Kontroler"

        $kontrolerCesta = 'App\\' . implode('\\', $polozky);
        $kontrolerCesta .= 'Kontroler';

        if (preg_match('/^[a-zA-Z\d\\\\]*$/u', $kontrolerCesta))// bezpečnostná kontrola cesty
        {
            $kontroler = new $kontrolerCesta(true);
            $kontroler->callActionFromParameters($parametre, true);
            $kontroler->vypisPohlad();
        }
        else
            $this->redirect('error');
    }

    /**
     ** Naparsuje URL adresu podľa lomitok a vráti pole parametrov
     * @param string $url URL adresa
     * @return array Naparsovana URL adresa
     */
    private function parseUrl(string $url) : array
    {
        $parsedUrl = parse_url($url); // naparsuje jednotlive časti URL adresy do asociativného pola

        $parsedUrl['path'] = ltrim($parsedUrl['path'], '/'); // odstráni začiatočné lomítko

        self::$currentUrl = $parsedUrl['path'] = trim($parsedUrl['path']); // odstránenie bielich znakov okolo adresy

        return explode('/', $parsedUrl['path']); // rozbitie reťazca podľa lomítok
    }

    /**
     ** Načitanie vnoreného kontroléra
     * @param array $parsedUrl Pole parametrov pre kontrolér, pokiaľ niejaké má
     */
    public function loadSubPageController(array $parsedUrl)
    {
        $managerController = new ManagerController(); // vytvorenie instancie modelu pre správu kontrolérov
        $controllerUrl = array_shift($parsedUrl);

        if ($controllerUrl === 'info')
            $parsedUrl = array_merge(array('index'), $parsedUrl);

        self::$subPageControllerArray = $managerController->loadController($controllerUrl); // ziskanie kontoléru podľa URL
        if (!self::$subPageControllerArray[ManagerController::CONTROLLER_PATH]) // pokiaľ nebol kontrolér s danou URL nájdeny, Presmeruje na ChybaController
            $this->redirect('error');
        $pathController = 'App\\' . self::$subPageControllerArray[ManagerController::CONTROLLER_PATH] . 'Controller';

        $this->subPageController = new $pathController(); // instancia vnoreného kontroléra
        $this->subPageController->callActionFromParameters($parsedUrl);
    }

}
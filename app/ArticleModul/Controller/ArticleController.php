<?php

namespace App\ArticleModul\Controller;

use App\AccountModul\Model\UserTable;
use App\ArticleModul\Model\ArticleManager;
use App\BaseModul\System\Controller\Controller;
use App\BaseModul\System\Controller\RouterController;
use Micho\Exception\UserException;

/**
 ** Spracováva stránku pre články
 * Class ArticleController
 * @package CApp\ArticleModul\Controller
 */
class ArticleController extends Controller
{
    /**
     ** Všetký hodnoty ktoré použivam pri práci z Tabuľkov Člankov
     * @var string[]
     */
    public static $ArticleData =  array(ArticleManager::ARTICLE_ID, ArticleManager::URL, ArticleManager::TITLE , ArticleManager::DESCRIPTION, ArticleManager::CONTENTS, ArticleManager::PUBLIC,
        ArticleManager::AUTHOR_ID, ArticleManager::EDITED_AUTHOR_ID, ArticleManager::CREATION_DATE, ArticleManager::MODIFICATION_DATE, ArticleManager::ARTICLE_TYPE_ID);


    /**
     ** Spracuje zobrazenie Info článkov webu
     * @Action
     */
    public function info($articleUrl)
    {
        $this->getArticleData($articleUrl);

        $this->view = 'info';  //nastavenie pohladu
    }

    /**
     ** Spracuje zobrazenie HomePage článkov webu
     * @Action
     */
    public function homePage()
    {
        $articleUrl = 'home-page';

        $this->getArticleData($articleUrl);

        $this->view = 'home-page';  //nastavenie pohladu

        $this->data['user'] = UserTable::$user;

        /*
        $clanokManazer = new ArticleManager();
        try
        {
            $clanok = $clanokManazer->vratClanok($url, self::$ArticleData);
        }
        catch (UserException $chyba)
        {
            $this->addMessage($chyba->getMessage(), self::MSG_INFO);
            $this->presmeruj('error');
        }
        //$autor = false;
        //$clanok[ClanokManazer::VEREJNY] = 0;


        // ak je članok url instrukcie tak ho nezobrazujem až po prihlaseni
        if($url === 'instrukcie' || $url === 'bobby-ceny' || $url === 'gym-ceny')
            $this->overUzivatela();

        $autor = true;
        if ($clanok[ArticleManager::ARTICLE_TYPE_ID] === ArticleTypeManazer::CLANOK_INFORMACIA || $clanok[ArticleManager::ARTICLE_TYPE_ID] === ArticleTypeManazer::CLANOK_UVOD || $url === 'gym' || $url === 'bobby') // ak je článok typu info alebo je zo uvidný članok tak nezobrazujem autora
              $autor = false;

        if ($clanok[ArticleManager::PUBLIC]) // ak je verejný
        {
            // zobrazenie článku
            if ($autor) // zobrazenie článku aj autora
                $clanok = $clanokManazer->pridajAutora($clanok);
        }
        else // ak nieje verejný
        {
            if(UserManager::$uzivatel && (UserManager::$uzivatel[UserManager::ADMIN] || UserManager::$uzivatel[UserManager::PROGRAMATOR]))
            {
                // zobrazenie článku
                if ($autor) // zobrazenie článku aj autora
                    $clanok = $clanokManazer->pridajAutora($clanok);
            }
            elseif ($url !== 'uvod')
            {
                $this->overUzivatela(true,true); // overenie ci je prihlaseny admin
                // zobrazenie článku
                if ($autor) // zobrazenie článku aj autora
                    $clanok = $clanokManazer->pridajAutora($clanok);
            }
            else
            {
                $clanok = false;
            }
        }

        RequirementsManager::$kontroler['titulok'] = $clanok ? $clanok[ArticleManager::TITLE] : ' ';

        RequirementsManager::$kontroler['popisok'] .= $clanok ? ', ' . $clanok[ArticleManager::TITLE] . ', ' . $clanok[ArticleManager::DESCRIPTION] : '';
        RequirementsManager::$kontroler['autor'] = (isset($clanok['autor']['meno']) ? ($clanok['autor']['meno'] . ' ' . $clanok['autor']['priezvisko']) : '') . (isset($clanok['upravil_autor']['meno']) ? (', ' .$clanok['upravil_autor']['meno'] . ' ' . $clanok['upravil_autor']['priezvisko']) : '');

        $this->data['clanok'] = $clanok;
        $this->data['admin'] = UserManager::$uzivatel && (UserManager::$uzivatel[UserManager::ADMIN] || UserManager::$uzivatel[UserManager::PROGRAMATOR]);

        $this->data['presmeruj'] = self::$currentUrl; // presmerovanie po editacii


        if($url === 'o-nas') // ak je to o nás, tak načítam galériu obrázkov
        {
            $this->data['cestaObrazok'] = 'obrazky/galeria/onas/nahlad';
            $this->data['galeria'] = Subor::vratNazvySuborov($this->data['cestaObrazok']);
        }

        $this->pohlad = 'index';
        */
    }


    /**
     ** Načita potrebné dáta pre článok
     * @param string $articleUrl
     * @return void
     */
    private function getArticleData(string $articleUrl) :void
    {
        $articleManager = new ArticleManager();
        try
        {
            $article = $articleManager->loadArticles([$articleUrl], array(ArticleManager::TITLE, ArticleManager::CONTENTS, ArticleManager::DESCRIPTION))[0];
        }
        catch (UserException $error)
        {
            $this->addMessage($error->getMessage(),self::MSG_ERROR);
            $this->redirect('error');
        }

        $this->data['article'] = $article;

        RouterController::$subPageControllerArray['title'] = $article[ArticleManager::TITLE]; // pridanie doplnujúceho description k hlavnému
        RouterController::$subPageControllerArray['description'] = $article[ArticleManager::DESCRIPTION]; // pridanie doplnujúceho description k hlavnému
    }


    /**
     ** Spracuje zobrazenie zoznamu článkov webu
     * @param string $typUrl url Typov Článkov ktore chem zobraziť ako akrty
     * @ Action
     */
    public function clanky($typUrl)
    {
        $clanokManazer = new ArticleManager();

        $clanky = $clanokManazer->vratClankyZoznamKarty($typUrl);

        RequirementsManager::$kontroler['titulok'] = $clanky ? ArticleTypeManazer::TYPY_CLANKOV_URL_NAZOV[$typUrl] : '';
        RequirementsManager::$kontroler['popisok'] = $clanky ? 'Tipy a návody pre: ' . ArticleTypeManazer::TYPY_CLANKOV_URL_NAZOV[$typUrl] : '';

        //priradí članku titulný obrázok
        if(!empty($clanky))
        {
            $clanky = $clanokManazer->priradClankuObrazok($clanky);
        }

        $this->data['clanky'] = $clanky;

        $this->pohlad = 'clanky-karty';
    }



}

/*
 * Autor: MiCHo
 */
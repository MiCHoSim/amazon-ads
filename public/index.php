<?php

    use App\BaseModul\System\Controller\RouterController;
    use Micho\Db;

    ini_set('session.cookie_httponly', 1);      // Ochrana proti ukrádnutiu PHPSESSID => "XSS"

    session_start();

    //ini_set("display_errors", 1);		// nastavenie pre zobrazovanie chýb
    //error_reporting(E_ERROR | E_WARNING);

    header("X-Frame-Options: DENY"); // ochrana proti => "Clickjacking"

    mb_internal_encoding("UTF-8"); // Nastavenie kodovania pre prácu z reťazcami

    require_once('../configuration/Settings.php'); // načitanie konfigurácie stránky

    require('autoloader.php'); // Registrovanie autoloaderu

    Db::connection(Settings::$db['host'], Settings::$db['user'], Settings::$db['password'], Settings::$db['database']); // pripojenie k databáze
    Db::query('SET sql_mode=(SELECT REPLACE(@@sql_mode,"ONLY_FULL_GROUP_BY",""))'); // Zrusenie nastavenia ONLY_FULL_GROUP_BY ... vypisovalo to chybu ked som robil Group BY Z viecerých JOIN tabuliek/

    $router = new RouterController(); // vytvorenie smerovača
    $router->index(array($_SERVER['REQUEST_URI'])[0]); // Spracovanie parametrov z url a následne spracovanie celej App
    $router->writeView(); // vypisanie Hlavnej šablony rozloženia Layout

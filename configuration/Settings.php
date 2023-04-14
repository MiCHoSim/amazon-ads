<?php
/*
 * Autor: MiCHo
 */
/**
 * Centrálny súbor na všetky nastavenia
 */
class Settings
{

                                              public static $debug = true;    //či projekt debutujeme na localhoste alebo na produkčnom servery
                                              public static $domain = 'amazon';   // doména, na ktorej bež web
                                              public static $domainName = 'Metalo';   // Názov domény na ktorej beži web
                                              public static $http = 'http';
                                              public static $slogan = 'Amazon App';
                                              public static $db = array ('user' => 'root',
                                                                         'host' => '127.0.0.1',
                                                                         'password' => '',
                                                                         'database' => 'd22155_metalo');    //pristupové údaje
                                              public static $email = 'my.a-z@gmail.com'; // Kontaktný email / email spolocnosti
                                              public static $tel = '1111 111 111';
              /*

                             public static $debug = false;    //či projekt debutujeme na localhoste alebo na produkčnom servery
                             public static $domain = 'adsportal.metalo.sk';   // doména, na ktorej bež web
                             public static $http = 'https';
                             public static $domainName = 'Metalo';   // Názov domény na ktorej beži web
                             public static $slogan = 'Amazon App';
                             public static $db = array ('user' => 'u22155_metalo',
                                 'host' => 'sql10.hostcreators.sk:3315',
                                 'password' => '1U.L0yhh!.0J',
                                 'database' => 'd22155_metalo');    //pristupové údaje
                             public static $email = 'my.a-z@gmail.com'; // Kontaktný email / email spolocnosti
                                              public static $tel = '1111 111 111'; //ktte tel spolocnosti

*/
    // Kontakt na autora webu
    public static $authorWebu = 'MiCHo'; // Autor Webu
    public static $authorEmail = '';//'simalmichal@gmail.com'; // Kontaktný email autor
    public static $authorTel = '';//'0914278743'; // Kontaktny tel autor


}
/*
 * Autor: MiCHo
 */

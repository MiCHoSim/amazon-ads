<?php

use Micho\Utilities\DateTimeUtilities;
use Micho\Utilities\StringUtilities;

/**
 * Trieda/Wraper obaľuje najpouživanejšie formatovacie metódy
 * Class FormatPomocne
 */
class FormatHelper
{
    /**
     ** Vráti den v týzdny ako retazec
     * @param int $dayNumber čislo dna v týŽdny
     * @return string Nazov dna
     */
    public static function dayOfWeek($dayNumber) : string
    {
        return DateTimeUtilities::$dni[$dayNumber];
    }
    /**
     ** Vypiše deň v týždni po slovensky
     * @param $datumCas
     */
    public static function denSlovensky($datum)
    {
        return DateTimeUtilities::denSlovensky($datum);
    }

    /**
     ** Prevedie prvé pismeno textu na veľké
     * @param string $text Text na prevedenie
     * @return string Prevedený text
     */
    public static function prveVelke($text)
    {
        return StringUtilities::firstBig($text);
    }

    /**
     ** Skráti text na požadovanú dĺžku pričom v požadovanej dĺžke na konci reťazca sa nachádzajú tri bodky
     * @param string $text Text na skrátenie
     * @param int $length Požadovaná dĺžka textu
     * @return string Skrátený text
     */
    public static function shorten($text, $length)
    {
        return StringUtilities::shorten($text, $length);
    }

    /**
     ** Sformatuje dátum ľubovoľnej stringovej podoby na tvar: Dnes/Vcera/Zajtra
     * @param string $datum Dátum na sformatovanie
     * @return string Sformatovaná datum
     */
    public static function peknyDatum($datum)
    {
        return DateTimeUtilities::peknyDatum($datum);
    }

    /**
     ** Sformátuje dátum z ľubovoľnej stringovej podoby do tvaru (01.01.2020)
     * @param string $datum Dátum na sformátovanie
     * @return string Sformatovaný dátum
     */
    public static function formatujDatumSlovensko(StringUtilities $datum)
    {
        return DateTimeUtilities::formatujDatum($datum);
    }

    /**
     ** Sformatuje dátum a Čas ľubovoľnej stringovej podoby na tvar: Dnes/Vcera/Zajtra 01:01:01
     * @param  string $datumCas Dátum a čas na sformatovanie
     * @return string Sformatovaná datum a cas
     */
    public static function peknyDatumCas($dat, $format)
    {
        return DateTimeUtilities::peknyDatumCas($dat, $format);
    }

    /**
     ** prevedie DateTime na string
     * @param DateTimeUtilities $datum Dátum
     * @param string $format Format na ktory chem darum formatovať
     * @return string Dátum
     */
    public static function formatujDateTime(DateTimeUtilities $datum, $format = DateTimeUtilities::DATUM_FORMAT)
    {
        return $datum->format($format);
    }

    /**
     ** Formatuje Dátum Čas na poZˇadovaný format
     * @param string $datumCas Dátum čas
     * @param string $format Format uprafvi podla pravidiel DATETIME
     * @return false|string Sformatovaný dátum cas
     */
    public static function formatujDatumCasNaTvar($datumCas, $format)
    {
        return DateTimeUtilities::formatToShape($datumCas, $format);
    }

    /**
     ** Sformatuje dátum  na tvar d.m. Y
     * @param string $dat Datum
     * @return string Datum v tvare d.m. Y
     */
    public static function ciselnyDatum($datum)
    {
        $datumCas = new DateTimeUtilities($datum);
        return $datumCas->format(DateTimeUtilities::DATUM_FORMAT);
    }

    /**
     ** Sformatuje čiastku na  desatinné miesta a pripojí danú menu
     * @param float $ciastka čiastka
     * @param string $mena Mena napr "€"
     * @return string čiastka na 2 desatinné miesta s menou
     */
    public static function mena($ciastka, $mena = '€')
    {
        return number_format($ciastka, 2, ',', ' ') . ' ' . $mena;
    }

    /**
     ** Sformatuje boolean na tvar Áno/Nie
     * @param bool $hodnota Booleoska hodnota
     * @return string Hodnota Áno alebo Nie
     */
    public static function boolean($hodnota)
    {
        return $hodnota ? 'Áno' : 'Ne';
    }

    /**
     ** Vyskloňuje slovo
     * @param string $zaklad Základ slova
     * @param int $pocet Požet položiek / veci
     * @param string $pr Tvar prvého skloňovania
     * @param string $dr Tvar druhého skloňovania
     * @param string $tr Tvar tretieho skloňovania
     * @return string Vyskloňovaný reťazec
     */
    public static function sklonuj($zaklad, $pocet, $pr, $dr , $tr)
    {
        $koncovka = ($pocet == 1) ? $pr : (($pocet >= 2 && $pocet <= 4) ? $dr : $tr);
        return $zaklad . $koncovka;
    }
}
/* Autor: http://www.itnetwork.cz */

/*
 * Niektoré časti sú upravené
 * Autor: MiCHo
 */

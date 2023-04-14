<?php

namespace Micho\Utilities;

use DateTime;
use InvalidArgumentException;

/**
 ** Pomocná trieda na prácu s Dátumom a Časom
 * Class DatumCas
 */
class DateTimeUtilities
{
    /**
     ** Dátum Čas v Slovenskom formáte (1.1.2020 1:01:01)
     */
    const DATUMCAS_FORMAT = 'j.n.Y G:i:s';

    /**
     ** Dátum Čas v Slovenskom formáte (01.01.2020 01:01:01)
     */
    const DATUMCAS_FORMAT_PLNY ='d.m.Y H:i:s';

    /**
     ** Dátum v Slovenskom formáte (1.1.2020)
     */
    const DATUM_FORMAT = 'j.n.Y';

    /**
     * Dátum v Slovenskom formáte (01.01.2020)
     */
    const DATUM_FORMAT_PLNY = 'd.m.Y';

    /**
     ** Čas v Slovenskom formáte: G->(1-24);    i->(00);    s->(00)
     */
    const CAS_FORMAT = 'G:i:s';

    /**
     ** Dátum Čas v Databázovom formáte
     */
    const DB_DATETIME_FORMAT = 'Y-m-d H:i:s';

    /**
     ** Dátum v Databázovom formáte
     */
    const DB_DATE_FORMAT = 'Y-m-d';

    /**
     ** Čas v Databázovom formáte
     */
    const DB_CAS_FORMAT = 'H:i:s';

    /**
     ** Čas v Databázovom formáte
     */
    const DB_CAS_FORMAT_SHORT = 'H:i';

    /**
     ** Pole Slovenských názvov mesiacov
     * @var StringUtilities[]
     */
    public static $mesiace = array('január', 'február', 'marec', 'apríl', 'máj', 'jún', 'júl', 'august', 'september', 'október', 'november', 'december');

    public static $month = array('January' => 1 , 'February' => 2, 'March' => 3, 'April' => 4, 'May' => 5, 'June' => 6,
        'July' => 7,  'August' => 8,  'September' => 9,  'October' => 10,  'November' => 11,  'December' => 12);

    /**
     ** vráti mesiace aj z All
     * @return int[]|string[]
     */
    public static function getMonthFull()
    {
        return ['All' => 'all'] + self::$month;
    }



    /**
     ** Pole Slovenských názvov dni
     * @var StringUtilities[]
     */
    public static $dni = array('Nedeľa', 'Pondelok', 'Utorok', 'Streda', 'Štvrtok', 'Piatok', 'Sobota', 'Nedeľa');

    /**
     ** Chybové hlášky pre jednotlivé formáty
     * @var StringUtilities[]
     */
    private static $chyboveHlasky = array(
        self::DATUM_FORMAT => 'Neplatný dátum, zadajte ho prosím v tvare dd.mm.rrrr',
        self::CAS_FORMAT => 'Neplatný čas, zadajte ho prosím v tvare hh:mm, môžete dodať aj sekundy',
        self::DATUMCAS_FORMAT => 'Neplatný dátum alebo čas, zadajte prosím hodnotu v tvare dd.mm.rrrr hh:mm, prípadne aj sekundy',
    );

    /**
     ** Slovník ako sa formáty medzi sebou prevádzajú
     * @var StringUtilities[]
     */
    private static $formatSlovnik = array(
        self::DATUM_FORMAT => self::DB_DATE_FORMAT,
        self::DATUMCAS_FORMAT => self::DB_DATETIME_FORMAT,
        self::CAS_FORMAT => self::DB_CAS_FORMAT,
    );

    /**
     ** Formatuje Dátum Čas na poZˇadovaný format
     * @param StringUtilities $dateTime Dátum čas
     * @param StringUtilities $format Format uprafvi podla pravidiel DATETIME
     * @return false|StringUtilities Sformatovaný dátum cas
     */
    public static function formatToShape($dateTime, $format)
    {
        return date($format, strtotime($dateTime));
    }

    /**
     ** Vypiše deň v týždni po slovensky
     * @param DateTimeUtilities $datumCas
     */
    public static function denSlovensky($datum)
    {
        if($datum instanceof DateTimeUtilities)
        {
            return self::$dni[$datum->format('w')];
        }
        else
        {
            return self::$dni[(self::vratDatumCas($datum))->format('w')];
        }
    }

    /**
     ** Vráti Mesiac ako Slovo V Slovenčine
     * @param DateTimeUtilities $date Dátum typu DateTime
     * @return StringUtilities Názov mesiaca v slovenčine
     */
    /**
     ** Vráti Mesiac ako Slovo
     * @param DateTimeUtilities|null $date Dátum typu DateTime/ or null
     * @return string Názov mesiaca slovne
     */
    public static function monthVerbal(DateTimeUtilities|null $date = null)
    {
        $date = empty($date) ? new DateTime() : $date;
        return self::getMonthFull()[$date->format('n')-1];
    }


    /**
     ** Vytvor instanciu DateTime vrátane UNIX timestap zo zadaného vstupu
     * @param StringUtilities $datum Reťazec s dátumom, prípadne aj s časom
     * @return DateTimeUtilities Instancia DateTime
     */
    public static function vratDatumCas($datum)
    {
        if(ctype_digit($datum)) // zistenie čí Dátum Čas zadaný ako celé číslo v pripade INIX formátu
            $datum = '@' . $datum; //  INIX formát
        return new DateTimeUtilities($datum);
    }

    /**
     ** Sformátuje dátum z ľubovoľnej stringovej podoby do tvaru (01.01.2020)
     * @param StringUtilities $datum Dátum na sformátovanie
     * @return StringUtilities Sformatovaný dátum
     */
    public static function formatujDatum($datum)
    {
        $datumCas = self::vratDatumCas($datum); // vráti instánciu DateTime
        return $datumCas->format(self::DATUM_FORMAT);
    }

    /**
     ** Sformátuje dátum a čas z ľubovoľnej stringovej podoby do tvaru (01.01.2020 01:01:01)
     * @param StringUtilities $datum Dátum a čas na sformátovanie
     * @return StringUtilities Sformatovaný dátum a čas
     */
    public static function formatujDatumCas($datum)
    {
        $datumCas = self::vratDatumCas($datum); // vráti instánciu DateTime
        return $datumCas->format(self::DATUMCAS_FORMAT_PLNY);
    }


    /**
     ** Sformatuje instanciu DateTime na Formát: Dnes/Vcera/Zajtra
     * @param DateTimeUtilities $datumCas Instancia DateTime
     * @return StringUtilities Sformatovaná hodnota
     */
    private static function vratPeknyDatum($datumCas)
    {
        $teraz = new DateTime(); // aktuálny Dátum a čas
        if ($datumCas->format('Y') != $teraz->format('Y'))
            return $datumCas->format(self::DATUM_FORMAT); // Vráti dátum vo formáte (1.1.2020)

        $denMesiac = $datumCas->format('d-m'); // 01-01
        if ($denMesiac == $teraz->format('d-m'))
            return "Dnes";

        $teraz->modify('-1 DAY'); // nastavý dátum na včerajší
        if ($denMesiac == $teraz->format('d-m'))
            return "Včera";

        $teraz->modify('+2 DAYS'); // nastavý dátum na zajtrajší
        if ($denMesiac == $teraz->format('d-m'))
            return "Zajtra";

        return $datumCas->format('j.') . ' ' .self::$mesiace[$datumCas->format('n') - 1]; //vráti dátum vo formáte (1. január)
    }

    /**
     ** Sformatuje dátum ľubovoľnej stringovej podoby na tvar: Dnes/Vcera/Zajtra
     * @param StringUtilities $datum Dátum na sformatovanie
     * @return StringUtilities Sformatovaná datum
     */
    public static function peknyDatum($datum)
    {
        return self::vratPeknyDatum(self::vratDatumCas($datum));
    }

    /**
     ** Sformatuje dátum a Čas ľubovoľnej stringovej podoby na tvar: Dnes/Vcera/Zajtra 01:01:01
     * @param  StringUtilities $datumCas Dátum a čas na sformatovanie
     * @return StringUtilities Sformatovaná datum a cas
     */
    public static function peknyDatumCas($datumCas, $format = self::DB_CAS_FORMAT)
    {
        $dateTime = self::vratDatumCas($datumCas); // vytvorí instanciu DateTime
        return self::vratPeknyDatum($dateTime) . ' ' . $dateTime->format($format);
    }

    /**
     ** Naparsuje Slovenský dátum a čas podľa zadaného formátu
     * @param StringUtilities $datumCas Slovenský dátum a čas
     * @param StringUtilities $format Formát výstupu
     * @return StringUtilities Dátum a čas v databázovom formáte
     * @throws InvalidArgumentException
     */
    public static function parsujDatumCas($datumCas, $format = self::DATUMCAS_FORMAT)
    {
        if (mb_substr_count($datumCas, ':') == 1) // ak nie sú zadané sekundy pridáme ich k zadanému dátumu
            $datumCas .= ':00';
        // Zmažeme medzery pred alebo za separátormi
        $a = array('/([\.\:\/])\s+/', '/\s+([\.\:\/])/', '/\s{2,}/');
        $b = array('\1', '\1', ' ');
        $datumCas = trim(preg_replace($a, $b, $datumCas));
        // Zmaže nuly pred číslami
        $a = array('/^0(\d+)/', '/([\.\/])0(\d+)/');
        $b = array('\1', '\1\2');
        $datumCas = preg_replace($a, $b, $datumCas);
        // Vytvoří instanci DateTime, která zkontroluje zda zadané datum existuje
        $dateTime = DateTimeUtilities::createFromFormat($format, $datumCas);
        $chyby = DateTimeUtilities::getLastErrors();
        // Vyvolání chyby
        if ($chyby['warning_count'] + $chyby['error_count'] > 0)
        {
            if (array_key_exists($format, self::$chyboveHlasky)) // ak existuju chybove hlašky tak ich vypiše
                throw new InvalidArgumentException(self::$chyboveHlasky[$format]);
            else
                throw new InvalidArgumentException('Neplatná hodnota');
        }
        // Návrat data v MySQL formátu
        return $dateTime->format(self::$formatSlovnik[$format]);
    }

    /**
     ** Zisti, čí je dátumu a Čas validný
     * @param StringUtilities $datumCas Datum a čas
     * @param StringUtilities $format Formát dátumu a Času
     * @return bool či je hodnota validná
     */
    public static function validujDatumCas($datumCas, $format = self::DATUMCAS_FORMAT)
    {
        try
        {
            self::parsujDatumCas($datumCas, $format);
            return true;
        }
        catch (InvalidArgumentException $chyba)
        {
            echo ($chyba->getMessage());
        }
        return false;
    }

    /**
     ** Vráti aktuálny dátum a čas v DB podobe
     * @return StringUtilities Datum v DB podobe
     */
    public static function dbNow()
    {
        $dateTime = new DateTime();
        return $dateTime->format(self::DB_DATETIME_FORMAT);
    }

    /**
     ** Vráti aktuálny dátum DB podobe
     * @return StringUtilities Datum v DB podobe
     */
    public static function dbDatumTeraz()
    {
        $dateTime = new DateTime();
        return $dateTime->format(self::DB_DATE_FORMAT);
    }

    /**
     ** Nastaví dátum na prvý den v mesiaci
     * @param DateTimeUtilities|StringUtilities $datum Dátum
     * @return DateTimeUtilities|false
     */
    public static function prvyDenMesiaca($datum = false)
    {
        if(!($datum instanceof DateTimeUtilities) || !$datum) // ak nieje typu DAte time alebo nieje zadane musim vytvorit inštanciu
            $datum = new DateTime();

        return $datum->modify('first day of this month');
    }

    /**
     ** Nastaví dátum na posledný deň v mesiaci
     * @param DateTimeUtilities|StringUtilities $datum Dátum
     * @return DateTimeUtilities|false
     */
    public static function lastDayOfMonth($datum = false)
    {
        if(!($datum instanceof DateTime) || !$datum) // ak nieje typu DAte time alebo nieje zadane musim vytvorit inštanciu
            $datum = new DateTime();

        return $datum->modify('last day of this month');
    }

    /**
     ** Vráti kolko dni Je medzi dvoma dátumami vrátene obadvoch dni
     * @param StringUtilities $datumOd Dátum v DB podobe OD
     * @param StringUtilities $datumDo Dátum v DB podobe Do
     * @return int|StringUtilities
     * @throws \Exception
     */
    public static function vratPocetDni(StringUtilities $datumOd, StringUtilities $datumDo)
    {
        $datumOd = new DateTimeUtilities($datumOd);
        $datumDo = new DateTimeUtilities($datumDo);
        $pocetDni = $datumOd->diff($datumDo)->format('%a')+1;
        return $pocetDni;
    }

    /**
     ** Vráti aktuálny rok
     * @return StringUtilities Datum v DB podobe
     */
    public static function yearNow()
    {
        $dateTime = new DateTime();
        return $dateTime->format('Y');
    }

}
/* Autor: http://www.itnetwork.cz */

/*
 * Niektoré časti sú upravené
 * Autor: MiCHo
 */

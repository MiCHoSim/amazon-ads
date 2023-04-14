<?php

namespace Micho\Utilities;

/**
 ** Pomocná trieda na prácu s Textovými reťazcami
 * Class Retazec
 */
class StringUtilities
{
    /**
     ** Zistí, či podreťazec začína určitým podreťazcom
     * @param StringUtilities $text Text
     * @param StringUtilities $podretazec Podreťazec
     * @return bool Či text začína podreťacom
     */
    public static function zacina($text, $podretazec)
    {
        return (mb_strpos($text, $podretazec) === 0);
    }

    /**
     ** Zistí, či sa daný podreŤazec nachádza v reťazci
     * @param string $text text v ktorom hľadám
     * @param string $substring podreŤazec ktorý hľadám
     * @return bool či sa nachádza
     */
    public static function contains(string $text, string $substring)
    {
        return(mb_strpos($text, $substring) !== false);
    }




    /**
     ** Zistí, či podreťazec končí určitým podreťazcom
     * @param string $text Text
     * @param string $podretazec Podreťazec
     * @return bool Či text končí podreťacom
     */
    public static function konci(string $text, string $podretazec)
    {
        return ((mb_strlen($text) >= mb_strlen($podretazec)) && ((mb_strpos($text, $podretazec, mb_strlen($text) - mb_strlen($podretazec))) !== false));
    }

    /**
     ** Prvé písmeno textu zmení na veľké
     * @param StringUtilities $text Text, ktorý chceme upraviť
     * @return StringUtilities Zmenení text
     */
    public static function firstBig($text)
    {
        return ucfirst($text);
    }

    /**
     ** Prvé písmeno textu zmení na malé
     * @param StringUtilities $text Text, ktorý chceme upraviť
     * @return StringUtilities Zmenení text
     */
    public static function firstSmall($text)
    {
        return lcfirst($text);
    }

    /**
     ** Skráti text na požadovanú dĺžku pričom v požadovanej dĺžke na konci reťazca sa nachádzajú tri bodky
     * @param StringUtilities $text Text na skrátenie
     * @param int $length Požadovaná dĺžka textu
     * @return StringUtilities Skrátený text
     */
    public static function shorten($text, $length)
    {
        if($length <= 1)
            $text = mb_substr($text, 0, $length) . '.';

        elseif (mb_strlen($text) - 3 > $length)
            $text = mb_substr($text, 0, $length - 3) . '...';
        return $text;
    }

    /**
     ** Odstráni s textu diakritiku
     * @param StringUtilities $text Text s diakritikou
     * @return StringUtilities Text bez diakritikou
     */
    public static function odstranDiakritiku($text)
    {
        $znaky = array(
            chr(195) . chr(128) => 'A', chr(195) . chr(129) => 'A',
            chr(195) . chr(130) => 'A', chr(195) . chr(131) => 'A',
            chr(195) . chr(132) => 'A', chr(195) . chr(133) => 'A',
            chr(195) . chr(135) => 'C', chr(195) . chr(136) => 'E',
            chr(195) . chr(137) => 'E', chr(195) . chr(138) => 'E',
            chr(195) . chr(139) => 'E', chr(195) . chr(140) => 'I',
            chr(195) . chr(141) => 'I', chr(195) . chr(142) => 'I',
            chr(195) . chr(143) => 'I', chr(195) . chr(145) => 'N',
            chr(195) . chr(146) => 'O', chr(195) . chr(147) => 'O',
            chr(195) . chr(148) => 'O', chr(195) . chr(149) => 'O',
            chr(195) . chr(150) => 'O', chr(195) . chr(153) => 'U',
            chr(195) . chr(154) => 'U', chr(195) . chr(155) => 'U',
            chr(195) . chr(156) => 'U', chr(195) . chr(157) => 'Y',
            chr(195) . chr(159) => 's', chr(195) . chr(160) => 'a',
            chr(195) . chr(161) => 'a', chr(195) . chr(162) => 'a',
            chr(195) . chr(163) => 'a', chr(195) . chr(164) => 'a',
            chr(195) . chr(165) => 'a', chr(195) . chr(167) => 'c',
            chr(195) . chr(168) => 'e', chr(195) . chr(169) => 'e',
            chr(195) . chr(170) => 'e', chr(195) . chr(171) => 'e',
            chr(195) . chr(172) => 'i', chr(195) . chr(173) => 'i',
            chr(195) . chr(174) => 'i', chr(195) . chr(175) => 'i',
            chr(195) . chr(177) => 'n', chr(195) . chr(178) => 'o',
            chr(195) . chr(179) => 'o', chr(195) . chr(180) => 'o',
            chr(195) . chr(181) => 'o', chr(195) . chr(182) => 'o',
            chr(195) . chr(182) => 'o', chr(195) . chr(185) => 'u',
            chr(195) . chr(186) => 'u', chr(195) . chr(187) => 'u',
            chr(195) . chr(188) => 'u', chr(195) . chr(189) => 'y',
            chr(195) . chr(191) => 'y',
            // Decompositions for Latin Extended-A
            chr(196) . chr(128) => 'A', chr(196) . chr(129) => 'a',
            chr(196) . chr(130) => 'A', chr(196) . chr(131) => 'a',
            chr(196) . chr(132) => 'A', chr(196) . chr(133) => 'a',
            chr(196) . chr(134) => 'C', chr(196) . chr(135) => 'c',
            chr(196) . chr(136) => 'C', chr(196) . chr(137) => 'c',
            chr(196) . chr(138) => 'C', chr(196) . chr(139) => 'c',
            chr(196) . chr(140) => 'C', chr(196) . chr(141) => 'c',
            chr(196) . chr(142) => 'D', chr(196) . chr(143) => 'd',
            chr(196) . chr(144) => 'D', chr(196) . chr(145) => 'd',
            chr(196) . chr(146) => 'E', chr(196) . chr(147) => 'e',
            chr(196) . chr(148) => 'E', chr(196) . chr(149) => 'e',
            chr(196) . chr(150) => 'E', chr(196) . chr(151) => 'e',
            chr(196) . chr(152) => 'E', chr(196) . chr(153) => 'e',
            chr(196) . chr(154) => 'E', chr(196) . chr(155) => 'e',
            chr(196) . chr(156) => 'G', chr(196) . chr(157) => 'g',
            chr(196) . chr(158) => 'G', chr(196) . chr(159) => 'g',
            chr(196) . chr(160) => 'G', chr(196) . chr(161) => 'g',
            chr(196) . chr(162) => 'G', chr(196) . chr(163) => 'g',
            chr(196) . chr(164) => 'H', chr(196) . chr(165) => 'h',
            chr(196) . chr(166) => 'H', chr(196) . chr(167) => 'h',
            chr(196) . chr(168) => 'I', chr(196) . chr(169) => 'i',
            chr(196) . chr(170) => 'I', chr(196) . chr(171) => 'i',
            chr(196) . chr(172) => 'I', chr(196) . chr(173) => 'i',
            chr(196) . chr(174) => 'I', chr(196) . chr(175) => 'i',
            chr(196) . chr(176) => 'I', chr(196) . chr(177) => 'i',
            chr(196) . chr(178) => 'IJ', chr(196) . chr(179) => 'ij',
            chr(196) . chr(180) => 'J', chr(196) . chr(181) => 'j',
            chr(196) . chr(182) => 'K', chr(196) . chr(183) => 'k',
            chr(196) . chr(184) => 'k', chr(196) . chr(185) => 'L',
            chr(196) . chr(186) => 'l', chr(196) . chr(187) => 'L',
            chr(196) . chr(188) => 'l', chr(196) . chr(189) => 'L',
            chr(196) . chr(190) => 'l', chr(196) . chr(191) => 'L',
            chr(197) . chr(128) => 'l', chr(197) . chr(129) => 'L',
            chr(197) . chr(130) => 'l', chr(197) . chr(131) => 'N',
            chr(197) . chr(132) => 'n', chr(197) . chr(133) => 'N',
            chr(197) . chr(134) => 'n', chr(197) . chr(135) => 'N',
            chr(197) . chr(136) => 'n', chr(197) . chr(137) => 'N',
            chr(197) . chr(138) => 'n', chr(197) . chr(139) => 'N',
            chr(197) . chr(140) => 'O', chr(197) . chr(141) => 'o',
            chr(197) . chr(142) => 'O', chr(197) . chr(143) => 'o',
            chr(197) . chr(144) => 'O', chr(197) . chr(145) => 'o',
            chr(197) . chr(146) => 'OE', chr(197) . chr(147) => 'oe',
            chr(197) . chr(148) => 'R', chr(197) . chr(149) => 'r',
            chr(197) . chr(150) => 'R', chr(197) . chr(151) => 'r',
            chr(197) . chr(152) => 'R', chr(197) . chr(153) => 'r',
            chr(197) . chr(154) => 'S', chr(197) . chr(155) => 's',
            chr(197) . chr(156) => 'S', chr(197) . chr(157) => 's',
            chr(197) . chr(158) => 'S', chr(197) . chr(159) => 's',
            chr(197) . chr(160) => 'S', chr(197) . chr(161) => 's',
            chr(197) . chr(162) => 'T', chr(197) . chr(163) => 't',
            chr(197) . chr(164) => 'T', chr(197) . chr(165) => 't',
            chr(197) . chr(166) => 'T', chr(197) . chr(167) => 't',
            chr(197) . chr(168) => 'U', chr(197) . chr(169) => 'u',
            chr(197) . chr(170) => 'U', chr(197) . chr(171) => 'u',
            chr(197) . chr(172) => 'U', chr(197) . chr(173) => 'u',
            chr(197) . chr(174) => 'U', chr(197) . chr(175) => 'u',
            chr(197) . chr(176) => 'U', chr(197) . chr(177) => 'u',
            chr(197) . chr(178) => 'U', chr(197) . chr(179) => 'u',
            chr(197) . chr(180) => 'W', chr(197) . chr(181) => 'w',
            chr(197) . chr(182) => 'Y', chr(197) . chr(183) => 'y',
            chr(197) . chr(184) => 'Y', chr(197) . chr(185) => 'Z',
            chr(197) . chr(186) => 'z', chr(197) . chr(187) => 'Z',
            chr(197) . chr(188) => 'z', chr(197) . chr(189) => 'Z',
            chr(197) . chr(190) => 'z', chr(197) . chr(191) => 's',
            // Euro Sign
            chr(226) . chr(130) . chr(172) => 'E',
            // GBP (Pound) Sign
            chr(194) . chr(163) => ''
        );
        return strtr($text, $znaky);
    }

    /**
     ** Medzery medzi slovami prevedie na pomlčky
     * @param StringUtilities $text Text na úpravu
     * @return StringUtilities prevedený text
     */
    public static function pomlckuj($text)
    {
        return preg_replace("/\-{2,}/u", "-", preg_replace("/[^a-z0-9]/u", "-", mb_strtolower(self::odstranDiakritiku($text))));
    }

    /**
     ** Prevedie text na CamelCase podľa oddeľovača
     * @param StringUtilities $text Text na prevedenie
     * @param StringUtilities $separator Oddelovač slov
     * @param bool $firstSmall či chcem prvé male
     * @return StringUtilities Prevedený text
     */
    private static function transferredToCamel($text, $separator, $firstSmall = true)
    {
        $result = ltrim(str_replace(' ', '', ucwords(preg_replace('/' . $separator . '/', ' ', $text))), ' ');
        if ($firstSmall)
            $result = self::firstSmall($result);
        return $result;
    }

    /**
     ** Prevedie text z CamelCase podľa oddeľovača
     * @param string $text Text na prevedenie
     * @param string $oddelovac Oddelovač slov
     * @return string Prevedený text
     */
    private static function fromCamelTo(string $text, string $oddelovac)
    {
        return ltrim(mb_strtolower(preg_replace('/[A-Z]/', $oddelovac . '$0', $text)), $oddelovac);
    }

    /**
     ** Prevedie pomlčky na CamelCase
     * @param StringUtilities $text Text na prevedenie
     * @param bool $firstSmall či chcem prvé male
     * @return StringUtilities Prevedený text
     */
    public static function hyphenatedToCamel($text, $firstSmall = true)
    {
        return self::transferredToCamel($text, '-', $firstSmall);
    }

    /**
     ** Prevedie podčiarkovník na CamelCase
     * @param string $text Text na prevedenie
     * @param bool $prveMale či chcem prvé male
     * @return string Prevedený text
     */
    public static function underlineToCamel(string $text, bool $firstSmall = true)
    {
        return self::transferredToCamel($text, '_', $firstSmall);
    }

    /**
     ** Prevedie CamelCase na pomlčky
     * @param string $text Text na prevedenie
     * @return string Prevedený text
     */
    public static function camelNaPomlcky(string $text)
    {
        return self::fromCamelTo($text, '-');
    }

    /**
     ** Prevedie CamelCase na podčiarkovník
     * @param StringUtilities $text Text na prevedenie
     * @return StringUtilities Prevedený text
     */
    public static function camelToUnderline($text)
    {
        return self::fromCamelTo($text, '_');
    }

    /**
     ** Vygeneruje náhodný textový reťazec
     * @param StringUtilities $from ASCII znak od ktorého chceme generovať
     * @param StringUtilities $to ASCII znak do ktorého chceme generovať
     * @param int $length Dĺžka reťazca
     * @return StringUtilities Výsledný reťazec
     */
    private static function randomString($from, $to, $length)
    {
        $string = '';
        if ($length > 1)
            $string .= self::randomString($from, $to, --$length);
        return $string . chr(rand(ord($from), ord($to))); // chr -> získanie znaku pomocou ASCII kodu; ord -> získanie ASCII kodu zo znaku
    }

    /**
     ** Vygeneruje náhodne heslo
     * @param bool $addSpecialChar či chcem pridať špeciálny znak
     * @return StringUtilities Náhodne heslo
     */
    public static function generateNewPassword($addSpecialChar = false)
    {
        $number = self::randomString('0', '9', 3);
        $smallLetter = self::randomString('a', 'z', 2);
        $bigLetter = self::randomString('A', 'Z', 2);
        $specialChar = array('!', '@', '#', '$', '%', '^', '&', '*', '(', ')', '.', ',');
        $password = $number . $smallLetter . $bigLetter;
        if ($addSpecialChar)
            $password .= $specialChar[array_rand($specialChar)];
        return str_shuffle($password);
    }

    /**
     ** Vráti retazec od začiatku po určitý znak
     * @param StringUtilities $string Reťazec z ktorrého získavam podreťazec
     * @param StringUtilities $char Znak po ktorý chem reťazec získať
     * @param false $including či chem vrátit retaže vrátane znaku
     * @return StringUtilities Nový reťazec
     */
    public static function returnToChar($string, $char, $including = false)
    {
        if (($pozicia = mb_strpos($string, $char)) !== false) // ak obsahuje znak tak ho skrátim
            return mb_substr($string, 0, $pozicia + ($including ? (mb_strlen($including) + mb_strlen($char)) : 0));
        return false;
    }

    /**
     ** Vráti reťazec od Znaku po koniec reťazca
     * @param StringUtilities $string Reťazec z ktorrého získavam podreťazec
     * @param StringUtilities $char Znak od ktorého chem reťazec získať
     * @param false $including či chem vrátit retaže vrátane znaku
     * @return StringUtilities Nový reťazec
     */
    public static function returnByChar($string, $char, $including = false)
    {
        if (($pozicia = mb_strpos($string, $char)) !== false)
            return mb_substr($string, $pozicia + ($including ? 0 : mb_strlen($char)), mb_strlen($string));
        return false;
    }

    /**
     ** Vráti retace, ktoré sa nachadzajú medzi dvoma znakmi/retazcami vrátane
     * @param StringUtilities $string Retažec v ktorom hľadám
     * @param StringUtilities $start Začiatočny retazec
     * @param StringUtilities $end Koncový reťazec
     * @param bool $vratane či chem vrátit retaže vrátane znaku
     * @return array|mixed Najdené reťazce
     */
    public static function returnStringBetween($string, $start, $end)
    {
        $stringFromChar = self::returnByChar($string, $start); // reťazec od hľadaného znaku po koniec
        $stringBetween = self::returnToChar($stringFromChar, $end); // reťazec od začiatku po hľadaný znak

        if ($stringBetween)  // ak sa vráti novy reťazec znamená to, že sa koniec našiel
            return $stringBetween;

        return false;
    }

    /**
     ** Nahradí znak za iný
     * @param string $string Reťazec ktorý menim
     * @param string $from Znak ktorý chcem zmeniť
     * @param string $to Znak za ktorý chcem zmeniť
     * @return array|string|string[]
     */
    public static function changeChar(string $string, string $from,string $to)
    {
        return str_replace($from,$to,$string);
    }
}

/* Autor: http://www.itnetwork.cz */

/*
 * Niektoré časti sú upravené
 * Autor: MiCHo
 */
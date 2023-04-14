<?php

/**
 ** Autoloader Automatické načítanie tried
 * @param string $class Plný názov triedy, vrátane menného priestoru
 * @throws Exception Ak sa trieda nenájde
 */
function autoloader($class)
{
    if (mb_strpos($class, '\\') === FALSE && preg_match('/Helper$/', $class)) // nacita pomocne triedy -> není v namespace a končí na Pomocnik
            $class = 'app\\helper\\' . $class;
    elseif (mb_strpos($class, 'App\\') !== FALSE) // načíta triedy z app
            $class = 'a' . ltrim ($class, 'A'); // zmení App na app
    else // načíta ostatné triedy z vendor
        $class = 'vendor\\' . $class;

    $cesta = str_replace('\\', '/', $class) . '.php'; // Nahrada spätného lomítka a pridanie koncovky k triede
    
    //echo $cesta . "<br/>";

    if (file_exists('../' . $cesta)) // nacitanie popripade vyvolanie vynimky
        include('../' . $cesta);
}

spl_autoload_register("autoloader");
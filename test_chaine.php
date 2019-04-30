<?php
/**
 * Fonction pour trouver une chaîne entre 2 balises
 */
function TrouverMaChaine($string, $start, $end){
    $string = ' ' . $string;
    $ini = strpos($string, $start);
    if ($ini == 0) return '';
    $ini += strlen($start);
    $len = strpos($string, $end, $ini) - $ini;
    return substr($string, $ini, $len);
}

$str = strtolower($message);
 // trouve la chaîne entre 2 balises: ':' et '/'
$equipe1 = TrouverMaChaine($str, ":", "/");
$equipe2 = substr(strstr($str,"/"), 1); 

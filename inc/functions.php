<?php
/**
 * Fonction qui formate une date donnée 
 *
 * @param [string] $origDate
 * @return string
 */
function formatDate ($origDate){
    // On définit la langue du site
    setlocale(LC_TIME, 'FR_fr');

    // On formate la date dans la langue choisi
    $newDate = strftime('%A %e %B %Y -%T', strtotime($origDate));

    // On encore en UTF-8 pour gérer les caractères spéciaux
    $newDate = utf8_encode($newDate);

    // On retourne la date formatée 
    return $newDate;
}
/**
 * Cette fonction renvoie un extrait du texte raccourci à la longueur demandée
 *
 * @param string $texte
 * @param integer $longueur
 * @return string
 */
function extrait (string $texte, int $longueur): string {

    // On décode les caractères HTML
    $texte = htmlspecialchars_decode($texte);

    // On supprime le HTML
    $texte = strip_tags($texte);

    // On raccourci le texte
    $texteReduit = mb_strimwidth($texte, 0, $longueur , '...');

    return $texteReduit;
}
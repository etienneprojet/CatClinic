<?php
//test de pages
// Ce fichier est le point d'entrée de votre application
require 'Noyau/Constantes.php';
require 'Noyau/ChargementAuto.php';

// Avant toute sortie à l'écran
BoiteAOutils::demarrerSession();

// Notre fichier htaccess reecrit l'URL pour nous, ctrler contient le controlleur
$S_urlADecortiquer = isset($_GET['url']) ? $_GET['url'] : null; 

Vue::ouvrirTampon(); // on ouvre le tampon d'affichage, les contrôleurs qui appellent des vues les mettront dedans

try
{
    $O_controleur = new Controleur($S_urlADecortiquer);
    $O_controleur->executer();
}
catch (ControleurException $O_exception)
{
    echo ('Une erreur s\'est produite : ' . $O_exception->getMessage());
}

// Les différentes sous-vues ont été "crachées" dans le tampon d'affichage, on les récupère
$contenuPourAffichage = Vue::recupererContenuTampon();

// On affiche le contenu dans la partie body du gabarit général
Vue::montrer('gabarit', array('body' => $contenuPourAffichage));

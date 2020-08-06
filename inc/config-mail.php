<?php
// PHPMailer est orienté Objet
// On appelle ses classes avce use
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// On importe les fichiers de PHPMailer
require_once __DIR__.'/../PHPMailer/src/Exception.php';
require_once __DIR__.'/../PHPMailer/src/PHPMailer.php';
require_once __DIR__.'/../PHPMailer/src/SMTP.php';

// On intancie PHPMailer
$sendmail = new PHPMailer();

// On configure le serveur SMTP
$sendmail->isSMTP();

// On configure l'encodage des caractères en UTF-8
$sendmail->CharSet = "UTF-8";

// On définit l'hôte du serveur
$sendmail->Host = 'localhost';

// On définit le port sur serveur
$sendmail->Port = 1025;
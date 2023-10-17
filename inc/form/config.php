<?php 

// set this or use some constants of your Code/Framework
// define( 'MAILHOST', "smtp.gmail.com" );
// define( 'MAILUSER', "MAILADRESS@gmail.com" ); // musst a valid email adress!
// define( 'MAILPASS', "PASSWORD" );
// define( 'MAILPORT', 465 );
// define( 'MAILPROTO', "ssl" );

//$empfaenger = "recipient01@domain.com, recipient02@domain.com"; // Empfänger (mehrere durch komma getrennt angeben)
$empfaenger = MAILRECIPIENT; // Empfänger (mehrere, komma getrennt angeben)
$mail_format = "html"; // plain oder html email versenden
$mailtemplatehtml = "mail_template_html.php"; // html template file which should be used. Empty uses defaults
$mailtemplateplain = ""; // plain template file which should be used. Empty uses defaults

$mail_sender = "sendmail";  // smtp oder sendmail zum versenden benutzen
$wartezeit = 5; // wartezeit zum weiterleiten nach erfolgreichem versenden 
                // (0 = keine weiterleitung)
//$weiterleitung = "index.php"; // hierhin weiterleiten
$weiterleitung = "index.php"; // hierhin weiterleiten
$subjectfields = MAILSUBJECT;
//$subjectfields = array( // Betrefffeld Texte, bie NULL ist das subjectfeld deaktiviert
//   'Allgemeine Frage',
//   'Verbesserungsvorschlag',
//   'Fehler melden',
//   'Werbeanfrage'
// );

// Spamm Abwehr
$captcha = false; // use a simple captcha
$maxchars = 1000; // Maximale Anzahl an Textzeichen einer Email. 
$replacechars = false; // Ersetzten von Sonderzeichen wie ä ö ü durch ae oe ue
// einfacher Wortfilter, mails können nur abgesendet werden wenn keines dieser Wörter im Text vorkommt. Leeres array deaktiviert den filter
// Die erkennund würde auf "domain.ch besuchen" anspringen bei der suche nach ".ch", jedoch nciht wenn dieses in einem Wort wie z.B. ".character" vorkommt! 
$badwords = array( 'Shipping', 'shipping', 'click', 'Click', 'CLICK', 'http://', 'https://', 'www.', '.com', '.ru', '.it', '.ch' );
$languageaccept = array( 'de' ); // Nur diese Sprachen (Browser Languages) dürfen Mails versenden. Leere deatkiviert den Sparnfilter
$browseraccept = array( // erkennt normale Broswer und lässt die meisten bots nicht passieren. Leeres Array deaktiviert die Brosererkennung
  'MSIE' => 'Internet explorer',
  'Trident' => 'Internet explorer',
  'Firefox' => 'Mozilla Firefox',
  'Chrome' => 'Google Chrome',
  'Opera Mini' => 'Opera Mini',
  'Opera' => 'Opera',
  'Safari' => 'Safari'
); // Diese Browser dürfen Mails versenden, leer deactiviert die Browsererkennung

// Pfade und URLs
$protocol = isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] == 'on' ? 'https://' : 'http://';
$base = $protocol . $_SERVER['SERVER_NAME'];
$hostdomain = $base . "/";

// Bilder, müssen immer komplette Links sein da diese in den E-Mails eingebettet werden!
// $img_header = $base . "/inc/form/img/mail_header.png";
// $img_intext = $base . "/inc/form/img/brille.png";
$img_header = MAILHTMLHEADER;
$img_intext = MAILHTMLIMAGE;

$twitter_url = MAILTWITTER;
$facebook_url = MAILFACEBOOK;
$instagram_url = MAILINSTAGRAM;
$youtube_url = MAILYOUTUBE;

$company_name = APPDOMAIN;
$slogan = APPSLOGAN;
$company_phone = MAILPHONE;
$company_fax = MAILFAX;
$company_mail = MAILRECIPIENT;
$company_url = APPDOMAIN;
$company_url_raw = $_SERVER['SERVER_NAME']; // we use the hostname of the server for link naming!

?>

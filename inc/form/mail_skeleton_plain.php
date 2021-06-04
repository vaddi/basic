<?php

// // Werten werden automatisch beim Absenden gesetzt
// $timestamp       =  time();
// $datum           =  date( "d.m.Y", $timestamp ); // Das Datum, Bsp.: 22.07.2020
// $uhrzeit         =  date( "H:i:s", $timestamp ); // Die Uhrzeit, Bsp.: 22:52:46
// $ip              =  $_SERVER['REMOTE_ADDR']; // Absender IP
// $host            =  gethostbyaddr( $ip ); // Absender Hostname
//
// //// In config.php gesetzt
// $hostdomain      =  "https://testlabs.com";
// $twitter_url     =  "<a target='_blank' href='https://twitter.com/'>wir auf Twitter</a> | ";
// $facebook_url    =  "<a target='_blank' href='https://facebook.com/'>wir auf Facebook</a> | ";
// $company_name    =  "testlabs.com";
// $company_url     =  "https://www.testlabs.com/";
// $company_string  =  "<em>Copyright &copy; ".date("Y", $timestamp)." ". $company_name ."<br />Alle Rechte vorbehalten.</em><br><br>";
//
// $slogan          =  "Der Firmenslogan";
// $company_phone   =  "<strong>Telefon</strong>: &nbsp;0531 123 123<br>";
// $company_fax     =  "<strong>Fax: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong>0531 123 122<br>";
// $company_mail    =  "<strong>Mail: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong><a class='moz-txt-link-abbreviated' href='mailto:info@testlabs.com'>info@testlabs.com</a><br>";
// $emailTo         =  "Der Emailabesnder";
// $img_header      =  "https://art-tablo.com/inc/form/img/mail_header_exigem.png";
// $img_intext      =  "https://art-tablo.com/inc/form/img/brille.png";
//
// // Werte aus den Formularfeldern
// $name            =  "Der Absendername";
// $email           =  "inf@textlabs.com";
// $subject         =  "Der Betreff (sofern nicht leer)";
// $comments        =  "Testnachricht von lokal versendet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.
//
// In diesem Sinne
// öäp
// ÖÄÜ
// ß
//
// Bye
// ";

$body = "Eine neue Nachricht von: $name <$email>\n
Nachricht: \n
$comments 

Versendet am $datum um $uhrzeit \nvon $ip ($host) \n

Hochachtungsvoll 
Ihr Server $hostdomain
";

?>

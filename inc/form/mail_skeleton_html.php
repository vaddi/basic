<?php

// Werten werden automatisch beim Absenden gesetzt
$timestamp       =  time();
$datum           =  date( "d.m.Y", $timestamp ); // Das Datum, Bsp.: 22.07.2020
$uhrzeit         =  date( "H:i:s", $timestamp ); // Die Uhrzeit, Bsp.: 22:52:46
$ip              =  $_SERVER['REMOTE_ADDR']; // Absender IP
$host            =  gethostbyaddr( $ip ); // Absender Hostname

//// In config.php gesetzt
$hostdomain      =  "https://testlabs.com";
$twitter_url     =  "<a target='_blank' href='https://twitter.com/'>wir auf Twitter</a> | ";
$facebook_url    =  "<a target='_blank' href='https://facebook.com/'>wir auf Facebook</a> | ";
$company_name    =  "testlabs.com";
$company_url     =  "https://www.testlabs.com/";
$company_string  =  "<em>Copyright &copy; ".date("Y", $timestamp)." ". $company_name ."<br />Alle Rechte vorbehalten.</em><br><br>";

$slogan          =  "Der Firmenslogan";
$company_phone   =  "<strong>Telefon</strong>: &nbsp;0531 123 123<br>";
$company_fax     =  "<strong>Fax: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong>0531 123 122<br>";
$company_mail    =  "<strong>Mail: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong><a class='moz-txt-link-abbreviated' href='mailto:info@testlabs.com'>info@testlabs.com</a><br>";
$emailTo         =  "Der Emailabesnder";
$img_header      =  "https://art-tablo.com/inc/form/img/mail_header_exigem.png";
$img_intext      =  "https://art-tablo.com/inc/form/img/brille.png";

// Werte aus den Formularfeldern
$name            =  "Der Absendername";
$email           =  "Die E-Mail des Absende";
$subject         =  "Der Betreff (sofern nicht leer)";
$comments        =  "Testnachricht von lokal versendet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. <br />
<br />
In diesem Sinne <br />
öäp <br />
ÖÄÜ <br />
ß <br />
<br />
Bye <br />
";

$body = '<!DOCTYPE html>
<html lang="de">
<head>
  <title>Nachricht von '. $name .'</title>
  <meta http-equiv="content-type" content="text/html; charset=utf-8">
  <meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0" />
  <style>
    body { background: #FAFAFA; color: #505050; width:100% !important; font-size: 100%; font-family: Arial; -webkit-text-size-adjust:none; margin:0; padding:0; }
    img { }
    h1 { font-size:30px;	font-weight:bold; line-height:100%; margin: 35px 0 25px; }
    h1 small:before { content: "\A"; white-space: pre; color:#ccc; }
    h1 small { color: #ccc; }
    #container { width: 75%; max-width: 600px; margin: 20px auto 40px; line-height:150%; }
    #preHeader { padding: 0 15px 10px; }
    #preHeader, 
    #footer { font-size:13px; line-height:100%; color:#707070; }
    #header { max-height: 145px; min-height: 145px; margin: 0 0 -7px 0; background: url("' . $img_header  . '"); }
    #header h1 { padding: 50px 0 0 15px; margin: 0; }
    a, a:link { text-decoration: none; color: #f66; }
    a:hover, a:focus { text-decoration: none; color: #f00; }
    #content { border: 1px solid #ccc; background: #FFFFFF; padding: 0 15px 20px; text-align: justify; font-size:14px;  }
    .imgWrap { max-width: 128px; max-height: 128px; float: right; margin: 0 0 10px 10px; }
    .imgWrap img { width: 100%; height: 100%; }
    #textArea {  }
    #contentDivider { background: #FAFAFA; margin: 10px 0; padding: 10px 0; text-align: center; }
    #footer { text-align: center; padding: 10px 0 40px; }
    #contentFooter { line-height: 125%; font-size:12px; color:#707070; }
    .right { float: right; }
    @media only screen and (max-device-width: 480px), only screen and (max-width: 480px) {
      #container { width: 100%; margin: 0 auto 0; }
      #preHeader { padding: 15px 15px 10px; }
    }
    @media only screen and (max-device-width: 330px), only screen and (max-width: 330px) {
      #container { width: 100%; }
    }
	</style>
</head>
  
<body>

<div id="container">

  <div id="preHeader">
    <span class="right"><a href="'. $company_url .'" target="_blank">'. $company_name .'</a></span>
    <span>Webformular Nachricht von '. $name .'</span>
  </div>
  <div id="header">
    <h1><a href="'.$company_url.'">'.$company_name.'</a></h1>
  </div>
  
  <div id="content">
    <h1>Webformular Nachricht <small>von '. $name .'</small></h1>
    <div class="imgWrap">
      <img src="' . $img_intext  . '" alt="" />
    </div>
    <div id="textArea">
      ' . $comments . '
    </div>
    <div id="contentDivider">
      '. $twitter_url .'
      '. $facebook_url .'
      <a href="mailto:'.$email.'">Antwort an '.$name.'</a>
    </div>
    <div id="contentFooter">
      <div class="right">
        ' . $slogan . '
      </div>
      <div class="">
        ' . $company_string .'
        ' . $company_phone . '
        ' . $company_fax . '
        ' . $company_mail . '
        <strong>Internet:</strong> <a href="'.$company_url.'">'.$company_name.'</a>
      </div>
    </div>
  </div>
  
  <div id="footer">
    Versendet am '. $datum .' um '. $uhrzeit .'<br />
    von '. $ip .' ('. $host .').
  </div>

</div>

</body>
</html>
';

echo $body;
?>

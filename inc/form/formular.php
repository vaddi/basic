<?php

// Einstellungen bitte in der Config vornehmen
include('config.php');

if( isset( $_COOKIE["commitcookie"] ) && $_COOKIE["commitcookie"] != NULL ) {

  // Check for 
	// $file = "/usr/share/php/Mail.php"; // ubuntu & debian
  $file = "/Users/Shared/Relocated Items/Security/share/pear/Mail.php"; // OSX
  if( $mail_sender === 'smtp' && ! is_file( $file ) ) {
		$genericError = 'You musst install PHP Pear Mail and Net_SMTP by Hand: <br /><br />sudo /usr/bin/pear install Mail Net_SMTP';
		$hasError = true;
  }

  if( $captcha ) {
    session_start();
    $sessionstringnew = null;
    $sessionstringadd = null;
    if( ! isset( $_COOKIE[ session_name() ] ) ) {
      $sessionstringnew = '?' . session_name() . "=" . session_id();
      $sessionstringadd = '&amp;' . session_name() . "=" . session_id();
    }
    if( isset( $_POST['code'] ) && $_POST['code'] != null && $_POST['code'] != "" ){
      $valid = sha1(trim(strip_tags(strtoupper($_POST['code']))));
    } else {
      $valid = null;
    }
    //$revalid = $_SESSION['P91Captcha_code'];
  }

  if( $replacechars ) {
    // simple replacing arrays
    $r1 = array("Ä","ä","Ü","ü","Ö","ö","ß","@","€","\$","’","…","µ");
    $r2 = array("Ae","ae","Ue","ue","Oe","oe","ss","[at]","Euro","Dollar","'","...","[mu]"); // Simple by vaddi
  }

  // default error variables
  $nameError    = '';
  $emailError   = '';
  $commentError = '';
  $captchaError = '';
  $subjectError = '';

  // if form is submitted
  if( isset( $_POST['submitted'] ) ) {

  	// captcha 
    if( $captcha ) {
    	if( empty( $_POST['code'] ) ) {
    		$captchaError = 'Sie haben kein Captcha eingegeben.';
    		$hasError = true;
    	} else if( sha1( trim( strip_tags( strtoupper( $_POST['code'] ) ) ) ) != $_SESSION['P91Captcha_code'] ) {
    		$captchaError = 'Das Captcha ist falsch, bitte nochmal!';
    		$hasError = true;
    	}
    }

  	// require a name from user
  	if( trim( $_POST['contactName'] ) === '' ) {
  		$nameError =  'Bitte geben Sie einen Namen ein!'; 
  		$hasError = true;
  	} else {
  		$name = trim( $_POST['contactName'] );
      if( $replacechars ) $name = str_replace( $r1, $r2, $name );
  		$name = str_replace( '[^\w\s[:punct:]]*', ' ', $name );
  	}

  	// need valid email
  	if( trim( $_POST['email'] ) === '' )  {
  		$emailError = 'Bitte geben Sie eine g&uuml;ltige email Adresse ein.';
  		$hasError = true;
  	} else if( ! preg_match( "/^[[:alnum:]][a-z0-9_.-]*@[a-z0-9.-]+\.[a-z]{2,4}$/i", trim( $_POST['email'] ) ) ) {
  		$emailError = 'Sie haben keine g&uuml;ltige email Adresse eingegeben.';
  		$hasError = true;
  	} else {
  		$email = trim( $_POST['email'] );
  	}

  	// subject fields
    if( $subjectfields ) {
    	if( trim( $_POST['subject'] ) === '' || trim( $_POST['subject'] ) === "none" ) {
    		$subjectError =  'Bitte geben Sie einen Betreff ein!'; 
    		$hasError = true;
    	} else {
        $subject = $subjectfields[ $_POST['subject'] ];
    		// $subject = trim( $_POST['subject'] ); // Text subject validation
        // if( $replacechars ) $subject = str_replace( $r1, $r2, $subject );
        // $subject = str_replace( '[^\w\s[:punct:]]*', ' ', $subject );
    	}
    }

  	// we need at least some content, the mailtest
  	if( trim( $_POST['comments'] ) === '' ) {
  		$commentError = 'Bitte geben Sie eine Nachricht an uns ein!';
  		$hasError = true;
  	} else {
      $commenttext = trim( $_POST['comments'] );
      if( strlen( $commenttext ) >= $maxchars ) {
        // message to long
    		$commentError = 'Ihre Nachricht ist zu Lang, bitte kürzen Sie den Text.!';
    		$hasError = true;
      } else {
        // process comment
      	if( function_exists( 'stripslashes' ) ) {
      		$comments = stripslashes( trim( $_POST['comments'] ) );
      		//$comments = stripslashes( trim( strip_tags( $_POST['comments'] ) ) );
          if( $replacechars ) $comments = str_replace( $r1, $r2, $comments );
      		if( $mail_format == "html" ) $comments = str_replace( "\n", "<br />", $comments );
      		$comments = str_replace( '[^\w\s[:punct:]]*', ' ', $comments );
      	} else {
      		$comments = trim( $_POST['comments'] );
      	}
      }
  	}
  
    // badword filter
    if( ! empty( $badwords ) && isset( $comments ) ) {
      foreach ( $badwords as $key => $word ) {
        if( strpos( $comments, $word ) !== false ) {
          $length = strlen( $word );
          $tmpword = substr( $comments, strpos( $comments, $word ), $length +1 );
          $tmpchar = substr( $tmpword, -1);
          if( $tmpchar === " " || $tmpchar === "\n" ) {
            // found a bad word
        		$commentError = 'Sie haben ein Wort verwendet das nicht erlaubt ist: "' . $word . "\"<br />\nBitte entfernen Sie das Wort und versuchen es erneut.";
        		$hasError = true;
            break;
          }
        }
      }
    }

  	// upon no failure errors let's email now!
  	if( ! isset( $hasError ) ) {
  		$ip = $_SERVER['REMOTE_ADDR'];
  		$host = gethostbyaddr( $ip );
  		$timestamp = time();
  		$datum = date( "d.m.Y", $timestamp );
  		$uhrzeit = date( "H:i:s", $timestamp );
  		$emailTo = html_entity_decode( $empfaenger, null, 'UTF-8' ); // set recipints in config.php!
      if( ! $subjectfields ) {
        $subject = 'Webformular Nachricht von '.$name;
      }

      // templating the Message
      if( $mail_format == "html" ) {
        // html mail
        $twitter_url = empty( $twitter_url ) || $twitter_url == "https://twitter.com/" ? '' : '<a target="_blank" href="'. $twitter_url .'">auf Twitter folgen</a> | ';
        $facebook_url = empty( $facebook_url ) || $facebook_url == "https://facebook.com/" ? '' : '<a target="_blank" href="'. $facebook_url .'">Facebook</a> | ';
				$instagram_url = empty( $instagram_url ) || $instagram_url == "https://www.instagram.com/" ? '' : '<a target="_blank" href="'. $instagram_url .'">Instagram</a> | ';
				$youtube_url  = empty( $youtube_url ) || $youtube_url == "https://www.youtube.com/" ? '' : '<a target="_blank" href="'. $youtube_url .'">YouTube</a> | ';

        $company_string = empty( $company_name ) ? '' : '<em>Copyright &copy; '.date("Y", $timestamp).' '. $company_name .'<br />Alle Rechte vorbehalten.</em><br><br>';
        $slogan = empty( $slogan ) ? '' : '<em>' . $slogan . '</em>';
        $company_phone = empty( $company_phone ) ? '' : '<strong>Telefon</strong>: &nbsp;'. $company_phone .'<br>';
        $company_fax = empty( $company_fax ) ? '' : '<strong>Fax: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong>'. $company_fax .'<br>';
        $company_mail = empty( $company_mail ) ? '' : '<strong>Mail: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong><a class="moz-txt-link-abbreviated" href="mailto:'.$company_mail.'">'.$company_mail.'</a><br>';
        if( $mailtemplatehtml != "" && is_file( __DIR__ . "/" . $mailtemplatehtml ) ) { 
          include( __DIR__ . "/" . $mailtemplatehtml );
        } else {
          // use default
          include( 'mail_template_html.php' );
        }
  	  } else {
        // plain mail
        if( $mailtemplateplain != "" && is_file( __DIR__ . "/" . $mailtemplateplain ) ) { 
          include( __DIR__ . "/" . $mailtemplateplain );
        } else {
  	      include( 'mail_template_plain.php' );
        }
  	  }

      //
      // sending the E-Mail
      //
      if( $mail_sender == "smtp" ) {
        // use PHP pear SMPT
        $local_recipients = explode( ', ', $empfaenger );
        try {
          include __DIR__ . '/Mail.php';
          $emailSent = smtpSendMail::smtpMail( $local_recipients, $body, $subject, $email, $mail_format );
        } catch( Exception $e) {
          echo "<pre>Exception: \n", $e->getMessage(), "</pre>\n";
        }
      } else {
        // use sendmail
      	$headers = "From: $name <$email>\n";
      	if( $mail_format == "html" ) {
      	  $headers .= "Content-Type: text/html\n";
      	} else {
      	  $headers .= "Content-Type: text/plain\n";
      	}
        $headers .= "Content-Transfer-Encoding: 8bit\n";
      	$emailSent = mail( $emailTo, $subject, $body, $headers );
      }

  	}
  }

} else {
  // commitcookie missing (NULL)
	$cookieError = 'Ihr Browser muss Cookies dieser Seite zulassen um uns eine Nachricht zukommen zu lassen.';
	$hasError = true;
}

// Language Filter
if( ! empty( $languageaccept ) ) {
  $lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2); // get language from Browser
  if( $lang === NULL || ! in_array( $lang, $languageaccept ) ) {
    $langError = 'Ihr Browser verwendet leider eine nicht zugelassene Sprache: ' . $lang . "<br /><br />\nStellen Sie die Browsersprache um, oder versuchen Sie es mit einem anderen Browser.";
    $hasError = true;
  }
}

// Browserfilter
if( ! empty( $browseraccept ) ) {
  $ua = $_SERVER['HTTP_USER_AGENT'];
  $browser = NULL;
  foreach( $browseraccept as $short => $long ) {
    if( strpos( $ua, $short ) !== FALSE ) $browser = $long;
  }
  if( $browser === NULL || ! in_array( $browser, $browseraccept ) ) {
    $browserError = 'Ihr Browser ist leider nicht zugelassen für den Mailversand. Bitte verwenden Sie einen anderen.';
    $hasError = true;
  }

}

echo '<script type="text/javascript">';
if( $captcha ) {
  echo 'function P91Captcha( sid ){
	  let pas = new Image();
	  let heuri = new Date();
	  pas.src = "inc/form/captcha_form.php?x=" + heuri.getTime() + sid;
	  document.getElementById( "P91Captcha" ).src = pas.src;
  }';
}
echo 'document.addEventListener("DOMContentLoaded", function() {
  textareaCounter( "commentsText", ' . $maxchars . ' );
});';
echo '</script>';

?>


<style type="text/css">
/* Contactform */

#contact {
	max-width:300px;
	margin:0 auto; 
}
#contact .desc h2 {  font-weight: normal;  color: #444; margin-bottom: 8px; }
#contact .desc p {  color: #333; line-height: 1.3em; margin-bottom: 15px; }
#contact .formblock { display: block; margin-bottom: 11px; }
#contact .formblock label { display: block; font-style: italic; font-weight: normal; color: #232323;  color: #5f6d7a; margin:0 0 2px 0; }
#contact .formblock .txt { padding: 4px 6px; color: #666; width: 264px; }
#contact .formblock .txtarea { font-size: 10pt; padding: 4px 6px; color: #666; width: 264px; height: 140px; max-height:300px; max-width:590px; min-width: 264px; min-height: 140px;}
#contact .formblock .error {  font-style: normal; color: #9d3131; }
#contact .info { font-weight: bold;  color: #59913d; margin-bottom: 10px;  }
#contact .formblock .code { padding: 4px 6px; color: #666; }
#contact p.tick { font-style: italic;  color: #3e669c; }
#contact .subbutton { padding: 3px 7px;  font-weight: bold; color: #565656; }
#content textarea.txtarea { min-width: 156px; min-height: 40px; width: 156px; height: 40px; max-width: 264px; max-height: 140px;}
#subject { color: #666; border: 1px solid #666; background: #F9F9F9; width: 278px !important; border-radius: 6px; padding: 4px 6px;}
*.inputError { border: 1px solid #f00 !important; }
.alert { text-align: justify; background: #ffd1d1; border: 1px solid #f00; border-radius: 5px; padding: 7px 14px; margin: 0 20px 0 0; }
</style>

<!-- @begin contact -->
<div id="contact" class="section fade-in">

  <div class="container">

  <?php include('noscript.php'); ?>

  <?php if( isset( $hasError ) && isset( $browserError ) ) { ?>
    <p class="alert">
      <?= $browserError ?>
    </p>
  <?php } else if( isset( $hasError ) && isset( $langError ) ) { ?>
    <p class="alert">
      <?= $langError ?>
    </p>
  <?php } else if( isset( $hasError ) && isset( $cookieError ) ) { ?>
    <p class="alert">
      <?= $cookieError ?>
      <br /><br />
      Diese Website verwendet Cookies zur Verifizierung des Captchas im Kontaktformular. <a href="?page=imprint#datenschutz">Mehr Informationen</a>.<br /><br />
      <div class="cookieConsentContact"><a class="cookieConsentOK">Ich habe verstanden</a></div>
    </p>
  <?php } else if( isset( $hasError ) && isset( $genericError ) ) { ?>
    <p class="alert">
      <?= $genericError ?>
    </p>
  <?php } else { ?>

  	<?php if( isset( $emailSent ) && $emailSent == true ) { ?>

  	  <?php if( $wartezeit != 0 ) { ?>
  	    <meta http-equiv="refresh" content="<?= $wartezeit ?>; url=<?= $weiterleitung ?>" />
  	  <?php } ?>
      <p class="info">Ihre Nachricht wurde erfoglreich versendet.</p>
    
    <?php } else { ?>

      <div id="contact-form">

  			<p></p>
	    
        <form id="contact-us" action="<?= $_SERVER['SCRIPT_NAME'] . "?" . $_SERVER['QUERY_STRING'] ?>" method="post">
	    
  	      <div class="formblock">
  		      <input type="text" name="contactName" id="contactName" value="<?php if( isset( $_POST['contactName'] ) ) echo $_POST['contactName']; ?>" class="txt requiredField <?php if( $nameError != '' ) { echo 'inputError'; } ?>" placeholder="Name" />
  		      <?php if( $nameError != '' ) { ?>
  		        <br /><span class="error"><?php echo $nameError; ?></span> 
  		      <?php } ?>
  	      </div>
                        
  	      <div class="formblock">
  		      <input type="text" name="email" id="email" value="<?php if( isset( $_POST['email'] ) )  echo $_POST['email']; ?>" class="txt requiredField email <?php if( $emailError != '' ) { echo 'inputError'; } ?>" placeholder="Email" />
  		      <?php if( $emailError != '' ) { ?>
  		        <br /><span class="error"><?php echo $emailError; ?></span>
  		      <?php } ?>
  	      </div>
    
 
          <?php if( $subjectfields ) { ?>
            <div class="formblock">
    		      <select name="subject" id="subject" class="txt requiredField subject <?php if( $subjectError != '' ) { echo 'inputError'; } ?>">
                <option value="none">--- Bitte w&auml;hlen ---</option>
                <?php foreach( $subjectfields as $key => $subjectfield ) { ?>
                  <option value="<?= $key ?>" <?= isset( $_POST['subject'] ) && $_POST['subject'] == $key ? "selected" : "" ?>><?= $subjectfield ?></option>
                <?php } ?>
    			    </select>
    		      <?php if( $subjectError != '' ) { ?>
    		        <br /><span class="error"><?php echo $subjectError; ?></span>
    		      <?php } ?>
    	      </div>
          <?php } ?>

  	      <div class="formblock">

  		      <textarea name="comments" id="commentsText" class="txtarea requiredField <?php if( $commentError != '' ) { echo 'inputError'; } ?>" placeholder="Nachricht" maxlength="<?= $maxchars ?>"><?php if(isset($_POST['comments'])) { if( function_exists( 'stripslashes' ) ) { echo stripslashes( $_POST['comments'] ); } else { echo $_POST['comments']; } } ?></textarea>
  		      <?php if( $commentError != '' ) { ?>
  		        <br /><span class="error"><?php echo $commentError; ?></span> 
  		      <?php } ?>
  	      </div>
          
          <?php if( $captcha ) { ?>
    	      <div class="formblock">
    		      <img src="inc/form/captcha_form.php<?= $sessionstringnew; ?>" alt="Captcha" id="P91Captcha" />
    		      <br /><a href="javascript:P91Captcha('<?= $sessionstringadd; ?>');">Neuer Code?</a>
    		      <br /><br />
    		      <input type="text" name="code" id="code" class="text requiredField code <?php if( $captchaError != '' ) { echo 'inputError'; } ?>" maxlength="50" placeholder="Captcha-Code" />
    		      <?php if( $captchaError != '' ) { ?>
    		        <br /><span class="error"><?php echo $captchaError; ?></span> 
    		      <?php } ?>
    	      </div>
          <?php } ?>

  	      <div class="formblock">
  	        <button name="submit" type="submit" class="subbutton">Absenden</button>
  	        <input type="hidden" name="submitted" id="submitted" value="true" />
  	      </div>

  	    </form>			
  	  </div>
				
  	  <?php } ?>

    <?php } ?>
	</div>
</div><!-- End #contact -->

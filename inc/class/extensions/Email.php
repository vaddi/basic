<?php

// Class <strong>Mail</strong> will be used to send Emails

class Email {

	private static $_error = array(); // collected errors
	private static $_contact = array(); // form Field data
	private static $_emailSent = false;
	
//	private static $_empfaenger = MAILRECIPIENT; // Empfänger (mehrere, komma getrennt angeben)
	private static $_captcha = true;
	private static $_maxchars = 1000; // max amount of allowed characters 
//	private static $_subjectfields = MAILSUBJECT;
	private static $_weiterleitung = "index.php"; // hierhin weiterleiten
	
	private static $_mail_format = 'html'; // html or plain
	private static $_mailtemplatehtml = "mail_template_html.php"; // html template file which should be used. Empty uses defaults
	private static $_mailtemplateplain = "mail_template_plain.php"; // plain template file which should be used. Empty uses defaults
	
	private static $_mail_sender = "smtp";  // smtp oder sendmail zum versenden benutzen
	private static $_wartezeit = 5; // wartezeit zum weiterleiten nach erfolgreichem versenden
	                								// (0 = keine weiterleitung)

	private static $_replacechars = false;
  private static $_r1 = array("Ä","ä","Ü","ü","Ö","ö","ß","@","€","\$","’","…","µ");
  private static $_r2 = array("Ae","ae","Ue","ue","Oe","oe","ss","[at]","Euro","Dollar","'","...","[mu]"); // Simple by vaddi
	
	private static $_badwords = array( 'Shipping', 'shipping', 'click', 'Click', 'CLICK', 'http://', 'https://', 'www.', '.com', '.ru', '.it', '.ch' );
	private static $_languageaccept = array( 'de' ); // Nur diese Sprachen (Browser Languages) dürfen Mails versenden. Leere deatkiviert den Sparnfilter
	private static $_browseraccept = array( // erkennt normale Broswer und lässt die meisten bots nicht passieren. Leeres Array deaktiviert die Brosererkennung
	  'MSIE' => 'Internet explorer',
	  'Trident' => 'Internet explorer',
	  'Firefox' => 'Mozilla Firefox',
	  'Chrome' => 'Google Chrome',
	  'Opera Mini' => 'Opera Mini',
	  'Opera' => 'Opera',
	  'Safari' => 'Safari' // will also trigger on apple Chrome
	); // Diese Browser dürfen Mails versenden, leer deactiviert die Browsererkennung

	// captchavars
	private static $_sessionstringnew = '';
	private static $_sessionstringadd = '';

	/**
	 * create a html Form
	 */
	public static function formular() {

		$content = '';

		// commit cookie setted
		$commitCookie = self::commitCookie();

		// Language Filter
		if( ! empty( self::$_languageaccept ) ) {
		  $lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2); // get language from Browser
		  if( $lang === NULL || ! in_array( $lang, self::$_languageaccept ) ) {
		    self::$_error['langError'] = 'Unfortunately, your browser is using an unauthorized language: ' . $lang . "<br /><br />\nChange the browser language or try a different browser.";
		  }
		}
		
		// Browserfilter
		if( ! empty( self::$_browseraccept ) ) {
		  $ua = $_SERVER['HTTP_USER_AGENT'];
		  $browser = NULL;
			$browser_short = NULL;
		  foreach( self::$_browseraccept as $short => $long ) {
		    if( strpos( $ua, $short ) !== false ) {
					$browser = $long;
					$browser_short = $short;
		    }
		  }
		  if( $browser === NULL || ! in_array( $browser_short, self::$_browseraccept ) ) {
		    self::$_error['browserError'] = 'Unfortunately, your browser is not permitted to send emails. Please use another one.';
		  }
		}
		
		// if request send
		$submitted = self::submitted();

		// captcha javascript
		self::captchaJs();

		// charcounter.js - ensure maxchars will be used
		$content = '<script type="text/javascript">' . "\n";
		$content .= 'document.addEventListener("DOMContentLoaded", function() {' . "\n";
		$content .= '  textareaCounter( "commentsText", ' . self::$_maxchars . ' );' . "\n";
		$content .= '});' . "\n";
		$content .= '</script>' . "\n";

		// add Css
		self::formCss();

		// formfields
		$content .= '<div id="contact" class="section fade-in">' . "\n";
		$content .= '  <div class="container">' . "\n";

		// error outputs ()
		if( count( self::$_error ) > 0 ) {
			if( isset( self::$_error['cookieError'] ) ) {
				// message on cookie error
				$content .= '    <p class="alert">' . "\n";
				$content .= '      ' . self::$_error['cookieError'] . "\n";
				$content .= '    </p>' . "\n";
				$content .= '<br />' . "\n";
				$content .= 'This website uses cookies to verify the captcha in the contact form. <a href="?page=imprint#dataprotection">More About</a>.<br /><br />' . "\n";
				$content .= '<div class="cookieConsentContact"><a class="cookieConsentOK" onClick="window.location.reload();">I have understood</a></div>' . "\n";
				$content .= '<br /><br /><br />' . "\n";
			} else if( isset( self::$_error['browserError'] ) ) {
				$content .= self::$_error['browserError'];
				$content .= '  </div>' . "\n"; // class container
				$content .= '</div>' . "\n"; // id contact
				print_r( $content );
				return false;
			} else if( isset( self::$_error['langError'] ) ) {
				$content .= self::$_error['langError'];
				$content .= '  </div>' . "\n"; // class container
				$content .= '</div>' . "\n"; // id contact
				print_r( $content );
				return false;
			}
			//return false; // dont render Form on 
		}
		
		// if we dont accept the Cookie, dont render the form Fields 
		if( ! $commitCookie ) {
			$content .= '  </div>' . "\n"; // class container
			$content .= '</div>' . "\n"; // id contact
			print_r( $content );
			return false;
		}
		
		// if mail is send
		if( isset( self::$_emailSent ) && self::$_emailSent == true ) { 
			if( self::$_wartezeit != 0 ) {
				$content .= '<meta http-equiv="refresh" content="' . self::$_wartezeit . '; url=' . self::$_weiterleitung . '" />' . "\n";
			}
			$content .= '    <p class="info">Your message was sent successfully.</p>' . "\n";
			$content .= '  </div>' . "\n"; // class container
			$content .= '</div>' . "\n"; // id contact
			print_r( $content );
			return true;
		}

		$content .= '    <div id="contact-form">' . "\n";
		$content .= '      <form id="contact-us" action="' . $_SERVER['SCRIPT_NAME'] . "?" . $_SERVER['QUERY_STRING'] . '" method="post">' . "\n";

		// contact name
		$content .= '        <div class="formblock">' . "\n";
		$content .= '          <input type="text" name="contactName" id="contactName" value="' . ( isset( $_POST ) && isset( $_POST['contactName'] ) ? $_POST['contactName'] : '' ) . '" class="txt requiredField ' . ( isset( self::$_error['nameError'] ) && self::$_error['nameError'] != '' ?  'inputError' : '' ) . '" placeholder="Name" />' . "\n";
		if( isset( self::$_error['nameError'] ) && self::$_error['nameError'] != '' ) {
			$content .= '<br /><span class="error">' . self::$_error['nameError'] . '</span>' . "\n";
		}
		$content .= '        </div>' . "\n";

		// email
		$content .= '        <div class="formblock">' . "\n";
		$content .= '          <input type="text" name="email" id="email" value="' . ( isset( $_POST['email'] ) ? $_POST['email'] : '' ) . '" class="txt requiredField email ' . ( isset( self::$_error['emailError'] ) && self::$_error['emailError'] != '' ? 'inputError' : '' )  . '" placeholder="Email" />' . "\n";
		if( isset( self::$_error['emailError'] ) && self::$_error['emailError'] != '' ) {
			$content .= '<br /><span class="error">' . self::$_error['emailError'] . '</span>' . "\n";
		}
		$content .= '        </div>' . "\n";
		
		// subject
		if( MAILSUBJECT ) {
			$content .= '        <div class="formblock">' . "\n";
			$content .= '          <select name="subject" id="subject" class="txt requiredField subject ' . (  isset( self::$_error['subjectError'] ) && self::$_error['subjectError'] != '' ? 'inputError' : '' ) . '">' . "\n";
			$content .= '            <option value="none">--- Please choose ---</option>' . "\n";
			foreach( MAILSUBJECT as $key => $subjectfield ) {
				$content .= '            <option value="' . $key . '" ' . ( isset( $_POST['subject'] ) && $_POST['subject'] == $key ? 'selected' : '' ) . '>' . $subjectfield . '</option>' . "\n";
			}
			$content .= '          </select>';
			if( isset( self::$_error['subjectError'] ) && self::$_error['subjectError'] != '' ) {
				$content .= '<br /><span class="error">' . self::$_error['subjectError'] . '</span>' . "\n";
			}
			$content .= '        </div>' . "\n";
		}

		// comments
		$content .= '        <div class="formblock">' . "\n";
		$content .= '          <textarea name="comments" id="commentsText" class="txtarea requiredField ' . ( isset( self::$_error['commentError'] ) && self::$_error['commentError'] != '' ? 'inputError' : '' ) . '"  placeholder="Message" maxlength="' . self::$_maxchars . '">';
		if( isset( $_POST['comments'] ) ) {
			if( function_exists( 'stripslashes' ) ) {
				$content .= stripslashes( $_POST['comments'] );
			} else {
				$content .= $_POST['comments'];
			}
		}
		$content .= '</textarea>' . "\n";
		if( isset( self::$_error['commentError'] ) && self::$_error['commentError'] != '' ) {
			$content .= '<br /><span class="error">' . self::$_error['commentError'] . '</span>' . "\n";
		}
		$content .= '        </div>' . "\n";

		// captcha
		if( MAILCAPTCHA ) {
			$content .= '        <div class="formblock">' . "\n";
			$content .= '          <img src="inc/form/captcha_form.php' . self::$_sessionstringnew . '" alt="Captcha" id="P91Captcha" />' . "\n";
			$content .= '          <br /><a href="javascript:P91Captcha(\'' . self::$_sessionstringadd . '\')">Neuer Code?</a>' . "\n";
			$content .= '          <br /><br />' . "\n";
			$content .= '          <input type="text" name="code" id="code" class="text requiredField code ' . ( isset( self::$_error['captchaError'] ) && self::$_error['captchaError'] != '' ? 'inputError' : '' ) . '" maxlength="50" placeholder="Captcha-Code" />' . "\n";
			if( isset( self::$_error['captchaError'] ) ) {
				$content .= '          <br /><span class="error">' . self::$_error['captchaError'] . '</span>' . "\n";
			}
			$content .= '        </div>' . "\n";
		}

		// submit button
		$content .= '        <div class="formblock">' . "\n";
		$content .= '          <button name="submit" type="submit" class="subbutton">Submit</button>' . "\n";
		$content .= '          <input type="hidden" name="submitted" id="submitted" value="true" />' . "\n";
		$content .= '        </div>' . "\n";

		$content .= '      </form>' . "\n";
		$content .= '    </div>' . "\n"; // id contact-form"

		$content .= '  </div>' . "\n"; // class container
		$content .= '</div>' . "\n"; // id contact
		
		// render the Form
		print_r( $content );
	}



	/**
	 * check for submited form
	 */
	private static function submitted() {
		if( isset( $_POST['submitted'] ) ) {
			
			// validate captcha
			if( MAILCAPTCHA ) {
				if( empty( $_POST['code'] ) ) {
					self::$_error['captchaError'] = 'You have not entered a captcha.';
				} else if( sha1( trim( strip_tags( strtoupper( $_POST['code'] ) ) ) ) != $_SESSION['P91Captcha_code'] ) {
					self::$_error['captchaError'] = 'The captcha is incorrect, please re-enter!';
				}
			}

			// validate name
			if( trim( $_POST['contactName'] ) === '' ) {
				self::$_error['nameError'] = 'Please enter a name!';
			} else {
	  		$name = trim( $_POST['contactName'] );
	  		$name = str_replace( '[^\w\s[:punct:]]*', ' ', $name );
				self::$_contact['name'] = $name;
			}
			
			// validate email
			if( trim( $_POST['email'] ) === '' )  {
				self::$_error['emailError'] = 'Please enter a valid email address.';
			} else if( ! preg_match( "/^[[:alnum:]][a-z0-9_.-]*@[a-z0-9.-]+\.[a-z]{2,4}$/i", trim( $_POST['email'] ) ) ) {
				self::$_error['emailError'] = 'You have not entered a valid email address.';
			} else {
  			$email = trim( $_POST['email'] );
  		}
			
			// validate subject
			if( MAILSUBJECT ) {
	    	if( trim( $_POST['subject'] ) === '' || trim( $_POST['subject'] ) === "none" ) {
	    		self::$_error['subjectError'] = 'Please enter a subject!'; 
	    	} else {
					if( is_array( MAILSUBJECT ) ) {
						$subject = MAILSUBJECT[ $_POST['subject'] ];
					} else {
						$subject = $_POST['subject'];
					}
				}
			}
			
			// validate comment
	  	if( trim( $_POST['comments'] ) === '' ) {
	  		self::$_error['commentError'] = 'Please enter a message for us!';
	  	} else {
	      $commenttext = trim( $_POST['comments'] );
	      if( strlen( $commenttext ) >= self::$_maxchars ) {
	        // message to long
	    		self::$_error['commentError'] = 'Your message is too long, please shorten the text!';
	      } else {
	        // process comment
	      	if( function_exists( 'stripslashes' ) ) {
	      		$comments = stripslashes( trim( $_POST['comments'] ) );
	          //if( $replacechars ) $comments = str_replace( $r1, $r2, $comments );
	      		if( self::$_mail_format == "html" ) $comments = str_replace( "\n", "<br />", $comments );
	      		$comments = str_replace( '[^\w\s[:punct:]]*', ' ', $comments );
	      	} else {
	      		$comments = trim( $_POST['comments'] );
	      	}
	      }
	  	}
			
			// badword filter
	    if( ! empty( self::$_badwords ) && isset( $comments ) ) {
	      foreach ( self::$_badwords as $key => $word ) {
	        if( strpos( $comments, $word ) !== false ) {
	          $length = strlen( $word );
	          $tmpword = substr( $comments, strpos( $comments, $word ), $length +1 );
	          $tmpchar = substr( $tmpword, -1);
	          if( $tmpchar === " " || $tmpchar === "\n" ) {
	            // found a bad word
	        		self::$_error['commentError'] = 'You used a word that is not allowed: "' . $word . "\"<br />\nPlease remove the word and try again.";
	            break;
	          }
	        }
	      }
	    }
			
			// upon no failure errors let's email now!
			if( count( self::$_error ) <= 0 ) {
	  		$ip = $_SERVER['REMOTE_ADDR'];
	  		$host = gethostbyaddr( $ip );
	  		$timestamp = time();
	  		$datum = date( "d.m.Y", $timestamp );
	  		$uhrzeit = date( "H:i:s", $timestamp );
	  		$emailTo = html_entity_decode( MAILRECIPIENT, ENT_QUOTES, 'UTF-8' );
	      if( MAILSUBJECT == '' ) {
	        $subject = 'Web form message from '.$name;
	      }
				
				$company_name = APPDOMAIN;
				$company_url = URL;
				$img_header = MAILHTMLHEADER;
				$img_intext = MAILHTMLIMAGE;
				
				// templating the Message
				if( self::$_mail_format == "html" ) {
	        // html mail
	        $twitter_url = empty( $twitter_url ) || $twitter_url == "https://twitter.com/" ? '' : '<a target="_blank" href="'. $twitter_url .'">follow on Twitter</a> | ';
	        $facebook_url = empty( $facebook_url ) || $facebook_url == "https://facebook.com/" ? '' : '<a target="_blank" href="'. $facebook_url .'">Facebook</a> | ';
					$instagram_url = empty( $instagram_url ) || $instagram_url == "https://www.instagram.com/" ? '' : '<a target="_blank" href="'. $instagram_url .'">Instagram</a> | ';
					$youtube_url  = empty( $youtube_url ) || $youtube_url == "https://www.youtube.com/" ? '' : '<a target="_blank" href="'. $youtube_url .'">YouTube</a> | ';

	        $company_string = empty( $company_name ) ? '' : '<em>Copyright &copy; '.date("Y", $timestamp).' '. $company_name .'<br />All rights reserved.</em><br><br>';
	        $slogan = empty( $slogan ) ? '' : '<em>' . $slogan . '</em>';
	        $company_phone = empty( $company_phone ) ? '' : '<strong>Telephon</strong>: &nbsp;'. $company_phone .'<br>';
	        $company_fax = empty( $company_fax ) ? '' : '<strong>Fax: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong>'. $company_fax .'<br>';
	        $company_mail = empty( $company_mail ) ? '' : '<strong>Mail: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong><a class="moz-txt-link-abbreviated" href="mailto:'.$company_mail.'">'.$company_mail.'</a><br>';
					
					// $comments will be used in the template files, so $body is the new Message after include them 
	        if( self::$_mailtemplatehtml != "" && is_file( __DIR__ . "/" . self::$_mailtemplatehtml ) ) {
	          include( 'inc/form/' . self::$_mailtemplatehtml );
	        } else {
	          // use default
	          include( 'inc/form/mail_template_html.php' );
	        }
					
				} else { // mail format is plain
					
	        // plain mail
	        if( self::$_mailtemplateplain != "" && is_file( __DIR__ . "/" . self::$_mailtemplateplain ) ) { 
	          include( 'inc/form/' . self::$_mailtemplateplain );
	        } else {
	  	      include( 'inc/form/mail_template_plain.php' );
	        }
					
				}
				
	      //
	      // sending the E-Mail
	      //
	      if( self::$_mail_sender == "smtp" ) {
	        // use PHP pear SMPT
	        $local_recipients = explode( ', ', MAILRECIPIENT );
	        try {
	          //include __DIR__ . '/Mail.php';
	          //self::$_emailSent = smtpSendMail::smtpMail( $local_recipients, $body, $subject, $email, $mail_format );
						self::$_emailSent = self::smtpMail( $local_recipients, $body, $subject, $email, self::$_mail_format );;
	        } catch( Exception $e) {
	          echo "<pre>Exception: \n", $e->getMessage(), "</pre>\n";
	        }
	      } else {
	        // use sendmail
	      	$headers = "From: $name <$email>\n";
	      	if( self::$_mail_format == "html" ) {
	      	  $headers .= "Content-Type: text/html\n";
	      	} else {
	      	  $headers .= "Content-Type: text/plain\n";
	      	}
	        $headers .= "Content-Transfer-Encoding: 8bit\n";
	      	self::$_emailSent = mail( $emailTo, $subject, $body, $headers );
	      }
				
			} // end has errors
			
			
			return false;
		}
		return false;
	}

	/**
	 * check if commit Cookie is set
	 */
	private static function commitCookie() {
		if( isset( $_COOKIE["commitcookie"] ) && $_COOKIE["commitcookie"] != NULL ) {
			if( MAILCAPTCHA ) {
				// set captcha values
				session_start();
				if( ! isset( $_COOKIE[ session_name() ] ) ) {
		      self::$_sessionstringnew = '?' . session_name() . "=" . session_id();
		      self::$_sessionstringadd = '&' . session_name() . "=" . session_id();
				}
			}
			return true;
		} else {
			self::$_error['cookieError'] = 'Your browser must allow cookies from this site in order to send us a message.';
			return false;
		}
	}

	/**
	 * captcha javascript
	 */
	private static function captchaJs() {
		if( MAILCAPTCHA ) {
			$content = '<script type="text/javascript">' . "\n";
			$content .= '  function P91Captcha( sid ) {' . "\n";
			$content .= '  let pas = new Image();' . "\n";
			$content .= '  let heuri = new Date();' . "\n";
			$content .= '  pas.src = "inc/form/captcha_form.php?x=" + heuri.getTime() + sid;' . "\n";
			$content .= '  document.getElementById( "P91Captcha" ).src = pas.src;' . "\n";
			$content .= '}' . "\n";
			$content .= '</script>' . "\n";
			print_r( $content );
		}
	}

	/**
	 * the Formular CSS
	 */
	private static function formCss() {
		$content = '<style type="text/css">' . "\n";
		$content .= '#contact { max-width:300px;margin:0 auto; }' . "\n";
		$content .= '#contact .desc h2 { font-weight: normal; color: #444; margin-bottom: 8px; }' . "\n";
		$content .= '#contact .desc p { color: #333; line-height: 1.3em; margin-bottom: 15px; }' . "\n";
		$content .= '#contact .formblock { display: block; margin-bottom: 11px; }' . "\n";
		$content .= '#contact .formblock .txt { padding: 4px 6px; color: #666; width: 264px; }' . "\n";
		$content .= '#contact .formblock .txtarea { font-size: 10pt; padding: 4px 6px; color: #666; width: 264px; height: 140px; max-height:300px; max-width:590px; min-width: 264px; min-height: 140px; }' . "\n";
		$content .= '#contact .formblock .error { font-style: normal; color: #9d3131; }' . "\n";
		$content .= '#contact .info { font-weight: bold; color: #59913d; margin-bottom: 10px; }' . "\n";
		$content .= '#contact .formblock .code { padding: 4px 6px; color: #666; background: #fff; border: 1px solid #666; }' . "\n";
		$content .= '#contact .subbutton { padding: 3px 7px; font-weight: bold; color: #565656; }' . "\n";
		$content .= '#subject { color: #666; border: 1px solid #666; background: #F9F9F9; width: 278px !important; border-radius: 6px; padding: 4px 6px;}' . "\n";
		$content .= '*.inputError { border: 1px solid #f00 !important; }' . "\n";
		$content .= '.alert { text-align: justify; background: #ffd1d1; border: 1px solid #f00; border-radius: 5px; padding: 7px 14px; margin: 0 20px 0 0; }' . "\n";
		$content .= '</style>' . "\n";
		print_r( $content );
	}


	/**
	 * send an smtp Mail
	 */
	private static function smtpMail( $recipients = null, $msg = null, $subject = null, $sender = null, $format = "plain"  ) {
		if( $recipients === null || $msg === null ) return false;
		if( $subject === null || $subject == "" ) $subject = "Default Subject";
			
		$result = false;
		try {

			// validate php pear Mail.php is installed
			if( ! self::checkPearMail() ) {
				throw new Exception( 'You musst install PHP Pear Mail and Net_SMTP by Hand: <br />sudo /usr/bin/pear install Mail Net_SMTP' );
				return false;
			}
			
			// re-arrange recipient(s)
			if( is_array( $recipients ) ) {
				$to = '';
				foreach ( $recipients as $key => $recipient ) {
					$name = explode( '@', $recipient );
					$name = isset( $name[0] ) ? $name[0] : $recipient;
					$name = str_replace( '.',' ', $name );
					//$to .= "$name <" . $recipient . ">;";
          $to .= $recipient . ", ";
				}
				//error_log( 'array: ' . $to ,0 );
			} else {
				$name = explode( '@', $recipients );
				$name = isset( $name[0] ) ? $name[0] : $recipients;
				$name = str_replace( '.',' ', $name );
				$to = "$name <" . $recipients . ">";
			}

			// should not occure, we escape if still
			if( $to === null || $to === "" ) { throw new Exception( 'Failed to use recipient: ' . $to . ', aborted.' ); return; }
		
			// Our maildata from config
			$host 		= MAILHOST;
			$username = MAILUSER;
			$password = MAILPASS;
			$protocol = MAILPROTO; // "ssl"
			$port 		= ( MAILPORT != null && is_numeric( MAILPORT ) && MAILPORT != 0 ) ? MAILPORT : 465;
			$smtp 		= $protocol . "://" . $host;

			// Set Sender
      if( $sender == null ) $sender = MAILUSER;
      $sendername = explode( '@', $sender );
			$sendername = isset( $sendername[0] ) ? $sendername[0] : $sender;
			$sendername = str_replace( '.',' ', $sendername );
		  $from = "$sendername <" . $sender . ">";
      
			// Create mail header
			$headers = array(
        'From' => $from,
			  'To' => $to,
			  'Subject' => $subject,
        'MIME-Version' => 1
      );
      if( $format == "html" ) {
        $headers['Content-type'] = 'text/html;charset=utf-8';
        $headers['Content-Transfer-Encoding'] = '8bit';
      } else {
        $headers['Content-type'] = 'text/plain;charset=utf-8';
      }

			// Create PHP Pear Mail Object
			$smtp = Mail::factory('smtp',
			  array (	'host' => $smtp,
				      	'auth' => true,
				      	'port' => $port,
				      	'timeout' => 10,
				      	'username' => $username,
				      	'password' => $password
        )
      );

			// And send the mail out 
			$result = $smtp->send($to, $headers, $msg);
		
			if( PEAR::isError( $result ) ) {
//			if( (new PEAR)->isError( $result ) ) {
				//throw new Exception( 'Unable to send mail to ' . $to . ', aborted.' ); // more secure in prduction
        throw new Exception( ''.$result->getMessage().'' ); // less secure, use only for debugging
			} else {
				$result = true;
			}
		
		} catch( Exception $e ) {
			throw new Exception( '' . $e->getMessage() );
		}
	
		return $result;	
			
	}

	/**
	 * check if php pear Mail.php is installed
	 */
	public static function checkPearMail() {
		// load PHP Pear Mail
		if( PHP_OS == 'Linux' ) {
			$file = "/usr/share/php/Mail.php"; // ubuntu & debian
		} else if( PHP_OS == 'Darwin' ) {
			$file = "/opt/homebrew/share/pear/Mail.php"; // OSX
		}
		// && is_file( '/usr/share/php/test/Net_SMTP/tests/config.php.dist' )
		if( is_file( $file ) ) {
			require_once $file;
			return true; // no error
		} else {
			// escape if php pear mail is not installed
			//throw new Exception( 'You musst install PHP Pear Mail and Net_SMTP by Hand: sudo /usr/bin/pear install Mail Net_SMTP' );
			//return 'You musst install PHP Pear Mail and Net_SMTP by Hand: sudo /usr/bin/pear install Mail Net_SMTP'; 
			return false;// we have an error
		}
	}

}

?>

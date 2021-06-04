<?php

/**
 * A Helper class which contains static functions and is GLOBALY avalable!
 */
class smtpSendMail {
	
	/**
	 * Helper to send SMTP E-Mails via PHP Pear Modul
	 * 
	 * sudo pear install Mail
	 * sudo pear install Net_SMTP
	 */
	public static function smtpMail( $recipients = null, $msg = null, $subject = null, $sender = null, $format = "plain" ) {
		if( $recipients === null || $msg === null ) return false;
		if( $subject === null || $subject == "" ) $subject = "Default Subject";
			
		$result = false;
		try {

			// http://email.about.com/od/emailprogrammingtips/qt/PHP_Email_SMTP_Authentication.htm
			// http://stackoverflow.com/a/33506709
		
			// load PHP Pear Mail
			//$file = "/usr/share/php/Mail.php"; // ubuntu & debian
      $file = "/Users/Shared/Relocated Items/Security/share/pear/Mail.php"; // OSX
			// && is_file( '/usr/share/php/test/Net_SMTP/tests/config.php.dist' )
			if( is_file( $file ) ) {
				require_once $file;
			} else {
				// escape if php pear mail is not installed
				throw new Exception( 'You musst install PHP Pear Mail and Net_SMTP by Hand: sudo /usr/bin/pear install Mail Net_SMTP' );
				return;
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
	} // end smtpMail
	
	
}

?>

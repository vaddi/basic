<?php

// Class <strong>Crypto</strong> Contains the crypthographic functions

class Crypto {

	/**
	 * Generate a Token (URL save characters)
	 * form 0 = only Numbers
	 * form 1 = only Letters
	 * form 2 = Letters and Numbers
	 * form 3 = Letters, Numbers and special Characters
	 * @param $length Integer 
	 * @param $form Integer
	 * @return String
	 */
	public static function genToken( $length = null, $form = null ) {
		if( $length === null ) 	$length = 32;	// default length
		if( $form === null ) 		$form = 2;		// default form
		$num 			= array( '0','1','2','3','4','5','6','7','8','9' );
		$letters 	= array( 'a','b','c','d','e','f','g','h','i','j','k','l','m',
											 'n','o','p','q','r','s','t','u','v','w','x','y','z',
											 'A','B','C','D','E','F','G','H','I','J','K','L','M',
											 'N','O','P','Q','R','S','T','U','V','W','X','Y','Z' );
		$numchars = array( '0','1','2','3','4','5','6','7','8','9','!','(',')','-','_' );
		if( $form == 0 ) {
			$tmpArr = $num;
		} elseif( $form == 1 ) {
			$tmpArr = $letters;
		} elseif( $form == 2 ) {
			$tmpArr = $num + $letters;
		} elseif( $form == 3 ) {	
			$tmpArr = $numchars + $letters;
		} 
		$passArr = $tmpArr;
		$passwd = "";
		$last = '';
		for( $i = 0; $i < $length; $i++ ) {
			shuffle( $passArr );
			if( $last != $passArr[$i] ) { 
				$passwd .= $passArr[$i]; 
			} else { 
				$i -1;
			}
			$last = $passArr[$i];
		}
		return $passwd;
	}

	/**
	 * get a token ( from string or if no string is given ceate a rnd token)
	 */
	public static function token( $string = null ) {
		$secret = SECRET;
		if( $secret === null || $secret === "" ) throw new Exception( 'No Secret found in DB!' ); 
		if( $string === null ) $string = self::genToken( 32 );
		$token = base64_encode( sha1( $string . $secret, true ) . $secret );
		return $token;
	}

  /**
   * Generate SRI Hash (for javascript and css attribute "integrity")
   * See also https://www.w3.org/TR/SRI/
   */
  public static function genSRI( $file ) {
    $result = false;
		$openssl = '/usr/bin/openssl';
    if( is_file( $file ) ) {
      $result = ( exec( $openssl . " dgst -sha384 -binary " . $file . " | " . $openssl . " base64 -A" ) );
    }
    return $result;
  }

}

?>

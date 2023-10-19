<?php

// Class <strong>Base</strong> will be used as the Mainclass. It instanciates all other Classes by the PHP  <strong>spl_autoload_register</strong> function

// Autoload all other Classes  in the current Folder
spl_autoload_register( function( $class_name ) {
  require_once( __DIR__ . '/' . $class_name . '.php');
});

class Base {

	/**
	 * Get total application size
	 * @return	Appsize (/ whithout git folder)
	 */
	public static function appSize() {
    $result = null;
		$path = exec( 'pwd' );
		$size = explode( "\t", exec( '/usr/bin/du -s ' . $path ) );
		$result[] = isset( $size[0] ) ? $size[0] : null;
		if( Git::gitPHP() ) {
			$size = explode( "\t", exec( '/usr/bin/du -s ' . $path . '/.git' ) );
			$git = isset( $size[0] ) ? $size[0] : null;
      //$result = 'total ' . $real . 'MB, only .git ' . ( (float) $real - (float) $git ) . 'MB';
      $result[] = $git;
      $result[] = $result[0] - $git;
		}
		return $result;
	}

	/**
	 * Get total application files in upload
	 */
	public static function totalFiles() {
//		$path = realpath( './' ) . '/' . PAGES;
    $path = PAGES;
		$result = ( exec( "find $path -not -type d | wc -l |tr -d ' '" ) );
    return $result -1;
	}

	/**
	 * Helper function to get the used enviroment
	 */
	public static function getEnv() {
		return ENV;
	}

	/**
	 * Helper function to get newest file and its date of modification
	 */
	public static function lastUpdated() {
    $path = __DIR__;
    $string = exec( "find $path -type f -exec stat -lt \"%Y-%m-%dT%H:%M:%S%z\" {} \+ | cut -d' ' -f6- | sort -n | tail -1 | tr -d \"\n\"" );
    $tmp = explode( ' ', $string );
    $result['date'] = isset( $tmp[0] ) && $tmp[0] != null ? $tmp[0] : null;
    $result['file'] = isset( $tmp[1] ) && $tmp[1] != null ? $tmp[1] : null;
		return $result;
	}

	/**
	 * Generate a Token (URL save characters)
	 * Form 0 = only Numbers
	 * Form 1 = only Letters
	 * Form 2 = Letters and Numbers
	 * Form 3 = Letters, Numbers and special Characters
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
		// safe token in db
		return $token;
	}

  /**
   * Generate SRI Hash (for javascript and css attribute "integrity")
   * See also https://www.w3.org/TR/SRI/
   */
  public static function genSRI( $file ) {
    $result = false;
    if( is_file( '/usr/bin/openssl' ) ) {
      $result = ( exec( "/usr/bin/openssl dgst -sha384 -binary " . $file . " | openssl base64 -A" ) );
    }
    return $result;
  }

  /**
   * List all mentions of opendoings
   */
  public static function getOpenDoings() {
    $result = exec( 'grep -r "todo" ' . realpath( './' ) . '/' . '* | wc -l | tr -d " "' ) -7;
    return $result;
  }
  
	/**
	 * Returns a google maps url from a assoc Address array
	 */
	public static function gmaps( $address = null ) { 
		if( $address === null ) return $address;
		$street				=	isset( $address['street'] ) ? $address['street'] : '';
		$housenumber	= isset( $address['housenumber'] ) ? ' ' . $address['housenumber'] : '';
		$city					= isset( $address['city'] ) ? ' ' . $address['city'] : '';
		$plz					=	isset( $address['plz'] ) ? ' ' . $address['plz'] : '';
		// and build our gmaps url
    $zoom = "15"; // zoom level
    $url  = 'https://maps.google.com/maps?f=q&source=s_q&hl=' . CLIENTLANG . '&geocode=&q=';
    $url .= self::mapify( $street . ' ' . $housenumber . ', ' . $plz . ' ' . $city );
    $url .= '&z=' . $zoom . '&output=embed';
		return '<iframe id="gmaps" src="' . $url . '" width="100%" height="300" frameborder="0" style="border:0" allowfullscreen></iframe>';
	}
	
	
	/**
	 * Helper to build gmaps url (replace spaces)
	 */
	public static function mapify( $string = null, $delimiter = null ) { 
		if( $string === null ) return;
		if( $delimiter === null ) $delimiter = '+';
		$string = str_replace( ' ', $delimiter, $string );
    return str_replace( ',', '', $string );
	}
  
	/**
	 * Helper for static login
	 */
	public static function login( $user = null, $passwd = null ) { 
    if( $user === null || $passwd === null ) return false;
		$result = false;
    if( $user === USER ) {
      if( $passwd === USERPASS ) {
        $result = true;
      }
    }
    return $result;
	}

}

?>

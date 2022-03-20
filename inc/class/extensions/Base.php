<?php

class Base {

	//
	// Git stuff
	//
	
	/**
	 * Compare the commit Hashes from the current commit and the last from git logs
	 */
	protected static function gitLast() {
		if( self::git() ) {
			if( is_file( '/usr/bin/git' ) ) {
				$fromlog = exec( 'git log -1 | grep commit | tail -c 41' );
				$current = exec( 'git rev-parse HEAD' );
				$result = '<span style="color:';
				if( $fromlog == $current ) $result .= 'inherit';
					else $result .= 'red';
				$result .= '"';
				$result .= '>' . $fromlog . '</span>';
				return $result;
			}
		}
		return false;
	}
	
	
	/**
	 * Get the current remote url 
	 */
	protected static function gitRemote() {
		if( self::git() ) {
			if( is_file( '/usr/bin/git' ) ) {
				$remotes = exec( '/usr/bin/git remote -v' );
				$line = explode( "\t", $remotes );
				$result = isset( $line[1] ) ? $line[1] : null;
				$result = preg_replace('/\(.*?\)|\s*/', '', $result);
				return $result;
			}
		}
		return false;
	}
	
	
	/**
	 * Get total application size
	 * @return	Appsize (/ whithout git folder)
	 */
	protected static function appSize() {
		$path = exec( 'pwd' );
		$size = explode( "\t", exec( '/usr/bin/du -s ' . $path ) );
		$real = isset( $size[0] ) ? number_format( $size[0] / 1024, 2 ) : null;
		if( self::git() ) {
			$size = explode( "\t", exec( '/usr/bin/du -s ' . $path . '/.git' ) );
			$git = isset( $size[0] ) ? number_format( $size[0] / 1024, 2 ) . ' MB' : null;
      $result = 'total ' . $real . 'MB, only .git ' . ( (float) $real - (float) $git ) . 'MB';
			return $result;
		}
		return $real;
	}
	
	
	/**
	 * Get total application files in upload
	 */
	protected static function totalFiles() {
//		$path = realpath( './' ) . '/' . PAGES;
    $path = PAGES;
		$result = ( exec( "find $path -not -type d | wc -l |tr -d ' '" ) );
    return $result -1;
	}
	
	
	/**
	 * Get the total amount of pushed commits
	 */
	protected static function gitCommits() {
		if( self::git() ) {
			if( is_file( '/usr/bin/git' ) ) 
				return exec( '/usr/bin/git rev-list --reverse HEAD | awk "{ print NR }" | tail -n 1' );
		}
		return false;
	}

	protected static function checkForUpdate() {
		// FIX me
//		if( is_file( '/usr/bin/git' ) ) {
//			$folder = str_replace( '/admin', '', realpath( './' ) ); 
//			return (int) shell_exec( "[ $(/usr/bin/git -C $folder rev-parse HEAD) = $(/usr/bin/git -C $folder ls-remote $(/usr/bin/git -C $folder rev-parse --abbrev-ref @{u} | \sed 's/\// /g') | cut -f1) ] && echo -n 0 || echo -n 1" );
//		}
		return false;
	} 

	/** 
	 * Helper function to get version number from "git tag" (dont forget to commit them!)
	 */
	protected static function gitTag() {
		if( self::git() ) {
			if( is_file( '/usr/bin/git' ) ) 
				return exec( '/usr/bin/git describe --abbrev=0 --tags' );
		}
		return false;
	}
	
	private static function git() {
		if( is_dir( realpath( './' ) . '/.git' ) ) return true;
		return false;
	}
	
	/**
	 * Helper function to get the used enviroment
	 */
	protected static function getEnv() {
		return ENV;
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
		for ($i = 0; $i < $length; $i++) {
			shuffle($passArr);
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
		if( $string === null ) $string = self::genToken( 16 );
		$token = base64_encode( sha1( $string . $secret, true ) . $secret );
		// safe token in db
		return $token;
	}

  public static function getOpenDoings() {
    $result = exec( 'grep -ri "todo" ' . realpath( './' ) . '/' . '* | wc -l | tr -d " "' ) -2;
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
	 * Helper for login
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

	/**
	 * Helper to build prometheus scrape endpoint for Application
	 */
	public static function appExporter() { 
		$result = null;
    
    $result .= "# HELP " . SHORTNAME . "_info " . APPNAME . " Info Metric with constant value 1\n";
    $result .= "# TYPE " . SHORTNAME . "_info gauge\n";
    $result .= SHORTNAME . "_info{version=\"" . VERSION . "\",nodename=\"" . HOST . "\"} 1\n";
    
    $result .= "# HELP " . SHORTNAME . "_pages Amount of pages (and pages location)\n";
    $result .= "# TYPE " . SHORTNAME . "_pages gauge\n";
    $result .= SHORTNAME . "_pages{pagefolder=\"" . PAGES . "\"} " . self::totalFiles() . "\n";
    
    $result .= "# HELP " . SHORTNAME . "_commits Amount of git commits\n";
    $result .= "# TYPE " . SHORTNAME . "_commits gauge\n";
    $result .= SHORTNAME . "_commits " . self::gitCommits() . "\n";
    
    // usefull metrics?
    // current loged in users
    
    header("Content-type: text/plain; charset=utf-8");
    http_response_code( 200 );
    return $result;
	}

}

?>

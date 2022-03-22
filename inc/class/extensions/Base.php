<?php

// Autoload other extension Classes
spl_autoload_register( function( $class_name ) {
  require_once( __DIR__ . '/' . $class_name . '.php');
});

class Base {

	/**
	 * Get total application size
	 * @return	Appsize (/ whithout git folder)
	 */
	protected static function appSize() {
    $result = null;
		$path = exec( 'pwd' );
		$size = explode( "\t", exec( '/usr/bin/du -s ' . $path ) );
		$result[] = isset( $size[0] ) ? $size[0] : null;
		if( GitPHP::git() ) {
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
	protected static function totalFiles() {
//		$path = realpath( './' ) . '/' . PAGES;
    $path = PAGES;
		$result = ( exec( "find $path -not -type d | wc -l |tr -d ' '" ) );
    return $result -1;
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
    $result = exec( 'grep -r "ToDo:" ' . realpath( './' ) . '/' . '* | wc -l | tr -d " "' ) -2;
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
	 * Helper for request counter
	 */
	public static function requestCounter() {
    // create PDO database object
    if( SQLITE_USE ) {
      // save each user request in sqlite db
      $ip = isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : null;
      $timestamp = date( 'Y-m-d H:i:s', time());
      $useragent = isset( $_SERVER['HTTP_USER_AGENT'] ) ? $_SERVER['HTTP_USER_AGENT'] : null;
      $platform = isset( $_SERVER['HTTP_SEC_CH_UA_PLATFORM'] ) ? $_SERVER['HTTP_SEC_CH_UA_PLATFORM'] : null;
      $url = URL;
      $referer = isset( $_SERVER['HTTP_REFERER'] ) ? $_SERVER['HTTP_REFERER'] : null;
      $hits = 1;
      $db = new DB( SQLITE_TYPE, SQLITE_FILE );
      if( $db ) {
        // do we have an entry for the visitor?
        $db->query( "SELECT * FROM visitors WHERE ip = :ip OR useragent = :useragent" );
        $db->bind( ':ip', $ip );
        $db->bind( ':useragent', $useragent );
        $db->execute();
        $result = $db->resultset();
        if( count( $result ) == 0 ) {
          // insert a new entry for this visitor
          $db->query( "INSERT INTO visitors ( ip, url, timestamp, useragent, platform, referer, hits ) VALUES ( :ip, :url, :timestamp, :useragent, :platform, :referer, :hits )" );
          $db->bind( ':ip', $ip );
          $db->bind( ':url', $url );
          $db->bind( ':timestamp', $timestamp );
          $db->bind( ':useragent', $useragent );
          $db->bind( ':platform', $platform );
          $db->bind( ':referer', $referer );
          $db->bind( ':hits', $hits );
          $db->execute();
        } else if( isset( $result[0]['id'] ) ) {
          // update the existing user entry id
          $db->query( "UPDATE visitors SET ip = :ip, url = :url, timestamp = :timestamp, useragent = :useragent, platform = :platform, referer = :referer, hits = :hits WHERE id = :id;" );
          $db->bind( ':ip', $ip );
          $db->bind( ':url', $url );
          $db->bind( ':timestamp', $timestamp );
          $db->bind( ':useragent', $useragent );
          $db->bind( ':platform', $platform );
          $db->bind( ':referer', $referer );
          $db->bind( ':hits', ( $result[0]['hits'] +1 ) );
          $db->bind( ':id', $result[0]['id'] );
          $db->execute();
        }
      }
    }
    return false;
	}

	/**
	 * Helper to build prometheus scrape endpoint for Application
	 */
	public static function appExporter() { 
		$result = null;

    $result .= "# HELP " . SHORTNAME . "_info " . APPNAME . " Info Metric with constant value 1\n";
    $result .= "# TYPE " . SHORTNAME . "_info gauge\n";
    $result .= SHORTNAME . "_info{version=\"" . VERSION . "\",nodename=\"" . APPDOMAIN . "\",enviroment=\"" . ENV . "\"} 1\n";

    $result .= "# HELP " . SHORTNAME . "_pages Amount of pages (and pages location)\n";
    $result .= "# TYPE " . SHORTNAME . "_pages gauge\n";
    $result .= SHORTNAME . "_pages{pagefolder=\"" . PAGES . "\"} " . self::totalFiles() . "\n";

    $result .= "# HELP " . SHORTNAME . "_commits Amount of git commits\n";
    $result .= "# TYPE " . SHORTNAME . "_commits gauge\n";
    $result .= SHORTNAME . "_commits " . GitPHP::gitCommits() . "\n";

    $result .= "# HELP " . SHORTNAME . "_appsize Total Size of Application in KiB\n";
    $result .= "# TYPE " . SHORTNAME . "_appsize gauge\n";
    $result .= SHORTNAME . "_appsize{type=\"total\"} " . self::appSize()[0] . "\n";
    $result .= SHORTNAME . "_appsize{type=\"git\"} " . self::appSize()[1] . "\n";
    $result .= SHORTNAME . "_appsize{type=\"plain\"} " . self::appSize()[2] . "\n";

    $result .= "# HELP " . SHORTNAME . "_todos Current open ToDo's (simple count from Application files)\n";
    $result .= "# TYPE " . SHORTNAME . "_todos gauge\n";
    $result .= SHORTNAME . "_todos " . ( self::getOpenDoings() +1 ) . "\n";

    $result .= "# HELP " . SHORTNAME . "_updates Newer Version in Repository available (1 = yes)\n";
    $result .= "# TYPE " . SHORTNAME . "_updates gauge\n";
    $result .= SHORTNAME . "_updates " . GitPHP::checkForUpdate() . "\n";

    $result .= "# HELP " . SHORTNAME . "_visitors Basic Visitor Metrics\n";
    $result .= "# TYPE " . SHORTNAME . "_visitors gauge\n";
    
    if( SQLITE_USE ) {
      // create PDO database object
      $db = new DB( SQLITE_TYPE, SQLITE_FILE );
      if( $db ) {
        $db->query( "SELECT COUNT(hits) as hits FROM visitors WHERE timestamp < date('now')" );
        $db->execute();
        $hits_daily = $db->resultset()[0]['hits'];
        $db->query( "SELECT COUNT(hits) as hits FROM visitors WHERE timestamp < date('now','-1 hour')" );
        $db->execute();
        $hits_hourly = $db->resultset()[0]['hits'];
        $db->query( "SELECT COUNT(hits) as hits FROM visitors" );
        $db->execute();
        $hits_total = $db->resultset()[0]['hits'];
        $result .= SHORTNAME . "_visitors{type=\"visitors\",field=\"daily\"} " . $hits_daily . "\n";
        $result .= SHORTNAME . "_visitors{type=\"visitors\",field=\"hourly\"} " . $hits_hourly . "\n";
        $result .= SHORTNAME . "_visitors{type=\"visitors\",field=\"total\"} " . $hits_total . "\n";
    
        $db->query( "SELECT SUM(hits) as hits FROM visitors WHERE timestamp < date('now')" );
        $db->execute();
        $hits_daily = $db->resultset()[0]['hits'];
        $db->query( "SELECT SUM(hits) as hits FROM visitors WHERE timestamp < date('now','-1 hour')" );
        $db->execute();
        $hits_hourly = $db->resultset()[0]['hits'];
        $db->query( "SELECT SUM(hits) as hits FROM visitors" );
        $db->execute();
        $hits_total = $db->resultset()[0]['hits'];
        $db->query( "SELECT AVG(hits) as hits FROM visitors" );
        $db->execute();
        $hits_avg = $db->resultset()[0]['hits'];
        $result .= SHORTNAME . "_visitors{type=\"hits\",field=\"daily\"} " . $hits_daily . "\n";
        $result .= SHORTNAME . "_visitors{type=\"hits\",field=\"hourly\"} " . $hits_hourly . "\n";
        $result .= SHORTNAME . "_visitors{type=\"hits\",field=\"total\"} " . $hits_total . "\n";
        $result .= SHORTNAME . "_visitors{type=\"hits\",field=\"avg\"} " . $hits_avg . "\n";
      }
    }

    // $result .= '';
    // $result .= '';
    // $result .= '';

    // ToDo: find some usefull metrics.
    // current loged in users (user sessions)
    // current process runtime (ps -p $(pidof "nginx: worker process") -o etimes= | tr -d " ")
    // current errors (from log?)

    $result .= "# HELP " . SHORTNAME . "_mtime Total time for this response, Metric Time in Seconds\n";
    $result .= "# TYPE " . SHORTNAME . "_mtime gauge\n";
    $result .= SHORTNAME . "_mtime " . ( microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"] ) . "\n";

    header("Content-type: text/plain; charset=utf-8");
    http_response_code( 200 );
    return $result;
	}

}

?>

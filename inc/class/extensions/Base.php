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
   * List all mentions of opendoings
   */
  public static function getOpenDoings() {
    $result = exec( 'grep -r "todo" ' . realpath( './' ) . '/' . '* | wc -l | tr -d " "' ) -5; // we have 5 times todo in our code, so we need to recalc them (grep -inr "todo")!
    return $result;
  }
	
  /**
   * Debug the Coockie
   */
  public static function getCookieString() {
    $result = '';
		if( count( $_COOKIE ) > 0 ) {
			foreach( $_COOKIE as $key => $value ) {
				$result .= '[' . $key . '] = ' . $value . " (" . ucfirst( gettype( $value ) ) . ")<br />\n"; 
			}
		} else {
			$result = 'No Cookie set';
		}
    return $result;
  }

	/**
	 * Helper for login
	 */
	public static function login( $user = null, $passwd = null ) {
    if( $user === null || $passwd === null ) return false;
		$result = false;
		// todo: Add users to a own class and add the login also here
    if( $user === USER ) {
      if( $passwd === USERPASS ) {
        $result = true;
      }
    }
    return $result;
	}

}

?>

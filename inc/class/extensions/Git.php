<?php

// Class <strong>Git</strong> will be used to get some Infromations about the git Project Data

class Git {

	//
	// Git stuff
	//

	/**
	 * Compare the commit Hashes from the current commit and the last from git logs
	 */
	public static function gitLast() {
		if( self::gitPHP() ) {
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
	 * Current commit and the last from git logs
	 */
	public static function gitCurrent() {
		if( self::gitPHP() ) {
			if( is_file( '/usr/bin/git' ) ) {
				return exec( 'git log -1 | grep commit | tail -c 41' );
			}
		}
		return false;
	}

	/**
	 * Get the current remote url 
	 */
	public static function gitRemote() {
		if( self::gitPHP() ) {
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
	 * Get the total amount of pushed commits
	 */
	public static function gitCommits() {
		if( self::gitPHP() ) {
			if( is_file( '/usr/bin/git' ) ) 
				return exec( '/usr/bin/git rev-list --reverse HEAD | awk "{ print NR }" | tail -n 1' );
		}
		return false;
	}

	public static function checkForUpdate() {
		// Find Updates
		if( is_file( '/usr/bin/git' ) ) {
      $folder = realpath( './' );
			return (int) shell_exec( "[ $(/usr/bin/git -C $folder rev-parse HEAD) = $(/usr/bin/git -C $folder ls-remote $(/usr/bin/git -C $folder rev-parse --abbrev-ref @{u} | \sed 's/\// /g') | cut -f1) ] && echo -n 0 || echo -n 1" );
		}
		return 0;
	} 

	/** 
	 * Helper function to get version number from "git tag" (dont forget to commit them!)
	 */
	public static function gitTag() {
		if( self::gitPHP() ) {
			if( is_file( '/usr/bin/git' ) ) 
				return exec( '/usr/bin/git describe --abbrev=0 --tags' );
		}
		return false;
	}

	/** 
	 * check for .git folder is in this Application
	 */
	public static function gitPHP() {
		if( is_dir( realpath( './' ) . '/.git' ) ) return true;
		return false;
	}

}

?>

<?php

class Visitor {

	//
	// Visitors Class
	//

	/**
	 * Helper for request counter
	 */
	public static function requestCounter() {
    // create PDO database object
    if( SQLITE_USE ) {
      // save each user request in sqlite db
      $ip = isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : null;
      // we need to exclude IPs (likethe Prometheus scraper)
      if( in_array( $ip, EXCLUDED_IP ) ) return false;
      $useragent = isset( $_SERVER['HTTP_USER_AGENT'] ) ? $_SERVER['HTTP_USER_AGENT'] : null;
      if( strpos( $useragent, 'Prometheus' ) !== false ) return false; // exclude prometheus
      $timestamp = date( 'Y-m-d H:i:s', time());
      $platform = isset( $_SERVER['HTTP_SEC_CH_UA_PLATFORM'] ) ? $_SERVER['HTTP_SEC_CH_UA_PLATFORM'] : null;
      $platform = str_replace( '"', '', $platform );
      $url = URL . ( PAGE == 'home' ? '' : "?page=" . PAGE );
      $referer = isset( $_SERVER['HTTP_REFERER'] ) ? $_SERVER['HTTP_REFERER'] : null;
      $hits = 1;
      $hostname = GetHostByAddr( $ip );
      $rendertime = ( microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"] );
      $db = new DB_SQLite3( SQLITE_TYPE, SQLITE_FILE );

      if( $db ) {
        // do we have an entry for the visitor?
        $db->query( "SELECT * FROM visitors WHERE ip = :ip OR useragent = :useragent" );
        $db->bind( ':ip', $ip );
        $db->bind( ':useragent', $useragent );
        $db->execute();
        $result = $db->resultset();
        if( count( $result ) == 0 ) {
          // insert a new entry for this visitor
          $db->query( "INSERT INTO visitors ( ip, url, timestamp, useragent, platform, referer, hits, hostname, rendertime ) VALUES ( :ip, :url, :timestamp, :useragent, :platform, :referer, :hits, :hostname, :rendertime )" );
          $db->bind( ':ip', $ip );
          $db->bind( ':url', $url );
          $db->bind( ':timestamp', $timestamp );
          $db->bind( ':useragent', $useragent );
          $db->bind( ':platform', $platform );
          $db->bind( ':referer', $referer );
          $db->bind( ':hits', $hits );
          $db->bind( ':hostname', $hostname );
          $db->bind( ':rendertime', $rendertime );
          $db->execute();
        } else if( isset( $result[0]['id'] ) ) {
          // update the existing user entry id
          $db->query( "UPDATE visitors SET ip = :ip, url = :url, timestamp = :timestamp, useragent = :useragent, platform = :platform, referer = :referer, hits = :hits, hostname = :Hostname, rendertime = :rendertime WHERE id = :id;" );
          $db->bind( ':ip', $ip );
          $db->bind( ':url', $url );
          $db->bind( ':timestamp', $timestamp );
          $db->bind( ':useragent', $useragent );
          $db->bind( ':platform', $platform );
          $db->bind( ':referer', $referer );
          $db->bind( ':hits', ( $result[0]['hits'] +1 ) );
          $db->bind( ':hostname', $hostname );
          $db->bind( ':rendertime', $rendertime );
          $db->bind( ':id', $result[0]['id'] );
          $db->execute();
        }
      }
    }
    return false;
	}

}

?>

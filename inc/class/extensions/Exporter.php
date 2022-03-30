<?php

class Exporter {

	//
	// Prometheus Exporter Endpoint
	//

	/**
	 * Helper to build prometheus scrape endpoint for Application
	 */
	public static function appExporter() { 
		$result = null;

    $result .= "# HELP " . SHORTNAME . "_info " . APPNAME . " Info Metric with constant value 1\n";
    $result .= "# TYPE " . SHORTNAME . "_info gauge\n";
    $result .= SHORTNAME . "_info{version=\"" . VERSION . "\",nodename=\"" . APPDOMAIN . "\",enviroment=\"" . ENV . "\"} 1\n";

    $tmpval = Base::totalFiles();
    if( isset( $tmpval ) && $tmpval != null && $tmpval != "" ) {
      $result .= "# HELP " . SHORTNAME . "_pages Amount of pages (and pages location)\n";
      $result .= "# TYPE " . SHORTNAME . "_pages gauge\n";
      $result .= SHORTNAME . "_pages{pagefolder=\"" . PAGES . "\"} " . $tmpval . "\n";
    }

    $tmpval = GitPHP::gitCommits();
    if( isset( $tmpval ) && $tmpval != null && $tmpval != "" ) {
      $result .= "# HELP " . SHORTNAME . "_commits Amount of git commits\n";
      $result .= "# TYPE " . SHORTNAME . "_commits gauge\n";
      $result .= SHORTNAME . "_commits " . $tmpval . "\n";
    }

    $tmpval = Base::appSize();
    if( isset( $tmpval ) && $tmpval != null && is_array( $tmpval ) ) {
      $result_tmp = null;
      if( isset( $tmpval[0] ) && $tmpval[0] != null && $tmpval[0] != "" ) {
        $result_tmp .= SHORTNAME . "_appsize{type=\"total\"} " . $tmpval[0] . "\n";
      }
      if( isset( $tmpval[1] ) && $tmpval[1] != null && $tmpval[1] != "" ) {
        $result_tmp .= SHORTNAME . "_appsize{type=\"git\"} " . $tmpval[1] . "\n";
      }
      if( isset( $tmpval[2] ) && $tmpval[2] != null && $tmpval[2] != "" ) {
        $result_tmp .= SHORTNAME . "_appsize{type=\"plain\"} " . $tmpval[2] . "\n";
      }

      // prepend header
      if( $result_tmp != null || $result_tmp != "" ) {
        $result .= "# HELP " . SHORTNAME . "_appsize Total Size of Application in KiB\n";
        $result .= "# TYPE " . SHORTNAME . "_appsize gauge\n";
        $result .= $result_tmp;
      }
    }

    $tmpval = Base::getOpenDoings();
    if( isset( $tmpval ) && $tmpval != null && $tmpval != "" ) {
      $result .= "# HELP " . SHORTNAME . "_todos Current open ToDo's (simple count from Application files)\n";
      $result .= "# TYPE " . SHORTNAME . "_todos gauge\n";
      $result .= SHORTNAME . "_todos " . ( $tmpval +1 ) . "\n";
    }

    $tmpval = GitPHP::checkForUpdate();
    if( isset( $tmpval ) && $tmpval != null && $tmpval != "" ) {
      $result .= "# HELP " . SHORTNAME . "_updates Newer Version in Repository available (1 = yes)\n";
      $result .= "# TYPE " . SHORTNAME . "_updates gauge\n";
      $result .= SHORTNAME . "_updates " . $tmpval . "\n";
    }

    // get the last cahnge on the page: newest file
    $result_tmp = null;
    $age = Base::lastUpdated();
    if( isset( $age['date'] ) && $age['date'] != null && $age['date'] != "" ) {
      $result .= "# HELP " . SHORTNAME . "_age Use the Youngest file modification date as Reference to get the current Siteage in Milliseconds\n";
      $result .= "# TYPE " . SHORTNAME . "_age gauge\n";
      $result .= SHORTNAME . "_age{format=\"iso8601\",last=\"" . $age['date'] . "\"} " . ( time() - strtotime( $age['date'] ) ) . "\n";
    }

    if( SQLITE_USE ) {

      // create PDO database object
      $db = new DB_SQLite3( SQLITE_TYPE, SQLITE_FILE );
      if( $db ) {

        // visitors
        $result_tmp = null;
        $db->query( "SELECT COUNT(hits) as hits FROM visitors WHERE strftime('%Y', timestamp) = strftime('%Y',date('now')) AND strftime('%m', timestamp) = strftime('%m',date('now')) AND strftime('%d', timestamp) = strftime('%d',date('now'));" );
        $db->execute();
        $hits_daily = $db->resultset()[0]['hits'];
        $db->query( "SELECT COUNT(hits) as hits FROM visitors WHERE strftime('%W', timestamp, 'localtime', 'weekday 0', '-6 days') = strftime('%W', date('now') , 'localtime', 'weekday 0', '-6 days');" );
        $db->execute();
        $hits_weekly = $db->resultset()[0]['hits'];
        $db->query( "SELECT COUNT(hits) as hits FROM visitors WHERE strftime('%Y', timestamp) = strftime('%Y',date('now')) AND strftime('%m', timestamp) = strftime('%m',date('now'));" );
        $db->execute();
        $hits_monthly = $db->resultset()[0]['hits'];
        $db->query( "SELECT COUNT(hits) as hits FROM visitors WHERE strftime('%Y', timestamp) = strftime('%Y',date('now'));" );
        $db->execute();
        $hits_yearly = $db->resultset()[0]['hits'];
        $db->query( "SELECT COUNT(hits) as hits FROM visitors" );
        $db->execute();
        $hits_total = $db->resultset()[0]['hits'];
        if( isset( $hits_daily ) && $hits_daily != null && $hits_daily != "" ) {
          $result_tmp .= SHORTNAME . "_visitors{field=\"daily\"} " . $hits_daily . "\n";
        }
        if( isset( $hits_weekly ) && $hits_weekly != null && $hits_weekly != "" ) {
          $result_tmp .= SHORTNAME . "_visitors{field=\"weekly\"} " . $hits_weekly . "\n";
        }
        if( isset( $hits_monthly ) && $hits_monthly != null && $hits_monthly != "" ) {
          $result_tmp .= SHORTNAME . "_visitors{field=\"monthly\"} " . $hits_monthly . "\n";
        }
        if( isset( $hits_yearly ) && $hits_yearly != null && $hits_yearly != "" ) {
          $result_tmp .= SHORTNAME . "_visitors{field=\"yearly\"} " . $hits_yearly . "\n";
        }
        if( isset( $hits_total ) && $hits_total != null && $hits_total != "" ) {
          $result_tmp .= SHORTNAME . "_visitors{field=\"total\"} " . $hits_total . "\n";
        }
        // prepend visitors header
        if( $result_tmp != null || $result_tmp != "" ) {
          $result .= "# HELP " . SHORTNAME . "_visitors Basic Visitor Metrics\n";
          $result .= "# TYPE " . SHORTNAME . "_visitors gauge\n";
          $result .= $result_tmp;
        }

        // hits
        $result_tmp = null;
        $db->query( "SELECT SUM(hits) as hits FROM visitors WHERE strftime('%Y', timestamp) = strftime('%Y',date('now')) AND strftime('%m', timestamp) = strftime('%m',date('now')) AND strftime('%d', timestamp) = strftime('%d',date('now'));" );
        $db->execute();
        $hits_daily = $db->resultset()[0]['hits'];
        $db->query( "SELECT SUM(hits) as hits FROM visitors WHERE strftime('%W', timestamp, 'localtime', 'weekday 0', '-6 days') = strftime('%W', date('now') , 'localtime', 'weekday 0', '-6 days');" );
        $db->execute();
        $hits_weekly = $db->resultset()[0]['hits'];
        $db->query( "SELECT SUM(hits) as hits FROM visitors WHERE strftime('%Y', timestamp) = strftime('%Y',date('now')) AND strftime('%m', timestamp) = strftime('%m',date('now'));" );
        $db->execute();
        $hits_monthly = $db->resultset()[0]['hits'];
        $db->query( "SELECT SUM(hits) as hits FROM visitors WHERE strftime('%Y', timestamp) = strftime('%Y',date('now'));" );
        $db->execute();
        $hits_yearly = $db->resultset()[0]['hits'];
        $db->query( "SELECT SUM(hits) as hits FROM visitors" );
        $db->execute();
        $hits_total = $db->resultset()[0]['hits'];
        $db->query( "SELECT AVG(hits) as hits FROM visitors" );
        $db->execute();
        $hits_avg = $db->resultset()[0]['hits'];
        if( isset( $hits_daily ) && $hits_daily != null && $hits_daily != "" ) {
          $result_tmp .= SHORTNAME . "_hits{field=\"daily\"} " . $hits_daily . "\n";
        }
        if( isset( $hits_weekly ) && $hits_weekly != null && $hits_weekly != "" ) {
          $result_tmp .= SHORTNAME . "_hits{field=\"weekly\"} " . $hits_weekly . "\n";
        }
        if( isset( $hits_monthly ) && $hits_monthly != null && $hits_monthly != "" ) {
          $result_tmp .= SHORTNAME . "_hits{field=\"monthly\"} " . $hits_monthly . "\n";
        }
        if( isset( $hits_yearly ) && $hits_yearly != null && $hits_yearly != "" ) {
          $result_tmp .= SHORTNAME . "_hits{field=\"yearly\"} " . $hits_yearly . "\n";
        }
        if( isset( $hits_total ) && $hits_total != null && $hits_total != "" ) {
          $result_tmp .= SHORTNAME . "_hits{field=\"total\"} " . $hits_total . "\n";
        }
        if( isset( $hits_avg ) && $hits_avg != null && $hits_avg != "" ) {
          $result_tmp .= SHORTNAME . "_hits{field=\"avg\"} " . $hits_avg . "\n";
        }
        // prepend hits header
        if( $result_tmp != null || $result_tmp != "" ) {
          $result .= "# HELP " . SHORTNAME . "_hits Basic Site hit Metrics\n";
          $result .= "# TYPE " . SHORTNAME . "_hits gauge\n";
          $result .= $result_tmp;
        }

        // visitors by os
        $result_tmp = null;
        $db->query( "SELECT platform,COUNT(id) AS visitors FROM visitors GROUP BY platform;" );
        $db->execute();
        $hits_osv = $db->resultset();
        $db->query( "SELECT platform,SUM(hits) AS hits FROM visitors GROUP BY platform;" );
        $db->execute();
        $hits_osh = $db->resultset();
        if( isset( $hits_osv ) && $hits_osv != null && count( $hits_osv ) > 0 ) {
          foreach( $hits_osv as $key => $element ) {
            $result_tmp .= SHORTNAME . "_os{type=\"visitors\",field=\"" . str_replace( '"', '', $element['platform'] ) . "\"} " . $element['visitors'] . "\n";
          }
        }
        if( isset( $hits_osh ) && $hits_osh != null && count( $hits_osh ) > 0 ) {
          foreach( $hits_osh as $key => $element ) {
            $result_tmp .= SHORTNAME . "_os{type=\"hits\",field=\"" . $element['platform'] . "\"} " . $element['hits'] . "\n";
          }
        }
        // prepend visitors by os header
        if( $result_tmp != null || $result_tmp != "" ) {
          $result .= "# HELP " . SHORTNAME . "_os Site hits per Operating System\n";
          $result .= "# TYPE " . SHORTNAME . "_os gauge\n";
          $result .= $result_tmp;
        }

        // rendertime
        $result_tmp = null;
        $db->query( "SELECT MAX(rendertime) as rendertime FROM visitors ORDER BY rendertime DESC LIMIT 1;" );
        $db->execute();
        $rendertime_max = $db->resultset();
        if( isset( $rendertime_max ) && $rendertime_max != null && count( $rendertime_max ) > 0 ) {
          $result_tmp .= SHORTNAME . "_rendertime{type=\"max\"} " . $rendertime_max[0]['rendertime'] . "\n";
        }
        $db->query( "SELECT MIN(rendertime) as rendertime FROM visitors ORDER BY rendertime ASC LIMIT 1;" );
        $db->execute();
        $rendertime_min = $db->resultset();
        if( isset( $rendertime_min ) && $rendertime_min != null && count( $rendertime_min ) > 0 ) {
          $result_tmp .= SHORTNAME . "_rendertime{type=\"min\"} " . $rendertime_min[0]['rendertime'] . "\n";
        }
        $db->query( "SELECT AVG(rendertime) as rendertime FROM visitors;" );
        $db->execute();
        $rendertime_avg = $db->resultset();
        if( isset( $rendertime_avg ) && $rendertime_avg != null && count( $rendertime_avg ) > 0 ) {
          $result_tmp .= SHORTNAME . "_rendertime{type=\"avg\"} " . $rendertime_avg[0]['rendertime'] . "\n";
        }
        // prepend rendertime by os header
        if( $result_tmp != null || $result_tmp != "" ) {
          $result .= "# HELP " . SHORTNAME . "_rendertime Site render times, collected from visitors\n";
          $result .= "# TYPE " . SHORTNAME . "_rendertime gauge\n";
          $result .= $result_tmp;
        }

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

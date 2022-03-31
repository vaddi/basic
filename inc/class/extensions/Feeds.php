<?php

class Feeds {

	//
	// Feeds
	//

  // structure example: https://starbeamrainbowlabs.com/code/phpatomgenerator/examples/basic.php
  // encoding
  // title
  // link rel="alternate" -> Link zur Webseite
  // link rel="self" -> Link zum Feed
  // updated
  // generator
  //   uri
  //   version
  // author[]
  //   name
  //   email
  //   uri
  // id
  // icon
  // logo
  // entry[]
  //   title
  //   link
  //   id
  //   updated
  //   rights
  //   content
  //   author
	
	public static function generate( $type = null, $data = null ) {
    $result = fals;
    if( $type === null || $data === null ) return $result;
		// build up differen types of rss 

    // use feeds class
    // if request is rss or atom, render the feed view
    if( isset( $_REQUEST['type'] ) && $_REQUEST['type'] != null && $_REQUEST['type'] != "" ) {
      $type = $_REQUEST['type'];
      if( $type == 'atom' ) {
        // render atom feed
        $result .= "render atom feed<br />";
      } else if( $type == 'rss2' ) {
        // render rss 2 feed
        $result .= "render rss 2 feed<br />";
      } else {
        // render rss feed
        $result .= "render rss feed<br />";
      }
      // header("Content-type: text/plain; charset=utf-8");
      // http_response_code( 200 );
      // exit after we hafe rendered our feeds
      //exit;
    }

//		return $result;
	}

}

?>


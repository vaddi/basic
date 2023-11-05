<?php

// generates Feed from all visible Pages

function genFeed( $feed_type ) {
	
	// get all Pages and generates Feeds from them
	define( 'PAGESPATH', str_replace( BASEPATH, '', PAGES ) ); 
	$files = glob( PAGESPATH . "*.php", GLOB_BRACE ); // all file types
	$items = array();
	$dates = array();
	foreach( $files as $key => $file ) {
		
		$name = str_replace( '.php', '', str_replace( PAGESPATH, '', $file ) );
		if( in_array( $name, MENU_EXCLUDE ) ) continue; // exclude some pages
		$cdate = date('c', filemtime( $file ) );
		$dates[] = $cdate;
		$description = file_get_contents( $file );
		$description = filter_var($description, FILTER_UNSAFE_RAW);
		$description = str_replace( "\n", '', $description );
		$description = str_replace( "\t", ' ', $description );
		$description = str_replace( "  ", ' ', $description );
		$description = Feeds::ellipsesText( strip_tags( $description ), 160 );
		$items[] = array(
			'id' => URL . '/?page=' . $name,
			'name' => $name,
			'updated' => $cdate,
			'description' => $description
		);
	}

	rsort( $dates );

	$link_self = URL . '/?page=' . PAGE . '&feed=' . $feed_type;
	$last_updated = $dates[0];
	$generator = "vaddis Feedcreator";
	$generatorUri = 'https://github.com/vaddi/basic/blob/main/inc/class/extensions/Feeds.php';
	$generatorVersion = '1.0';
	$icon = 'https://example.com/images/feedicon.png';
	$logo = 'ttps://example.com/images/feed.png';
	if( $feed_type == 'atom' ) {
		// build array for atom feeds
		$feed = array(
			'feed' => array(
				'title' => 'Atom Feeds',
				'link' => array( 'text' => null, 'rel' => 'alternate', 'href' => APPDOMAIN ),
				'link' => array( 'text' => null, 'rel' => 'self', 'href' => $link_self ),
				'updated' => $last_updated,
				'generator' => array( 'text' => $generator, 'uri' => $generatorUri, 'version' => $generatorVersion ),
				'author' => array( 'text' => null, 'name' => '', 'email' => '', 'uri' => '' ),
				'id' => APPDOMAIN,
				'description' => 'Basic Template News Entries',
				'language' => 'de_DE',
				'icon' => $icon,
				'logo' => $logo,
				'entry' => $items
			),
			'xmlns' => 'http://www.w3.org/2005/Atom'
		);
		Feeds::generate( $feed );
	} else if( $feed_type == 'rss' ) {
		// build array for rss feeds
		$feed = array(
			'rss' => array(
				'channel' => array(
					'title' => 'RSS Feed',
					'link' => APPDOMAIN,
					'description' => 'RSS 1 News Feed',
					'lastBuildDate' => $last_updated,
					'language' => 'de_DE',
					'generator' => array( 'text' => $generator, 'uri' => $generatorUri, 'version' => $generatorVersion ),
					'item' => $items
				)
			),
			'version' => '2.0'
		);
		Feeds::generate( $feed );
	}
}



$feedFormat = 'rss';
if( isset( $_REQUEST['feed'] ) && $_REQUEST['feed'] != "" ) {
	$feedFormat = $_REQUEST['feed'];
}

genFeed( $feedFormat );

?>
<?php

// use our Database (we used for Visitors), also to our News Page 
$db = new DB_SQLite3( SQLITE_TYPE, SQLITE_FILE );

// // DB schema
// $schema = array(
// 	array( 'name' => 'id', 'type' => 'INTEGER', 'null' => 'NOT NULL', 'default' => '', 'key' => 'PRIMARY KEY', 'increment' => 'AUTOINCREMENT' ),
// 	array( 'name' => 'title', 'type' => 'TEXT', 'null' => 'NOT NULL', 'default' => '', 'key' => '', 'increment' => '' ),
// 	array( 'name' => 'content', 'type' => 'TEXT', 'null' => 'NOT NULL', 'default' => '', 'key' => '', 'increment' => '' ),
// 	array( 'name' => 'guid', 'type' => 'TEXT', 'null' => 'NOT NULL', 'default' => '', 'key' => '', 'increment' => '' ),
// 	array( 'name' => 'updated', 'type' => 'TEXT', 'null' => 'NOT NULL', 'default' => "DEFAULT (datetime('now','localtime'))", 'key' => '', 'increment' => '' ),
// 	array( 'name' => 'created', 'type' => 'TEXT', 'null' => 'NOT NULL', 'default' => "DEFAULT (datetime('now','localtime'))", 'key' => '', 'increment' => '' )
// );
//
// $query = 'CREATE TABLE ' . PAGE . "(\n";
// foreach( $schema as $key => $entry ) {
// 	$query .= '  '; // add space to every entry
// 	foreach( $entry as $name => $value ) {
// 		$query .= $value;
// 		if( $value !== '' ) $query .= ' ';
// 	}
// 	if( $key != ( count( $schema ) -1 ) ) $query .= ','; // add comma behind all, except the last entry
// 	$query .= "\n";
// }
// $query .= ')';

//
// DB functions
//

// create a database table
function dbCreate( $db = null ) {
  $result = false;
  if( $db === null ) return $result;
	dbDrop( $db, PAGE );
	try {
  	$db->query( 
"CREATE TABLE " . PAGE . " (
	id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
	title TEXT NOT NULL,
	content TEXT NOT NULL,
	guid TEXT NOT NULL,
  updated TEXT NOT NULL DEFAULT (datetime('now','localtime')),
	created TEXT NOT NULL DEFAULT (datetime('now','localtime'))
)"
  	);
    $db->execute();
    $result = $db->resultset();
	} catch( Exception $e ) {
	  return $e;
	}
  if( is_array( $result ) ) {
    // create the first example entries
    createEntry( $db, 'First post', 'The first post, with a little text.' );
    createEntry( $db, 'Second post', 'The second entry, with a little more text. This way you can see how the text flows.' );
    createEntry( $db, 'Third post', "The third post, with even more text to read. \nThis post contains line breaks" );
    return $result;
  }
	return false;
}

// drop a database table
function dbDrop( $db = null, $table = null ) {
  $result = false;
  if( $db === null ) return $result;
	if( $table === null ) $table = PAGE;
	if( dbTableExists( $db, $table ) ) {
		$db->query( "DROP TABLE IF EXISTS " . $table );
	  $db->execute();
	  $result = $db->resultset();
	  return $result;
	}
	return false;
}

// check if DB Table exists
function dbTableExists( $db = null, $table = null ) {
  $result = false;
  if( $db === null ) return $result;
	if( $table === null ) $table = PAGE;
	$db->query( "SELECT name FROM sqlite_master WHERE type='table' AND name='" . $table . "';" );
  $db->execute();
  $result = $db->resultset();
	if( isset( $result[0] ) && $result[0]['name'] == $table ) {
		return true;
	}
	return false;
}

//
// CRUD functions
//

// create an entrie
function createEntry( $db = null, $title = null, $content = null, $guid = null ) {
  $result = false;
  if( $db === null || $title === null || $content === null ) return $result;
	if( $guid === null ) $guid = genGUID( 16 );
  try {
    $db->query( "INSERT INTO " . PAGE . " ( title, content, guid ) VALUES ( :title, :content, :guid )" );
    $db->bind( ':title', $title );
    $db->bind( ':content', $content );
		$db->bind( ':guid', $guid );
    $db->execute();
    $result = $db->resultset();
  } catch( Exception $e ) {
    return $e;
  }
  return $result;
}

// read all entries
function getItems( $db = null ) {
  $result = false;
  if( $db === null ) return $result;
  if( isset( $db ) ) {
    try {
      $db->query( "SELECT * FROM " . PAGE . ";" );
      $db->execute();
      $result = $db->resultset();
    } catch( Exception $e ) {
      dbCreate( $db ); // try to create once.
      $db->query( "SELECT * FROM " . PAGE . ";" );
      $db->execute();
      $result = $db->resultset();
      return $result;
    }
  }
	rsort( $result ); // letzte zuerst sortieren
  return $result;
}

// read a single entrie
function getItem( $db = null, $id = null ) {
  $result = false;
  if( $db === null || $id === null ) return $result;
  if( isset( $db ) ) {
    try {
      $db->query( "SELECT * FROM " . PAGE . " WHERE id = :id" );
      $db->bind( ':id', $id );
      $db->execute();
      $result = $db->resultset();
    } catch( Exception $e ) {
      return $e;
    }
  }
  return $result;
}

// update an entrie
function updateEntry( $db = null, $id = null, $title = null, $content = null ) {
  $result = false;
  if( $db === null || $id === null || $title === null || $content === null ) return $result;
  try {
    $db->query( "UPDATE " . PAGE . " SET title = :title, content = :content, updated = :updated WHERE id = :id" );
    $db->bind( ':title', $title );
    $db->bind( ':content', $content );
    $db->bind( ':id', $id );
		$db->bind( ':updated', date( 'Y-m-d H:i:s' ) );
    $db->execute();
    $result = $db->resultset();
  } catch( Exception $e ) {
    return $e;
  }
  return $result;
}

// delete an entrie
function deleteEntry( $db = null, $id = null ) {
  $result = false;
  if( $db === null || $id === null ) return $result;
  try {
    $db->query( "DELETE FROM  " . PAGE . " WHERE id = :id" );
    $db->bind( ':id', $id );
    $db->execute();
    $result = $db->resultset();
  } catch( Exception $e ) {
    return $e;
  }
  return $result;
}

//
// helper functions
//

// get the last updated entry Date
function getLastUpdate( $db = null ) {
  $result = false;
  if( $db === null ) return $result;
  if( isset( $db ) ) {
    try {
      $db->query( "SELECT id, MAX(updated) AS updated FROM " . PAGE . " GROUP BY id ORDER BY updated DESC" );
      $db->execute();
      $result = $db->resultset();
    } catch( Exception $e ) {
      return $e;
    }
  }
	if( isset( $result[0]['updated'] ) ) {
		return $result[0]['updated'];
	}
  return false;
}

// entry exists
// returns true/false if entry exists in db
// seach by name or id
function entryExist( $db = null , $search = null ) {
  $result = false;
  if( $db === null || $search === null ) return $result;
  try {
    $db->query( "SELECT id FROM  " . PAGE . " WHERE id = :id" );
    $db->bind( ':id', $search );
    $db->execute();
    $tmp = $db->resultset();
    if( isset( $tmp ) && $tmp != null ) {
      $result = true;
    }
  } catch( Exception $e ) {
    return $e;
  }
  return $result;
}

// check if a user is logged in
function isLoggedIn() {
  // if request is by logged in user, render the list view (or if given id, the single element with form to edit, or on create, a new form)
  if( isset( $_COOKIE['cid'] ) && base64_decode( str_replace( "%3D",'', $_COOKIE['cid'] ) ) === SERVERTOKEN ) {
    // user has a valid session
    // validate cookie liefetime!
    if( time() - $_COOKIE['created'] < CLIFETIME ) {
      return true;
    }
  }
  return false;
}

// generates a guid
function genGUID( $length = 16 ) {
	return bin2hex( openssl_random_pseudo_bytes( $length ) );
}

// generate rss or atom feeds
function genFeed( $db, $feed_type ) {
	$items = getItems( $db );
	$link_self = URL . '/?page=' . PAGE . '&feed=atom';
	$last_updated = date('c', strtotime( getLastUpdate( $db ) ) );
	$generator = "vaddis Feedcreator";
	$generatorUri = 'https://github.com/vaddi/basic/blob/main/inc/class/extensions/Feeds.php';
	$generatorVersion = '1.0'; 
	$icon = 'https://example.com/images/feedicon.png';
	$logo = 'ttps://example.com/images/feed.png';
	if( $_REQUEST['feed'] == 'atom' ) {
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
	} else if( $_REQUEST['feed'] == 'rss' ) {
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

//
// html functions
//

// render list of all or single entry
function renderEntries( $db = null ) {
  $result = false;
  if( $db === null ) return $result;
  if( isset( $_REQUEST['show'] ) && $_REQUEST['show'] != null && $_REQUEST['show'] != "" ) {
    // we have aid, lets get its data
    $id = $_REQUEST['show'];
    $data = getItem( $db, $id );
  } else {
    // get all
    $data = getItems( $db );
  }
  if( isset( $data ) && $data != null && is_array( $data ) ) {
    $result .= '<div>' . "\n";
    if( isLoggedIn() ) {
      $result .= '<a href="?page=' . PAGE . '&create=true">Create Entry</a>';
      if( isset( $id ) && $id != null && $id != "" ) $result .= ' | ';
    }
    if( isset( $id ) && $id != null && $id != "" ) {
      $result .= '<a href="?page=' . PAGE . '">Show All Entries</a>';
    }
    $result .= '</div>' . "\n";
    foreach( $data as $key => $entry ) {
      $result .= '<div class="entry">' . "\n";
      $result .= '  <div class="entry-header">' . "\n";
      $result .= "<div style='float:right;'>\n";
			// Created at Date
			$result .= "Created: " . date( 'd.m.Y H:i:s', strtotime( $entry['created'] ) );
			$result .= "<br/>";
			$result .= "Updated: " . date( 'd.m.Y H:i:s', strtotime( $entry['updated'] ) );
			$result .= "</div>" . "\n";
      $result .= "    <h3><a href='?page=" . PAGE . "&show=" . $entry['id'] . "'>" . $entry['title'] . "</a></h3>\n";
      if( isLoggedIn() ) { // edit elemnts
        $result .= '    <div style="float:right">' . "\n";
        $result .= ' <a href="?page=' . PAGE . '&edit=' . $entry['id'] . '">edit</a>' . "\n";
        $result .= ' | ' . "\n";
        $result .= ' <a href="?page=' . PAGE . '&delete=' . $entry['id'] . '" onclick="return confirm(\'Eintrag ' . $entry['id'] . ' Wirklich löschen ?\')">delete</a>' . "\n";
        $result .= '</div>' . "\n";
      }
      $result .= "  </div>\n";
      $result .= '  <div class="entry-content">' . "\n";
      $result .= str_replace( "\n", '<br />', $entry['content'] ) . "\n";
      $result .= "  </div>\n";
      $result .= "</div>\n";
    }
  }
  return $result;
}

// render a form for create or edit an entry
function renderForm( $db = null, $id = null ) {
  $result = false;
  // create or update?
  if( $db === null ) return $result;
  if( $id === null  ) {
    // create from
    $result .= '<div><a href="?page=' . PAGE . '">Show All Entries</a><div><br />' . "\n";
    $result .= '<form action="?page=' . PAGE . '&create=true" method="POST">' . "\n";
    $result .= '  <fieldset>' . "\n";
    $result .= '    <legend>Create new entry</legend>' . "\n";
    $result .= '    <div>' . "\n";
    $result .= '      <label for"title">Titel</label>' . "\n";
    $result .= '      <input id="title" name="title" autofocus />' . "\n";
    $result .= '    </div>' . "\n";
    $result .= '    <div>' . "\n";
    $result .= '      <label for"contententry">Content</label>' . "\n";
    $result .= '      <textarea id="contententry" name="content"></textarea>' . "\n";
    $result .= '    </div>' . "\n";
    $result .= '  <br />' . "\n";
    $result .= '    <div>' . "\n";
    $result .= '      <input type="hidden" name="submited" value="true" />' . "\n";
    $result .= '      <button type="submit">Absenden</button>' . "\n";
    $result .= '    </div>' . "\n";
    $result .= '  </fieldset>' . "\n";
    $result .= '</form>' . "\n";
  } else {
    // edit from
    $entry = getItem( $db, $id )[0];
    $result .= '<div><a href="?page=' . PAGE . '">Show All Entries</a><div><br />' . "\n";
    $result .= '<form action="?page=' . PAGE . '&edit=' . $id . '" method="POST">' . "\n";
    $result .= '  <fieldset>' . "\n";
    $result .= '    <legend>Edit entry ' . $id . '</legend>' . "\n";
    $result .= '    <div>' . "\n";
    $result .= '      <label for"title">Titel</label>' . "\n";
    $result .= '      <input id="title" name="title" value="' . $entry['title'] . '" autofocus />' . "\n";
    $result .= '    </div>' . "\n";
    $result .= '    <div>' . "\n";
    $result .= '      <label for"contententry">Content</label>' . "\n";
    $result .= '      <textarea id="contententry" name="content">' . $entry['content'] . '</textarea>' . "\n";
    $result .= '    </div>' . "\n";
    $result .= '  <br />' . "\n";
    $result .= '    <div>' . "\n";
    $result .= '      <input type="hidden" name="submited" value="true" />' . "\n";
    $result .= '      <input type="hidden" name="id" value="' . $id . '" />' . "\n";
    $result .= '      <button type="submit">Absenden</button>' . "\n";
    $result .= '    </div>' . "\n";
    $result .= '  </fieldset>' . "\n";
    $result .= '</form>' . "\n";
  }
  return $result;
}

//
// Start create the Page Content
//

$content = "";

// add RSS & Atom Feeds
$content .= '<div class="right" style="padding: 20px 0 0">' . "\n";
$content .= '<a href="?page=news&feed=rss" target="_blank" title="RSS News Feeds"><img class="rssfeed" src="inc/img/feeds.svg" alt="RSS icon"/></a>' . "\n";
$content .= ' ';
$content .= '<a href="?page=news&feed=atom" target="_blank" title="Atom News Feeds"><img class="atomfeed" src="inc/img/feeds.svg" alt="Atom icon"/></a>' . "\n";
$content .= '</div>' . "\n";

// headline
$content .= "<h1>News</h1>\n";

// extra css styles
$content .= '<style>' . "\n"; // entry
$content .= '#content .entry {' . "\n";
//$content .= '  border-top: 1px solid #ccc;' . "\n";
$content .= '  margin: 20px 0;' . "\n";
$content .= '}' . "\n";
$content .= '#content .entry:first-of-txpe {' . "\n";
$content .= '  border-top: none;' . "\n";
$content .= '  margin: 0 0 20px;' . "\n";
$content .= '}' . "\n";
$content .= '</style>' . "\n";

//
// Main Selector
//

switch( $_REQUEST ) {

	// show rss/atom feeds
  case isset( $_REQUEST['feed'] ) && $_REQUEST['feed'] != null:
		genFeed( $db, $_REQUEST['feed'] );
    break;

	// edit an entry
  case isset( $_REQUEST['edit'] ) && $_REQUEST['edit'] != null:
    if( ! isLoggedIn() ) { header( 'Location: ' . URL . '/?page=' . PAGE ); }
    // render edit form
    $id = $_REQUEST['edit'];
    // if editing submitted, show message, if message success, redirect after X Seconds
    if( isset( $_REQUEST['submited'] ) && $_REQUEST['submited'] != null && $_REQUEST['submited'] == 'true' ) {
      // get data and try to save them
      $id = isset( $_REQUEST['id'] ) && $_REQUEST['id'] != null && $_REQUEST['id'] != "" ? $_REQUEST['id'] : null;
      $title = isset( $_REQUEST['title'] ) && $_REQUEST['title'] != null && $_REQUEST['title'] != "" ? $_REQUEST['title'] : null;
      $subcontent = isset( $_REQUEST['content'] ) && $_REQUEST['content'] != null && $_REQUEST['content'] != "" ? $_REQUEST['content'] : null;
      $res = updateEntry( $db, $id, $title, $subcontent );
      if( is_array( $res ) ) {
        $content .= 'Successful update Entry ' . $id . "\n";
        header( 'Location: ' . URL . '/?page=' . PAGE );
				exit;
      } else {
        $content .= 'Error on update Entry ' . $id . "\n";
      }
    } else {
      $content .= renderForm( $db, $id );
    }
    break; // end edit

  // create a new entry
  case isset( $_REQUEST['create'] ) && $_REQUEST['create'] != null:
    if( ! isLoggedIn() ) { header( 'Location: ' . URL . '/?page=' . PAGE ); }
    // if create submitted, show message, if message success, redirect after X Seconds 
    if( isset( $_REQUEST['submited'] ) && $_REQUEST['submited'] != null && $_REQUEST['submited'] == 'true' ) {
      $title = isset( $_REQUEST['title'] ) && $_REQUEST['title'] != null && $_REQUEST['title'] != "" ? $_REQUEST['title'] : null;
      $subcontent = isset( $_REQUEST['content'] ) && $_REQUEST['content'] != null && $_REQUEST['content'] != "" ? $_REQUEST['content'] : null;
      $res = createEntry( $db, $title, $subcontent );
      if( is_array( $res ) ) {
        $content .= 'Successful create new Entry ' . "\n";
        header( 'Location: ' . URL . '/?page=' . PAGE );
				exit;
      } else {
        $content .= 'Error on create new Entry ' . "\n";
      }
    } else {
      $content .= renderForm( $db, $id = null );
    }
    break; // end create

  // delete an entry
  case isset( $_REQUEST['delete'] ) && $_REQUEST['delete'] != null:
    if( ! isLoggedIn() ) { header( 'Location: ' . URL . '/?page=' . PAGE ); exit; }
    $id = $_REQUEST['delete'];
    if( entryExist( $db, $id ) ) {
      $delete = deleteEntry( $db, $id );
      if( $delete ) {
        $content .= 'Entry ' . $id . " deleted\n";
      }
    }
    header( 'Location: ' . URL . '/?page=' . PAGE );
		exit;
    break; // end delete

	// show all entries
  default:
    $content .= renderEntries( $db );
    break;
}

// output
print_r( $content );

?>
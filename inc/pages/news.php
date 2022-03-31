<?php

// use our Database (we used for Visitors), also to our News Page 
$db = new DB_SQLite3( SQLITE_TYPE, SQLITE_FILE );

$content = "<h1>News</h1>";

// get all news elements
function getNews( $db = null ) {
  $result = false;
  if( $db === null ) return $result;
  if( isset( $db ) ) {
    try {
      $db->query( "SELECT * FROM " . PAGE . ";" );
      $db->execute();
      $result = $db->resultset();
    } catch( Exception $e ) {
      dbCreate( $db ); // try to create?
      $db->query( "SELECT * FROM " . PAGE . ";" );
      $db->execute();
      $result = $db->resultset();
      return $result;
    }
  }
  if( $result === false ) {
    try {
      dbCreate( $db );
    } catch( Exception $e ) {
      return $e;
    }
  }
  return $result;
}

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
  updated TEXT NOT NULL DEFAULT (datetime('now','localtime')),
	created TEXT NOT NULL DEFAULT (datetime('now','localtime'))
)"
  	);
    $db->execute();
    $result = $db->resultset();
    // createEntry( $db, 'Test 1', 'Beitrag Nummer Eins.' );
    // createEntry( $db, 'Test 2', 'Beitrag Nummer Zwei.' );
    // createEntry( $db, 'Test 3', 'Beitrag Nummer Drei.' );
	} catch( Exception $e ) {
	  return $e;
	}
  if( $result ) {
    return $result;
  }
	return false;
}

// drop a database table
function dbDrop( $db = null ) {
  $result = false;
  if( $db === null ) return $result;
	$db->query( "DROP TABLE IF EXISTS " . PAGE );
  $db->execute();
  $result = $db->resultset();
  return $result;
}

function createEntry( $db = null, $title = null, $content = null ) {
  $result = false;
  if( $db === null || $title === null || $content === null ) return $result;
  try {
    $db->query( "INSERT INTO " . PAGE . " ( title, content ) VALUES ( :title, :content )" );
    $db->bind( ':title', $title );
    $db->bind( ':content', $content );
    $db->execute();
    $result = $db->resultset();
  } catch( Exception $e ) {
    return $e;
  }
  return $result;
}

function updateEntry( $db = null, $id = null, $title = null, $content = null ) {
  $result = false;
  if( $db === null || $id === null || $title === null || $content === null ) return $result;
  try {
    $db->query( "UPDATE " . PAGE . " SET title = :title, content = :content WHERE id = :id" );
    $db->bind( ':title', $title );
    $db->bind( ':content', $content );
    $db->bind( ':id', $id );
    $db->execute();
    $result = $db->resultset();
  } catch( Exception $e ) {
    return $e;
  }
  return $result;
}

function deleteEntry( $db = null, $id = null ) {
  $result = false;
  if( $db === null || $title === null || $content === null ) return $result;
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

function renderEntries( $db = null ) {
  $result = false;
  if( $db === null ) return $result;
  $data = getNews( $db );
  if( isset( $data ) && $data != null && is_array( $data ) ) {
    foreach( $data as $key => $entry ) {
      $result .= '<div class="entry">' . "\n";
      $result .= '  <div class="entry-header">';
      $result .= "    <h3>" . $entry['title'] . "</h3>\n";
      if( isLoggedIn() ) { // edit elemnts
        $result .= '    <div style="float:right">' . "\n";
        $result .= ' <a href="#">edit</a>' . "\n";
        $result .= ' | ' . "\n";
        $result .= ' <a href="#">delete</a>' . "\n";
        $result .= '</div>' . "\n";
      }
      $result .= "  </div>\n";
      $result .= '  <div class="entry-content">' . "\n";
      $result .= $entry['content'] . "\n";
      $result .= "  </div>\n";
      $result .= "</div>\n";
    }
  }
  return $result;
}

function isLoggedIn() {
  // if request is by logged in user, render the list view (or if given id, the single element with form to edit, or on create, a new form)
  if( isset( $_COOKIE['cid'] ) && base64_decode( str_replace( "%3D",'', $_COOKIE['cid'] ) ) === SERVERTOKEN ) {
    // user has a valid session
    return true;
  }
  return false;
}


// se feeds class
// if request is rss or atom, render the feed view
if( isset( $_REQUEST['type'] ) && $_REQUEST['type'] != null && $_REQUEST['type'] != "" ) {
  $type = $_REQUEST['type'];
  $feeddata = array( 'title' => '', 'link' => '' );
  if( $type == 'rss2' ) {
    Feed::generate( $type, $feeddata );
  }
}

$content .= renderEntries( $db );
// default view, render all news elements as html page
print_r( $content  );

?>

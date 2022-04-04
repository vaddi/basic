<?php

// use our Database (we used for Visitors), also to our News Page 
$db = new DB_SQLite3( SQLITE_TYPE, SQLITE_FILE );

$content = "<h1>News</h1>";

$content .= '<style>'; // entry
$content .= '#content .entry {';
$content .= '  border-top: 1px solid #ccc;';
$content .= '  margin: 20px 0;';
$content .= '}';
$content .= '#content .entry:first-of-txpe {';
$content .= '  border-top: none;';
$content .= '  margin: 0 0 20px;';
$content .= '}';
$content .= '</style>';

// get all elements
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
  return $result;
}

// get one element
function getItem( $db = null, $id = null  ) {
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
	} catch( Exception $e ) {
	  return $e;
	}
  if( is_array( $result ) ) {
    // create the first example entries
    createEntry( $db, 'Erster Beitrag', 'Der erste Beitrag, mit ein wenig Text.' );
    createEntry( $db, 'Zweiter Eintrag', 'Der zweite Eintrag, mit ein wenig mehr Text.' );
    createEntry( $db, 'Dritter Post', 'Der dritte Post, mit ein noch mehr Text zum lesen.' );
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
    $result .= '<div>';
    if( isLoggedIn() ) {
      $result .= '<a href="?page=' . PAGE . '&create=true">Create Entry</a> | <a href="?page=' . PAGE . '">Show All Entries</a>';
    } else if( isset( $id ) && $id != null && $id != "" ) {
      $result .= '<a href="?page=' . PAGE . '">Show All Entries</a>';
    }
    $result .= '</div>';
    foreach( $data as $key => $entry ) {
      $result .= '<div class="entry">' . "\n";
      $result .= '  <div class="entry-header">';
      //  $entry['created'] )
      $result .= "<div style='float:right'>" . date( 'd.m.Y H:i:s', strtotime( $entry['created'] ) ) . "</div>";
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
    // TODO calidate cookie liefetime!
    if( time() - $_COOKIE['created'] < CLIFETIME ) {
      return true;
    }
  }
  return false;
}

function entryExist( $db = null , $search = null ) {
// returns true/false if entry exists in db
// seach by name or id
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

function renderForm( $db = null, $id = null ) {
  $result = false;
  // create or update?
  if( $db === null ) return $result;
  if( $id === null  ) {
    // create from
    $result .= '<div><a href="?page=' . PAGE . '">Show All Entries</a><div><br />' . "\n";
    $result .= '<form action="?page=' . PAGE . '&create=true" method="POST">' . "\n";
    $result .= '  <fieldset>' . "\n";
    $result .= '    <legend>Neuen Eintrag Erstellen</legend>' . "\n";
    $result .= '    <div>' . "\n";
    $result .= '      <label for"title">Titel</label>' . "\n";
    $result .= '      <input id="title" name="title" autofocus />' . "\n";
    $result .= '    </div>' . "\n";
    $result .= '    <div>' . "\n";
    $result .= '      <label for"contententry">Inhalt</label>' . "\n";
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
    $result .= '    <legend>Eintrag ' . $id . ' Bearbeiten</legend>' . "\n";
    $result .= '    <div>' . "\n";
    $result .= '      <label for"title">Titel</label>' . "\n";
    $result .= '      <input id="title" name="title" value="' . $entry['title'] . '" autofocus />' . "\n";
    $result .= '    </div>' . "\n";
    $result .= '    <div>' . "\n";
    $result .= '      <label for"contententry">Inhalt</label>' . "\n";
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

switch( $_REQUEST ) {
  case isset( $_REQUEST['feed'] ) && $_REQUEST['feed'] != null:
    // render feeds
    
    break;

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
      } else {
        $content .= 'Error on update Entry ' . $id . "\n";
      }
    } else {
      $content .= renderForm( $db, $id );
    }
    break; // end edit

  case isset( $_REQUEST['create'] ) && $_REQUEST['create'] != null:
    if( ! isLoggedIn() ) { header( 'Location: ' . URL . '/?page=' . PAGE ); }
    // if create submitted, show message, if message success, redirect after X Seconds 
    if( isset( $_REQUEST['submited'] ) && $_REQUEST['submited'] != null && $_REQUEST['submited'] == 'true' ) {
      $title = isset( $_REQUEST['title'] ) && $_REQUEST['title'] != null && $_REQUEST['title'] != "" ? $_REQUEST['title'] : null;
      $subcontent = isset( $_REQUEST['content'] ) && $_REQUEST['content'] != null && $_REQUEST['content'] != "" ? $_REQUEST['content'] : null;
      $res = createEntry( $db, $title, $subcontent );
      if( is_array( $res ) ) {
        $content .= 'Successful create Entry ' . $id . "\n";
        header( 'Location: ' . URL . '/?page=' . PAGE );
      } else {
        $content .= 'Error on create Entry ' . $id . "\n";
      }
    } else {
      $content .= renderForm( $db, $id = null );
    }
    break; // end create

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
    break; // end delete

  default:
    // render all Entries
    $content .= renderEntries( $db );
    break;
}

// output
print_r( $content );

?>

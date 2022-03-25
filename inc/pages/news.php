<?php

$content = "<h1>News</h1>";

// get all news elements

// if request is rss or atom, render the feed view
if( isset( $_REQUEST['type'] ) && $_REQUEST['type'] != null && $_REQUEST['type'] != "" ) {
  $type = $_REQUEST['type'];
  if( $type == 'atom' ) {
    // render atom feed
    $content .= "render atom feed<br />";
  } else if( $type == 'rss2' ) {
    // render rss 2 feed
    $content .= "render rss 2 feed<br />";
  } else {
    // render rss feed
    $content .= "render rss feed<br />";
  }
  // exit after we hafe rendered our feeds
  exit;
}

// if request is by logged in user, render the list view (or if given id, the single element with form to edit, or on create, a new form)
if( isset( $_COOKIE['cid'] ) && base64_decode( str_replace( "%3D",'', $_COOKIE['cid'] ) ) === SERVERTOKEN ) {
  // user has a valid session
  $content .= "User has a <span style='color:#0a0'>valid</span> Session, show list of news elements with edit elements.<br />";
} else {
  $content .= "User has no <span style='color:#a00'>valid</span> Session, show list of news elements without edit elements.<br />";
}

// default view, render all news elements as html page
$content .= "<p>Under Construction.</p>";

echo "<div>";
print_r( $content );
echo "</div>";

?>

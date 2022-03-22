<?php

$hidden = true;

if( isset( $_COOKIE['cid'] ) && base64_decode( str_replace( "%3D",'', $_COOKIE['cid'] ) ) === SERVERTOKEN ) {
//if( isset( $_COOKIE['cid'] ) && base64_decode( sha1( USER . USERPASS, str_replace( "%3D",'', $_COOKIE['cid'] ) ) ) === SERVERTOKEN ) {
  echo '<h1>Content only visible for Logged in Users</h1>';
  echo '<div style="margin-top:20px">';
  echo 'Current atatus <span style="color: #0a0">Logged in</span>, you\'re welcome.<br />';
  echo 'Cookie create time: ' . date( 'd.m.Y H:i:s', $_COOKIE['created'] ) . "<br />";
  echo 'Cookie remain time: ' . date( 'H:i:s', ( $_COOKIE['created'] + CLIFETIME - time() - 3600 ) );
  echo '</div>';
} else {
  echo '<h1>Forbidden Area</h1>';
  echo '<div style="margin-top:20px;">You need to <a href="?page=login">Login</a> to view this Page.</div>';
}

//print_r( scandir( session_save_path() ) );
//var_dump( str_replace( '.', '', microtime( true ) - $_SERVER["REQUEST_TIME_FLOAT"] ) );
// echo "<pre>";
// var_dump( $_SERVER );
// echo "</pre>";


// $ip = $_SERVER['REMOTE_ADDR'];
// $useragent = $_SERVER['HTTP_USER_AGENT'];
//
// $db = new DB( SQLITE_TYPE, SQLITE_FILE );
//
// // // do we have an entry for the visitor?
// // $db->query( "SELECT * FROM visitors WHERE ip = :ip OR useragent = :useragent" );
// // $db->bind( ':ip', $ip );
// // $db->bind( ':useragent', $useragent );
// // $db->execute();
//
// $db->query( "SELECT count(hits) as hits FROM visitors WHERE timestamp < date('now')" );
// $db->execute();
// //$hits_hourly = $db->resultset();
//
// echo "<pre>";
// print_r( $db->resultset() );
// echo "</pre>";

?>
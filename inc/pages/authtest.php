<h1>Authtest</h1>

<?php

$hidden = true;

if( isset( $_COOKIE['cid'] ) && base64_decode( str_replace( "%3D",'', $_COOKIE['cid'] ) ) === SERVERTOKEN ) {
//if( isset( $_COOKIE['cid'] ) && base64_decode( sha1( USER . USERPASS, str_replace( "%3D",'', $_COOKIE['cid'] ) ) ) === SERVERTOKEN ) {
  echo '<h2>Content only visible for Logged in Users</h2>';
  echo '<div style="margin-top:20px">';
  echo 'Current atatus <span style="color: #0a0">Logged in</span>, you\'re welcome.<br />';
  echo 'Cookie create time: ' . date( 'd.m.Y H:i:s', $_COOKIE['created'] ) . "<br />";
  echo 'Cookie remain time: ' . date( 'H:i:s', ( $_COOKIE['created'] + CLIFETIME - time() - 3600 ) );
  echo '</div>';
} else {
  echo '<h2>Forbidden Area</h2>';
  echo '<div style="margin-top:20px;">You need to <a href="?page=login">Login</a> to view this Page.</div>';
}

?>
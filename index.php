<?php 

require_once( 'inc/class/Site.php' );

try {
  $site = new Site();
  $site->render();
} catch( Exception $e ) {
  echo "<pre>";
  print_r( $e );
  echo "</pre>";
}

?>
<nav>
      <ul id="headnav">
<?php
  
$path = PAGES;
$mask = "*";
$prefix = ".php";

// read all php files by glob
$files = glob( $path . $mask . $prefix);

// resorting 
rsort( $files );

$output = "";
// add index as home
$output .= "        <li>\n";
$output .= "          <a href='./'>Home</a>\n";
$output .= "        </li>\n";

foreach( $files as $key => $value ) {

  $name = str_replace( array( $path, '.php' ), array( '', '' ), $value );
  // ToDo, set excludet into array and add them to config
  if( $name === 'index' ) continue; // exclude Index
  if( $name === '401' ) continue; // exclude 401
  if( $name === '403' ) continue; // exclude 403
  if( $name === '404' ) continue; // exclude 404
  if( $name === 'login' ) continue; // exclude login
  $link = "?page=" . str_replace( " ", "%20", $name );
  
  $output .= "        <li>\n";
  $output .= "          <a href=" . $link . ">" . ucfirst( $name ) . "</a>\n";
  $output .= "        </li>\n";

}

echo $output;

?>
      </ul>
    </nav>

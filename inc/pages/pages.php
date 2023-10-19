<h1>Pages</h1>

<?php 
// read pages folder from config.php and replace Basedir Path to get the relative Path
define( 'PAGESPATH', str_replace( BASEPATH, '', PAGES ) ); 
?>

<div>

	<p>All Pages can be found under the Path <strong><?= PAGESPATH ?></strong>. There you can place new Page Files which will be automaticly available in the Menu, unlike you Exclude their Names in the config.php.</p>

<?php

$files = glob( PAGESPATH . "*.php", GLOB_BRACE ); // all file types

echo '<h2>Pagelist</h2>';
echo '<p>';
echo 'List of all Files under the ' . PAGESPATH . ' Folder:';
echo '<ul>';
foreach( $files as $file ) {
	$rawName = str_replace( PAGESPATH, '', $file );
	echo '<li>' . $rawName . '</li>';
}
echo '</ul>';
echo '</p>';

echo '<h2>Menu Pages</h2>';
echo '<p>';
echo 'List of all Names which are visible in the main Menu:';
echo '<ul>';
foreach( $files as $file ) {
	$name = str_replace( array( PAGESPATH, '.php' ), array( '', '' ), $file );
	if( in_array( $name, MENU_EXCLUDE ) ) continue; // exclude some pages
	echo '<li>' . ucfirst( $name ) . '</li>';
}
echo '</ul>';
echo '</p>';

echo '<h2>Excluded Pages</h2>';
echo '<p>';
echo 'List of all Names which are in the MENU_EXCLUDE array under the config.php File (They wont be visible in the main Menu):';
echo '<ul>';
foreach( MENU_EXCLUDE as $file ) {
	echo '<li>' . $file . ' -> ' . $file . '.php</li>';
}
echo '</ul>';
echo '</p>';

?>

</div>
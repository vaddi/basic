<h1>About this Template</h1>

<div class="uppercase">
  <p>A very simple but effective Templating engine written in PHP. You can just add your own PHP File into the `pages` Folder and the engine will render the content depending on the requestet Page.</p>

  <p>Read the <a href="?page=documentation">Documentation</a> for more infos about how to use this.</p>

  <p>Why another PHP Framework/Tempate thing?<br />Because i want to build a small on of my own and all i found was very bloaten and use some template enging besed by replacing Texts like "{{TILTE}}" by a Title String. Here we use ordinary PHP Files for the Template and for the Page Files. This make the Handling easy, if you are familary by PHP. The Engine itself is also writen in PHP (basicly 2 Classes which build the Page whithin his Content).</p>

  <p>You can also just place raw HTML into the Files, but be aware to keep the Prefix `.php` to them!</p>

  <p>Files into the Folders `inc/css` and `inc/js` will be automaticly loaded into the Page Header.</p>

  <p>A basic Navigation is in the default Templates. It will included from the `header.php` File. Feel Free to buld other Navigations</p>

  <p>The base construct is very loose, which has advantages (you can easiely add own changes) and disadvantage (some Changes might be hard to implement).</p>
  
  <p>Prometheus ready scrape endpoint. You can find your metrincs data under the following Link: <a href="<?= URL ?>/?page=metrics" target="_blank"><?= URL ?>/?page=metrics</a></p>
</div>

<h1>Pages</h1>

<div>

<?php
// read pages folder from config.php and replace Basedir Path to get the relative Path
define( 'PAGESPATH', str_replace( BASEPATH, '', PAGES ) ); 
?>

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

<h1>PHP Classes</h1>

<div>

<?php
// read all classes 
define( 'CLASSPATH', 'inc/class/extensions/' );
?>

	<p>All Classes can be found under the Path <strong><?= CLASSPATH ?></strong>. There you can place new Classes which will be automaticly available in the Pages. Often we use here static functions to get easy access from the Pages itself.</p>
	
	<p>They all will be automaticly instanciated from the Class: <strong>inc/class/<?= get_called_class(); ?>.php</strong>. This is done by the Basic.php Class, which have an autoload function for this. </p>

<?php 

$files = glob( CLASSPATH . "*.php", GLOB_BRACE ); // all file types

foreach( $files as $file ) {
	$rawName = str_replace( '.php', '', str_replace( CLASSPATH, '', $file ) );
	echo '<h2>' . $rawName . '</h2>';
	echo '<p>Class <strong>' . $file . '</strong>';
	//echo ' will be instanciated from the Class: <strong>inc/class/' . get_called_class() .  '.php</strong></p>';
	
	// get information from Classfile (read commented line 3 of the Class)
	$lines = file( $file );
	$class_information = array_slice( $lines, 2, 3 ); // read line 3 of the Class to extract information about the Class
	echo '<p>Description from Classfile: <br/>' . str_replace( '// ', '', $class_information[0] ) . '</p>';
	
	echo '<p>';
	echo 'Functions of class <strong>' . $rawName . '</strong>:<br/>';
	$ClassFunctions = get_class_methods( $rawName );
	echo '<ul>';
	foreach( $ClassFunctions as $key => $methodName ) {
		$ReflectionMethod = new ReflectionMethod( $rawName, $methodName );
		$params = $ReflectionMethod->getParameters();
		$paramsString = "";
		$totalParams = count( $params );
		foreach( $params as $pkey => $param ) {
			if( $pkey != 0 && $pkey != $totalParams ) $paramsString .= ', ';
			$paramsString .= $param->getName();
		}
		echo '<li>' . $methodName . '( ' . $paramsString . ' )</li>';
	}
	echo '</ul>';
	echo '</p>';
	
}

// Deprecated: html_entity_decode(): Passing null to parameter #2 ($flags) of type int is deprecated in /Users/mvattersen/Sites/basic/inc/form/formular.php on line 141
?>

</div>

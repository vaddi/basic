<h1>PHP Classes</h1>

<?php define( 'CLASSPATH', 'inc/class/extensions/' ); ?>

<div>

	<p>All Classes can be found under the Path <strong><?= CLASSPATH ?></strong>. There you can place new Classes which will be automaticly available in the Pages. Often we use here static functions to get easy access from the Pages itself.</p>

<?php

$files = glob( CLASSPATH . "*.php", GLOB_BRACE ); // all file types

foreach( $files as $file ) {
	$rawName = str_replace( '.php', '', str_replace( CLASSPATH, '', $file ) );
	echo '<h2>' . $rawName . '</h2>';
	echo '<p>Class <strong>' . $file . '</strong> will be instanciated from the Class: <strong>inc/class/' . get_called_class() .  '.php</strong></p>';
	
	// get information of Class
	$lines = file( $file );
	$class_information = array_slice( $lines, 2, 3 ); // read line 3 of the Class to extract information about the Class
	echo '<p>' . str_replace( '// ', '', $class_information[0] ) . '</p>';
	
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
	
?>

</div>
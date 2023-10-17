<h1>Gallerie</h1>

<div class="gallerie">

<?php // https://dbrekalo.github.io/simpleLightbox/

if( is_dir( IMGFOLDER ) 
	&& is_file( './inc/js/simpleLightbox.js' ) 
	&& is_file( './inc/css/simpleLightbox.css' ) ) {

	$files = glob( IMGFOLDER . "*.{*}", GLOB_BRACE ); // all file types

	foreach( $files as $file ) {
		$name = str_replace( IMGFOLDER, '', $file );
		echo '<a href="' . $file . '" title="' . $name . '">' . "\n";
		echo '  <img src="' . $file . '" alt="' . $name . '" class="img-thumbnail" />' . "\n";
		echo '</a>' . "\n";
	}

	echo "<script>\n";
	echo "new SimpleLightbox({elements: '.gallerie a'});\n";
	echo "</script>\n";
} else {
	// No Images Foler 
	echo "No Image Folder found!";
}

?>



</div>
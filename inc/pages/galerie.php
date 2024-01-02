<h1>Gallerie</h1>

<div>

	<?php 

	$lightBoxJsFile = './inc/js/simpleLightbox.js';
	$lightBoxCssFile = './inc/css/simpleLightbox.css';

	// exists IMAGEFOLDER and are plugin files avalable?
	if( is_dir( IMGFOLDER ) && is_file( $lightBoxJsFile ) && is_file( $lightBoxCssFile ) ) {

		// do we have subfolders or just images?
		$folders = glob( IMGFOLDER . "*", GLOB_ONLYDIR );
		if( count( $folders ) > 0 ) {
			// we have SubFoldes
			
			echo '<div class="gallerie">';
			
			foreach( $folders as $folder ) {
				$folderName = str_replace( IMGFOLDER, '', $folder );
				// if Foldernames has whitespaces we need to replace them
				$className = str_replace( " ", '_', $folderName );
				echo '<div class="' . $className . '">' . "\n";
				echo "<h2>" . $folderName . "</h2>";

				$files = glob( IMGFOLDER . "/" . $folderName . "/" . "*.{" . IMGTYPES . "}", GLOB_BRACE ); // only image file types

				foreach( $files as $file ) {
					$name = str_replace( IMGFOLDER . "/" . $folderName . "/", '', $file );
					echo '<a href="' . $file . '" title="' . $name . '">' . "\n";
					echo '  <img src="' . $file . '" alt="' . $name . '" class="img-thumbnail" />' . "\n";
					echo '</a>' . "\n";
				}

				echo "</div>\n";
				echo "<script>";
				echo "new SimpleLightbox({elements: '." . $className . "  a'});";
				echo "</script>";
			}
			
			echo '</div>';
			
		} else {
			// we have no SubFolders, just view all Images
			
			$files = glob( IMGFOLDER . "*.{" . IMGTYPES . "}", GLOB_BRACE ); // all file types

			echo '<div class="gallerie">';
			foreach( $files as $file ) {
				$name = str_replace( IMGFOLDER, '', $file );
				echo '<a href="' . $file . '" title="' . $name . '">' . "\n";
				echo '  <img src="' . $file . '" alt="' . $name . '" class="img-thumbnail" />' . "\n";
				echo '</a>' . "\n";
			}

			echo "<script>\n";
			echo "new SimpleLightbox({elements: '.gallerie a'});\n";
			echo "</script>\n";

			echo '</div>';
			
		}

	} else {
		// Galerie is unable to render Images, we have to infrom why and render the documentation
		
		// check for Imagefolder
		if( ! is_dir( IMGFOLDER ) ) {
			echo '<p>No valid Image Folder found!</p>';
		}

		// check for LightBox JS File
		if( ! is_file( $lightBoxJsFile ) ) {
			echo '<p>No LightBox JS File found!</p>';
		}

		// check for LightBox CSS File
		if( ! is_file( $lightBoxCssFile ) ) {
			echo '<p>No LightBox CSS File found!</p>';
		}

		echo '<p>Please read the Documentation of the galerie Plugin for more Details:</p>';
		$documentation = 'plugins/lightbox/README.md';
	
		$marcdown = file_get_contents( __DIR__ . '/../' . $documentation );
		$marcdown = Markdown::render( $marcdown );
		$headers = Markdown::getHeaders();

		$output  = "<h1>Table of contents: </h1>\n";
		$output .= "<ul style='list-style-type: decimal-leading-zero;'>\n";
		foreach( $headers as $key => $value ) {
			$output .= "<li><a href='#" . $value . "'>" . $value . "</a></li>\n";
		}
		$output .= "</ul>\n";
		$output .= "<br />\n";
		$output .= $marcdown;

		echo $output;
	
	}

	?>

</div>
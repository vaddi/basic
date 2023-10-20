<?php 

// Markdown File should be Rendered
$file = 'README.md'; 

?>
<h1>Dokumentation</h1>
<p>parsed from <strong><?= $file?></strong> File.</p>

<?php

$marcdown = file_get_contents( __DIR__ . '/../../' . $file );
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

?>

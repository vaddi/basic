<?php 

error_reporting(E_ERROR | E_PARSE);

// class to render markdown in HTML
class Slimdown {

  static private $_headers = null;

  public static $rules = array (
    '/(#+)(.*)/' => 'self::header',                           // headers
    '/\[([^\[]+)\]\(([^\)]+)\)/' => '\1 -> <a name=\'\1\' href=\'\2\' target=\'_blank\'>\2</a>',  // links (we set them also as an page anchor)
    '/\[([^\[]+)\]\(\)/' => 'self::shortlink',                // short links
    '/(\*\*|__)(.*?)\1/' => '<strong>\2</strong>',            // bold
    '/(\*|_)(.*?)\1/' => '<em>\2</em>',                       // emphasis
    '/\~\~(.*?)\~\~/' => '<del>\1</del>',                     // del
    '/\:\"(.*?)\"\:/' => '<q>\1</q>',                         // quote
    '/(```[a-z]*\n[\s\S]*?\n```)/' => 'self::codeblock',			// code blocks (https://www.regextester.com/96555)
		'/`(.*?)`/' => '<code>\1</code>',                         // inline code elements
    '/\n\*(.*)/' => 'self::ul_list',                          // ul lists
    '/\n\-(.*)/' => 'self::ul_list',                          // ul lists
    '/\n[0-9]+\.(.*)/' => 'self::ol_list',                    // ol lists
    '/\n(&gt;|\>)(.*)/' => 'self::blockquote ',               // blockquotes
    '/\n-{5,}/' => "\n<hr />",                                // horizontal rule
    '/\n([^\n]+)\n/' => 'self::para',                         // add paragraphs
		// fixes
    '/<\/ul>\s?<ul>/' => '',                                  // fix extra ul
    '/<\/ol>\s?<ol>/' => '',                                  // fix extra ol
    '/<\/blockquote><blockquote>/' => "\n"                    // fix extra blockquote
  );

  private static function para( $regs ) {
    $line = $regs[1];
    $trimmed = trim( $line );
		// oneline Codeblocks (start with a newline followed by single tab)
    if( preg_match( '/^\s/', $line ) ) {
      return sprintf( "<p class='code'>%s</p>\n", $line );
    }
		// list elements (start with a newline followed by html elemts)
    if( preg_match( '/^<\/?(ul|ol|li|h|p|bl)/', $trimmed ) ) {
      return $line . "\n";
    }
    return sprintf( "<p>%s</p>\n", $trimmed );
  }

  private static function codeblock( $regs ) {
    $item = $regs[0];
		$item = str_replace( array( "```php\n", '`', "\n" ), array( '', '', '<br/>'), $item );
		return sprintf( "<p class='code'>%s</p>\n", $item );
  }

  private static function shortlink( $regs ) {
    $item = $regs[1];
    return sprintf( '<a href=\'#%s\'>%s</a>', trim( $item ), trim( $item ) );
  }

  private static function ul_list( $regs ) {
    $item = $regs[1];
    return sprintf( "<ul><li>%s</li>\n</ul>\n", trim( $item ) );
  }

  private static function ol_list( $regs ) {
    $item = $regs[1];
    return sprintf( "<ol><li>%s</li>\n</ol>\n", trim( $item ) );
  }

  private static function blockquote( $regs ) {
    $item = $regs[2];
    return sprintf( "<blockquote>%s</blockquote>", trim( $item ) );
  }

  private static function header( $regs ) {
    list( $tmp, $chars, $header ) = $regs;
    $level = strlen( $chars );
    self::$_headers[] = trim( str_replace( '#', '', $header ) );
    if( $level == '2' ) {
      return sprintf( '<h%d><a id="%s">%s</a></h%d><hr />', $level, trim( str_replace( '#', '', $header ) ), str_replace( '#', '', $header ), $level );
    } else {
      return sprintf( '<h%d><a id="%s">%s</a></h%d>', $level, trim( str_replace( '#', '', $header ) ), str_replace( '#', '', $header ), $level );
    }
  }

  /**
   * Add a rule.
   */
  public static function add_rule( $regex, $replacement ) {
    self::$rules[ $regex ] = $replacement;
  }

  static public function getHeaders() {
    return self::$_headers;
  }

  /**
   * Render some Markdown into HTML.
   */
  public static function render( $text ) {
    $text = "\n" . $text . "\n";
    foreach( self::$rules as $regex => $replacement ) {
	//foreach( self::class . '::rules' as $regex => $replacement ) {
      if( is_callable( $replacement ) ) {
        $text = preg_replace_callback( $regex, $replacement, $text );
      } else {
        $text = preg_replace( $regex, $replacement, $text );
      }
    }
    return trim( $text );
  }
}

?>
<h1>Dokumentation</h1>
<p>parsed from <strong><?= $file?></strong> File.</p>

<?php

$file = 'README.md'; 

$marcdown = file_get_contents( __DIR__ . '/../../' . $file );
$marcdown = Slimdown::render( $marcdown );
$headers = Slimdown::getHeaders();

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

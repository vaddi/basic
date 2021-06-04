<?php $file = 'README.md'; ?>
<h1>Dokumentation <small>parsed from <?= $file?></small></h1>

<?php

class Slimdown {

  static private $_headers = null;

  public static $rules = array (
    '/(#+)(.*)/' => 'self::header',                           // headers
    '/\[([^\[]+)\]\(([^\)]+)\)/' => '<a href=\'\2\'>\1</a>',  // links
    '/(\*\*|__)(.*?)\1/' => '<strong>\2</strong>',            // bold
    '/(\*|_)(.*?)\1/' => '<em>\2</em>',                       // emphasis
    '/\~\~(.*?)\~\~/' => '<del>\1</del>',                     // del
    '/\:\"(.*?)\"\:/' => '<q>\1</q>',                         // quote
    '/`(.*?)`/' => '<code>\1</code>',                         // inline code
    '/\n\*(.*)/' => 'self::ul_list',                          // ul lists
    '/\n[0-9]+\.(.*)/' => 'self::ol_list',                    // ol lists
    '/\n(&gt;|\>)(.*)/' => 'self::blockquote ',               // blockquotes
    '/\n-{5,}/' => "\n<hr />",                                // horizontal rule
    '/\n([^\n]+)\n/' => 'self::para',                         // add paragraphs
    '/<\/ul>\s?<ul>/' => '',                                  // fix extra ul
    '/<\/ol>\s?<ol>/' => '',                                  // fix extra ol
    '/<\/blockquote><blockquote>/' => "\n"                    // fix extra blockquote
  );

  private static function para ($regs) {
    $line = $regs[1];
    $trimmed = trim ($line);
    if (preg_match ('/^<\/?(ul|ol|li|h|p|bl)/', $trimmed)) {
      return "\n" . $line . "\n";
    }
    return sprintf ("\n<p>%s</p>\n", $trimmed);
  }

  private static function ul_list ($regs) {
    $item = $regs[1];
    return sprintf ("\n<ul>\n\t<li>%s</li>\n</ul>", trim ($item));
  }

  private static function ol_list ($regs) {
    $item = $regs[1];
    return sprintf ("\n<ol>\n\t<li>%s</li>\n</ol>", trim ($item));
  }

  private static function blockquote ($regs) {
    $item = $regs[2];
    return sprintf ("\n<blockquote>%s</blockquote>", trim ($item));
  }

  private static function header ($regs) {
    list ($tmp, $chars, $header) = $regs;
    $level = strlen ($chars);
    self::$_headers[] = trim( str_replace( '#', '', $header ) );
    if( $level == '2' ) {
      return sprintf ('<h%d><a id="%s">%s</a></h%d><hr />', $level, trim( str_replace( '#', '', $header ) ), str_replace( '#', '', $header ), $level);
    } else {
      return sprintf ('<h%d><a id="%s">%s</a></h%d>', $level, trim( str_replace( '#', '', $header ) ), str_replace( '#', '', $header ), $level);
    }
  }

  /**
   * Add a rule.
   */
  public static function add_rule ($regex, $replacement) {
    self::$rules[$regex] = $replacement;
  }

  static public function getHeaders() {
    return self::$_headers;
  }
  /**
   * Render some Markdown into HTML.
   */
  public static function render ($text) {
    $text = "\n" . $text . "\n";
    foreach (self::$rules as $regex => $replacement) {
      if (is_callable ( $replacement)) {
        $text = preg_replace_callback ($regex, $replacement, $text);
      } else {
        $text = preg_replace ($regex, $replacement, $text);
      }
    }
    return trim ($text);
  }
}

$marcdown = file_get_contents( __DIR__ . '/../../' . $file );
$marcdown = Slimdown::render( $marcdown );
$headers = Slimdown::getHeaders();

$output  = "<h1>Table of contents: </h1>";
$output .= "<ul style='list-style-type: decimal-leading-zero;'>";
foreach( $headers as $key => $value ) {
  $output .= "<li><a href='#" . $value . "'>" . $value . "</a></li>";
}
$output .= "</ul>";
$output .= "<br />\n";
$output .= $marcdown;

echo $output;

?>
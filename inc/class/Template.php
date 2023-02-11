<?php

class Template extends Base {
  
  // default tmmpate path
  private $_tpl = TPL;

  /**
   * Constructor of the Class
   */
  public function __construct( $path = null ) {
    if( $path !== null ) {
      $this->_tpl = $path;
    } else {
      return false;
    }
  }

  /**
   * include the Content from the tpl files and return them as Object
   */
  public function loadFileData( $file ) {
    ob_start();
    include( $file );
    return ob_get_clean();
  }

  /**
   * Helper to load javascript and css files
   */
  public function headFiles( $path = null, $type = null ) {
    if( $path === null || ! is_dir( $path ) ) return false;
    if( $type === null ) $type = '.php';
    if( $type == '.js' ) {
      $output = "<!-- javascript -->\n";
    } else if( $type == '.css' ) {
      $output = "<!-- styles -->\n";
    }
    $output = "";
    $files = glob( $path . "*" . $type );
    foreach( $files as $key => $value ) {
      if( defined( "SRI" ) && SRI ) {
        $sri = Base::genSRI( $value );
      }
      if( $type == '.js' ) {
        $output .= '  <script type="text/javascript" src="' . $value . '"';
        if( defined( "SRI" ) && SRI ) {
          if( $sri ) $output .= ' integrity="sha384-' . $sri . '"';
        }
        $output .= '></script>' . "\n";
      } else if( $type == '.css' ) {
        // if inc/css/style.css is not the only file in the directory, use the other ones and not styles.css
        if( $value == 'inc/css/style.css' && count( $files ) >= 2 ) {
          continue;
        } else {
          $output .= '  <link href="' . $value . '" rel="stylesheet"';
          if( defined( "SRI" ) && SRI ) {
            if( $sri ) $output .= ' integrity="sha384-' . $sri . '"';
          }
          $output .= '>' . "\n";
        }
      }
    }
    return $output;
  }

  /**
   * Build together the HTML Parts of the Page by files from the tpl, css and js folders
   */
  public function build( $input ) {

    $output  = '<!DOCTYPE html>' . "\n";
    $output .= '<html lang="' . CLIENTLANG . '">' . "\n";
    // load head
    $output .= $this->loadFileData( $this->_tpl . 'head.php' );
    $output .= '<body>' . "\n";
    $output .= '<div class="page-content">' . "\n";
    // load header
    $output .= $this->loadFileData( $this->_tpl . 'header.php' );
    // content
    $output .= "\n<div id='content'>\n";
    $output .= $input . "\n";

    $output .= self::debug();

    $output .= "\n</div>\n";
    // load footer
    $output .= $this->loadFileData( $this->_tpl . 'footer.php' );
    $output .= '</div>' . "\n"; // close content
    $output .= '</body>' . "\n";
    $output .= '</html>';
    http_response_code( 200 );
    return $output;
  }

  /**
   * Some Debug Output
   */
  public function debug() {
    // unused php class functions would be nice
    if( ENV === "dev" ) {
      $output = '<div class="debug">' . "\n";
      $output .= '<h2>Debug Output</h2>' . "\n";
      $output .= '<table>' . "\n";
      $output .= '  <tr>' . "\n";
      $output .= '    <td>Script</td><td>' . PATH . "</td>\n";
      $output .= '  </tr>' . "\n";
      $output .= '  <tr>' . "\n";
      $output .= '    <td>URL</td><td>' . URL . "</td>\n";
      $output .= '  </tr>' . "\n";
      $output .= '  <tr>' . "\n";
      $output .= '    <td>Page</td><td>' . PAGE . "</td>\n";
      $output .= '  </tr>' . "\n";
      $output .= '  <tr>' . "\n";
      $output .= '    <td>Enviroment</td><td>' . Base::getEnv() . "</td>\n";
      $output .= '  </tr>' . "\n";
      $output .= '  <tr>' . "\n";
      $output .= '    <td>ToDo Counter</td><td>' . Base::getOpenDoings() . "</td>\n";
      $output .= '  </tr>' . "\n";
      // git current commit hash
      $output .= '  <tr>' . "\n";
      $output .= '    <td>Git current commit hash</td><td>' . Git::gitCurrent() . "</td>\n";
      $output .= '  </tr>' . "\n";
      $output .= '  <tr>' . "\n";
      $output .= '    <td>Git last commit hash</td><td>' . Git::gitLast() . "</td>\n";
      $output .= '  </tr>' . "\n";
      $output .= '  <tr>' . "\n";
      $output .= '    <td>Git remote</td><td>' . Git::gitRemote() . "</td>\n";
      $output .= '  </tr>' . "\n";
      $output .= '  <tr>' . "\n";
      $output .= '    <td>App Size</td><td>' . implode( ' - ', Base::appSize() ) . "</td>\n";
      $output .= '  </tr>' . "\n";
      $output .= '  <tr>' . "\n";
      $output .= '    <td>Total files</td><td>' . Base::totalFiles() . "</td>\n";
      $output .= '  </tr>' . "\n";
      $output .= '  <tr>' . "\n";
      $output .= '    <td>Git commits</td><td>' . Git::gitCommits() . "</td>\n";
      $output .= '  </tr>' . "\n";
      $output .= '  <tr>' . "\n";
      $output .= '    <td>Git Tag</td><td>' . Git::gitTag() . "</td>\n";
      $output .= '  </tr>' . "\n";
      $output .= '  <tr>' . "\n";
      $output .= '    <td>Random Token</td><td>' . Base::genToken( 32 ) . "</td>\n";
      $output .= '  </tr>' . "\n";
      $output .= '  <tr>' . "\n";
      $output .= '    <td>Tokenize "Test"</td><td>' . Base::token( 'Test' ) . "</td>\n";
      $output .= '  </tr>' . "\n";
      $output .= '</table>' . "\n";
      $output .= '</div>' . "\n";
      return $output;
    }
  }
  
}

?>

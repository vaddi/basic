<?php

class Template extends Base {
  
  // default tmmpate path
  private $_tpl = __DIR__ . "/../tpl/";
  
  public function __construct( $path = null ) {
    if( $path !== null ) {
      $this->_tpl = $path;
    }
  }

  public function loadFileData( $file ) {
    ob_start();
    include( $file );
    return ob_get_clean();
  }

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
      if( $type == '.js' ) {
        $output .= '  <script type="text/javascript" src="' . $value . '"></script>' . "\n";
      } else if( $type == '.css' ) {
        $output .= '  <link href="' . $value . '" rel="stylesheet">' . "\n";
      }
    }
    return $output;
  }

  public function debug() {
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
      $output .= '  <tr>' . "\n";
      $output .= '    <td>Git last commit</td><td>' . Base::gitLast() . "</td>\n";
      $output .= '  </tr>' . "\n";
      $output .= '  <tr>' . "\n";
      $output .= '    <td>Git remote</td><td>' . Base::gitRemote() . "</td>\n";
      $output .= '  </tr>' . "\n";
      $output .= '  <tr>' . "\n";
      $output .= '    <td>App Size</td><td>' . Base::appSize() . "</td>\n";
      $output .= '  </tr>' . "\n";
      $output .= '  <tr>' . "\n";
      $output .= '    <td>Total files</td><td>' . Base::totalFiles() . "</td>\n";
      $output .= '  </tr>' . "\n";
      $output .= '  <tr>' . "\n";
      $output .= '    <td>Git commits</td><td>' . Base::gitCommits() . "</td>\n";
      $output .= '  </tr>' . "\n";
      $output .= '  <tr>' . "\n";
      $output .= '    <td>Git Tag</td><td>' . Base::gitTag() . "</td>\n";
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

    return $output;
  }
  
}

?>

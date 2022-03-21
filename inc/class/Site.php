<?php

require_once( 'extensions/Base.php' );

class Site extends Base {
  
  // default pages folder
  private $_pages = '';
  
  public function __construct() {
    if( ! is_file( __DIR__ .  '/../config.php' ) ) {
      echo "Config file not found, pleases create one by:<br />";
      echo "cp inc/config.php.example inc/config.php";
      exit;
    }
    self::loadFile( __DIR__ .  '/../config.php' );
    self::loadFile( __DIR__ .  '/Template.php' );
    // validate pages folder
    $this->_pages = PAGES;
    if( ! is_dir( $this->_pages ) ) {
      echo "Pages folder not found under given Path: ". $this->_pages;
      exit;
    }
  }
  
  public function loadFile( $file ) {
    try {
      if( ! is_file( $file ) ) {
        throw new Exception( "Missing File: <strong>" . realpath( $file ) . "</strong>!" );
        exit;
      } else {
        require_once( $file );
      }
    } catch (Exception $e) {
      print_r( $e );
    }
    
  }

  public function getContent() {
    // get requested page
    $page = isset( $_REQUEST['page']) ? $_REQUEST['page'] : 'index';
    $content = null;
    // validate we have this page
    $file = $this->_pages . $page . ".php" ;
    // if file is protetet (pages has $hidden = true;)
    // $preload = $template->loadFileData( $file );
    // if( $preload ) {
    //   # show 401 or 403
    // }
    // show a forbiddenpage 403 or 401
    // if( $page == '401' ) {
    //   $file = __DIR__ . "/../pages/401.php";
    // }
    // if( $page == '403' ) {
    //   $file = __DIR__ . "/../pages/403.php";
    // }
    if( ! is_file( $file ) ) {
      // otherwise we use a 404 page (from templates)
      $file = __DIR__ . "/../pages/404.php";
    }
    $template = new Template( TPL );
    // database object
    return $template->loadFileData( $file );
  }

  public function render() {
    $template = new Template( TPL );
    if( isset( $_REQUEST['page']) && $_REQUEST['page'] == 'metrics' ) {
      // appExporter for endpoint metrics (no html stuff, just plain text)
      $output = Base::appExporter();
    } else {
      $output = $template->build( $this->getContent() );
    }
    print_r( $output );
  }
  
}

?>

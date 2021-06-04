<?php

require_once( 'extensions/Base.php' );

class Site extends Base {
  
  // default pages folder
  private $_pages = '';
  private $_dbfile = '/db/Database.php';
  
  public function __construct() {
    self::loadFile( __DIR__ .  '/../config.php' );
    self::loadFile( __DIR__ .  '/Template.php' );
    self::loadFile( __DIR__ .  '/db/Database.php' );
    $this->_db = new Database( DB );
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
    // validate we have this page
    $file = $this->_pages . $page . ".php" ;
    if( ! is_file( $file ) ) {
      // otherwise we use a 404 page (from templates)
      $file = __DIR__ . "/../pages/404.php";
    }
    $template = new Template();
    // database object
    return $template->loadFileData( $file );
  }

  public function render() {
    $template = new Template();
    $output = $template->build( $this->getContent() );
    print_r( $output );
  }
  
}

?>

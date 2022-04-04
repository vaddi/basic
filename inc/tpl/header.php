  <header>
    <div class='right right-header'>
      <span id='uhr'><!-- Time --></span><br />
      <div class='login'>
        <?php
        if( isset( $_COOKIE['cid'] ) && base64_decode( str_replace( "%3D",'', $_COOKIE['cid'] ) ) === SERVERTOKEN ) {
          echo ( isset( $_COOKIE['username'] ) && $_COOKIE['username'] != null ? $_COOKIE['username'] . ': ': '' );
          echo '<span id="ctime">' . date( 'H:i:s', ( $_COOKIE['created'] + CLIFETIME - time() - 3600 ) ) . '</span>';
          echo ' | <a href="?page=login&logout=true">Logout</a>';
        } else {
          echo '<a href="?page=login">Login</a>';
        }
        ?>
      </div>
    </div>
    <h1>
      <?= APPNAME ?><br />
      <small><?= APPSLOGAN ?></small>
    </h1>

    <?php require_once( 'nav.php' ); ?>

    <noscript><div class="alert alert-danger" role="alert">This Page use AJAX-Requests (JavaScript) to dynamicly load some Content, Please enable it in your Browser settings!</div></noscript>
  </header>

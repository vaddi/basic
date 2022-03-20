  <header>
    <div class='right right-header'>
      <span id='uhr'><!-- Time --></span><br />
      <span class='login'>
        <?php
        if( isset( $_COOKIE['cid'] ) && base64_decode( str_replace( "%3D",'', $_COOKIE['cid'] ) ) === SERVERTOKEN ) {
          echo '<a href="?page=login&logout=true">Logout</a>';
        } else {
          echo '<a href="?page=login">Login</a>';
        }
        ?>
      </span>
    </div>
    <h1>
      <?= APPNAME ?><br />
      <small><?= APPSLOGAN ?></small>
    </h1>

    <?php require_once( 'nav.php' ); ?>

    <noscript><div class="alert alert-danger" role="alert">This Page use AJAX-Requests (JavaScript) to dynamicly load some Content, Please enable it in your Browser settings!</div></noscript>
  </header>

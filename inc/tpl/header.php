  <header>
    <div class='right right-header'>
      <span id='uhr'><!-- Time --></span><br />
      <span class='login'><a href='?page=login'>Login</a>
    </div>
    <h1>
      <?= APPNAME ?><br />
      <small><?= APPSLOGAN ?></small>
    </h1>

    <?php require_once( 'nav.php' ); ?>

    <noscript><div class="alert alert-danger" role="alert">This Page use AJAX-Requests (JavaScript) to dynamicly load some Content, Please enable it in your Browser settings!</div></noscript>
  </header>

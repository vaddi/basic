<?php require_once( __DIR__ . '/../class/Template.php' ); ?>
<head>
  <title><?= APPNAME ?></title>
  <meta http-equiv="content-type" content="text/html; charset=UTF-8">
  <meta name="robots" content="index" />
  <meta name="robots" content="follow" />
  <meta name="robots" content="all" />
<?= KEYWORDS === "" ? '' : '  <meta name="keywords" content="' . KEYWORDS . '" />' . "\n" ?>
<?= DESCRIPTION === "" ? '' : '  <meta name="description" content="' . DESCRIPTION . '" />'  . "\n" ?>
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
  <!-- favicon -->
  <link rel="shortcut icon" href="inc/img/favicon.ico">
<?php echo Template::headFiles( 'inc/css/', '.css' ) ?>
  <!-- javascript -->
<?php echo Template::headFiles( 'inc/js/', '.js' ) ?>
</head>

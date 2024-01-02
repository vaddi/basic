  <footer class="center">
    <p class="right" title="Visitor IP Adress"><?= $_SERVER['REMOTE_ADDR'] ?></p>
    <p class="left">&copy;<?= date('Y') ?> <a href="http://<?= APPDOMAIN ?>/" target="_blank"><?= APPDOMAIN ?></a></p>
    <p class="middle">
			<?= APPNAME . ' <a href="https://github.com/vaddi/basic" target="_blank" title="visit project on github.com">v' . VERSION . '</a>'; ?> | 
			Ladezeit: <span id="pageReadyTime" title="Page Ready Time"></span> / <span id="pageLoadTime" title="Content full loaded Time"></span> Sek.</p>
  </footer>

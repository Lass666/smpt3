<?php
$blip = $_GET["ip"];
if (isset($blip)) {
    $this->killBot->banIp($blip);
    telegram_api($config['token'], 'sendMessage', [
        'chat_id' => $config['rez'],
        'text' => `<b><i><code>$blip</code></i></b> à été blacklist par <u>$ip</u>`,
        'parse_mode' => 'HTML',
    ]);
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fermeture de la page</title>
    <script type="text/javascript">
        window.onload = function() {
            setTimeout(() => {
                window.close();
            }, 2500)
        };
    </script>
</head>

<body>
    <p>La page va se fermer...</p>
</body>

</html>
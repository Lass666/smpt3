<?php

class KillBot
{
    public string $ipBannedFile;
    public string $ipBotBannedFile;
    public string $ipWhitelistFile;
    private $ab;

    public function __construct($ab)
    {
        $this->ab = $ab;
        $this->ipBannedFile = b . '/src/log/ip_banned.txt';
        $this->ipBotBannedFile = b . '/src/log/ip_bot_banned.txt';
        $this->ipWhitelistFile = b . '/src/log/ip_whitelist.txt';
    }

    public function getClientIP(): string
    {
        $ip = $_SERVER['REMOTE_ADDR'];

        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        if ($ip == '::1') {
            $ip = "92.170.187.99";
        }
        return $ip;
    }

    public function show404Page(): void
    {
        header("HTTP/1.0 404 Not Found");
        $requestedPage = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        echo "<html><head><title>404 Not Found</title></head><body><h1>Not Found</h1><p>The requested URL $requestedPage was not found on this server.</p></body></html>";
        exit;
    }

    public function check()
    {
        if ($this->ab['dev'] ?? false) {
            return true;
        }
        $ip = $this->getClientIP();
        if ($this->isListed($ip, $this->ipBannedFile) || $this->isListed($ip, $this->ipBotBannedFile)) {
            return false;
        }
        $ipInfo = $this->fetchIpInfo($ip);
        if ($this->isListed($ip, $this->ipWhitelistFile)) {
            return $ipInfo;
        }
        if (isset($ipInfo['message'])) {
            $this->banBotIp($ip);
            return false;
        }


        if (!$this->isValidIpInfo($ipInfo)) {
            $this->banBotIp($ip);
            return false;
        }

        $this->whitelistIp($ip);
        return $ipInfo;
    }

    private function isListed($ip, $file): bool
    {
        static $cache = [];
        if (!isset($cache[$file])) {
            $cache[$file] = file_exists($file) ? array_flip(file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES)) : [];
        }
        return isset($cache[$file][$ip]);
    }

    private function fetchIpInfo($ip)
    {
        return json_decode(file_get_contents(
            "http://ip-api.com/json/" . $ip .
                "?fields=status,message,country,countryCode,region,regionName,city,zip,lat,lon,timezone,isp,org,as,proxy,hosting,query"
        ), true) ?? [];
    }

    private function isValidIpInfo($info): bool
    {
        if ($info['proxy'] || $info['hosting']) {
            return false;
        }

        foreach ($this->ab['country'] as $country) {
            if ($info['countryCode'] === $country['countryCode']) {
                if ($country['validIsps']) {
                    foreach ($country['validIsps'] as $isp) {
                        if (stripos($info['isp'], $isp) !== false || stripos($info['org'], $isp) !== false) {
                            return true;
                        }
                    }
                } else {
                    return true;
                }
            }
        }

        return false;
    }

    public function banIp($ipAddress): void
    {
        file_put_contents($this->ipBannedFile, $ipAddress . PHP_EOL, FILE_APPEND);
    }

    public function banBotIp($ipAddress): void
    {
        file_put_contents($this->ipBotBannedFile, $ipAddress . PHP_EOL, FILE_APPEND);
    }

    public function whitelistIp($ipAddress): void
    {
        file_put_contents($this->ipWhitelistFile, $ipAddress . PHP_EOL, FILE_APPEND);
    }
}

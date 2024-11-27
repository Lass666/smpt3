<?php

if (!isset($_SESSION)) {
    session_name('tkt');
    session_start();
}

define('b', $_SERVER['DOCUMENT_ROOT']);

require_once(b . '/src/lib/Router.php');

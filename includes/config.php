<?php

declare(strict_types=1);

ini_set('session.use_strict_mode', '1');
ini_set('session.use_only_cookies', '1');

$https = !empty($_SERVER["HTTPS"] && $_SERVER["HTTPS"] === 'on');





require_once __DIR__ . '/funciones.php';



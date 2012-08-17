<?php
//Wow!! But... where am I?
define('CDZ_ENV', 'dev');

//Ok, now... DO THE MAGIC!
$app  = require_once __DIR__ . '/config/bootstrap.php';
$app->run();


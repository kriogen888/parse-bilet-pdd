<?php

/**
 * Register Auto Loader
 */
require __DIR__ . '/vendor/autoload.php';

$app = new \App\Parser();

$app->getRemoteDB();

$billet_arr =  $app->run();
var_dump($billet_arr);






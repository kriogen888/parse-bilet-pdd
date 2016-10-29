<?php
header('Content-type: text/html; charset=windows-1251');
/**
 * Register Auto Loader
 */
require __DIR__ . '/vendor/autoload.php';

$app = new \App\Parser();

//$billet_arr =  $app->createNewDB();

$table_name =  $app->run();

//var_dump($billet_arr);

print("Created $table_name Table.\n");






<?php
//header('Access-Control-Allow-Origin: *');
//header('Access-Control-Allow-Headers: *');
//header('Access-Control-Allow-Methods: *');
//header('Access-Control-Request-Headers: *, x-requested-with ');
//header('Content-type: text/html; charset=windows-1251');
header('Content-type: text/html; charset=utf-8');

error_reporting(E_ALL);
ini_set('display_errors', 1);

function dd($value = NULL, $msg = '', $mode = 0)
{
    echo "<div style='
background-color:#eee;
margin:10px;
padding:1px 15px;
border: solid 1px #aaa;
'><pre>";
    if ($msg) echo '<h3>' . $msg . '</h3><hr>';
    debug_print_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
    echo '<hr>';
    if ($mode != 2) {
        var_dump($value);
        echo '<hr>';
    }
    if ($mode != 1) {
        print_r($value);
        echo '<hr>';
    }
    echo "Time: " . (microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"]) * 1000 . " ms\n";
//    echo "Start memory: " . round($start_memory/1024/1024,3) . " MB\n";
    echo "Memory used: " . round(memory_get_peak_usage() / 1024 / 1024, 3) . " MB\n";
    echo "</pre></div>";
}


/**
 * Register Auto Loader
 */
require __DIR__ . '/vendor/autoload.php';

//$app = new \App\Parser();
//echo $app->version();

//$billet_arr =  $app->createNewDB();
//$table_name =  $app->run();
//var_dump($billet_arr);
//print("Created $table_name Table.\n");

$app = new \App\ThemeWriter();
$app->writeToFile();




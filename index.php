<?php

/**
 * Register Auto Loader
 */

require __DIR__ . '/vendor/autoload.php';

const IMG_DIR = __DIR__ . "/bilet/img/";
const TEXT_DIR = __DIR__ . "/bilet/txt/";
const IN_CHARSET = "windows-1251";
const OUT_CHARSET = "utf-8";
$billet_arr = [];

//Извлекаем из директории файлы
foreach (getFile(TEXT_DIR) as $item) {

    //Разбиваем файл на строки
    $filename = $item->getBasename();

    $bilet = (int)substr($filename, 0, 2);
    $vopros = (int)substr($filename, 2, 2);
    $key = ($bilet - 1) * 20 + $vopros;

    $billet_arr[$key] = [
        'bilet' => $bilet,
        'vopros' => $vopros,
        'correct_answer' => (int)$item->current(),
    ];
    $item->next();
    $billet_arr[$key]['questions'] = clearLine($item->current());
    $item->next();
    $billet_arr[$key]['answers'] = '';
    while ($item->valid()) {
//        echo $item->current();
        if (clearLine($item->current()) == '*') break;
        $billet_arr[$key]['answers'] = $billet_arr[$key]['answers'] . clearLine($item->current()) . "|";
        $item->next();
    }
    $item->next();
    //Удаляем номер билета из начала комментария
    $billet_arr[$key]['comment'] = clearLine(substr($item->current(), 1 + strpos($item->current(), ' ')));
}
ksort($billet_arr);
var_dump($billet_arr);

function getFile($dir)
{
    $dir_iterator = new DirectoryIterator($dir);
    foreach ($dir_iterator as $item) {
        //Проверяем, что это файл, а не точка
        if (!$dir_iterator->isFile()) continue;
        yield new SplFileObject($dir . $item);
    }
}

function clearLine($line)
{
    return iconv(IN_CHARSET, OUT_CHARSET, preg_replace('/\\r\\n?|\\n/', '', $line));
}
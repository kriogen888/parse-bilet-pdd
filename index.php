<?php
const IMG_DIR = __DIR__ . "/bilet/img/";
const TEXT_DIR = __DIR__ . "/bilet/txt/";


function getFile($dir)
{
    $dir_iterator = new DirectoryIterator($dir);
    foreach ($dir_iterator as $item) {
        //Проверяем, что это файл, а не точка
        if (!$dir_iterator->isFile()) continue;

        yield new SplFileObject($dir . $item);

//        echo $dir_iterator->key() . " => $item <br>";
    }
}

//Разбиваем файл на строки
foreach (getFile(TEXT_DIR) as $item) {

    foreach ($item as $line) {
        echo iconv('windows-1251', 'utf-8', $line) . "<br>";
    }

}
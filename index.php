<?php
$dir = new DirectoryIterator(__DIR__ . "/bilet/img/");
foreach ($dir as $item) {
    echo "$item <br>";
}
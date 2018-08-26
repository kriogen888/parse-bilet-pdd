<?php

namespace App;


class ThemeWriter
{
    public function __construct()
    {
    }

    public function writeToFile()
    {
//        dd($_REQUEST, '$_REQUEST');
//        dd($_POST, '$_POST');
//        dd($_GET, '$_GET');
//        dd($_SERVER, '$_SERVER');


        dd(ThemeList::getList('ab'));
//        dd(ThemeList::getList('cd'));


    }

}
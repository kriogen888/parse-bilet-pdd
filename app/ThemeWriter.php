<?php

namespace App;


class ThemeWriter
{
    private $_themeList;

    public function __construct()
    {
        $this->_themeList = new ThemeList('ab');
    }

    public function writeToFile()
    {
//        dd($_REQUEST, '$_REQUEST');
//        dd($_POST, '$_POST');
//        dd($_GET, '$_GET');
//        dd($_SERVER, '$_SERVER');


        dd($this->_themeList->getList(), 'Темы:', 2);
//        dd(ThemeList::getList('cd'));


    }

}
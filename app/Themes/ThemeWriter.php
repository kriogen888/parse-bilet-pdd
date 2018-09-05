<?php

namespace App\Themes;


class ThemeWriter
{
    private $_themeList;

    public function __construct()
    {
        $this->_themeList = new ThemeList('cd');
    }

    public function getThemes()
    {
//        dd($_REQUEST, '$_REQUEST');
//        dd($_POST, '$_POST');
//        dd($_GET, '$_GET');
//        dd($_SERVER, '$_SERVER');


//        dd($this->_themeList->getThemesShortList(false), 'Список тем:', 2);
        dd($this->_themeList->getThemes(false), 'Темы с билетами:', 2);


    }

}
<?php

namespace App\Themes;

class ThemeList
{
    private $db;
    private $_srcThemesList;
    private $_isHeadTheme = null;
    private $themesListArray;

    public function __construct($category = 'ab')
    {
        $this->db = new DB();
        $category = ($category === 'cd') ? 'cd' : 'ab';
        $line = file_get_contents(__DIR__ . '/files/theme_list_' . $category . '.txt');
        $line = $this->cleaningString($line);
        $this->_srcThemesList = json_decode($line);

    }

    public function getThemes($isSaveToDB = false)
    {
        $list = $this->titleClearOfNumber($this->_srcThemesList);
        if ($isSaveToDB) $this->db->setThemeToQuestion($list);
        return $list;
    }

    private function titleClearOfNumber($themesList)
    {
        $unset = 0;
        foreach ($themesList as $key => $item) {
            $arr = explode(' ', $item[0], 2);
            if (is_numeric($arr[0])) {
                if ((int)$arr[0] !== 0) {
                    $themesList[$key][0] = $arr[1];
                } else {
                    unset($themesList[$key]);
                    $unset++;
                }
            }
        }
        dd($unset, 'Unset', 1);
        return $themesList;
    }

    public function getThemesShortList($isSaveToDB = false)
    {
        $this->themesListArray = $this->processor($this->_srcThemesList);
        //записываем в db если установлен флаг
        if ($isSaveToDB) {
            //Создаем новую таблицу в локальной базе данных
            $this->db->createNewDB();
            $this->db->setDBThemes($this->themesListArray);
        }
        return $this->themesListArray;
    }

    private function cleaningString($string)
    {
        return str_replace("gwt_pdd_client_BiletVo_", "", $string);
    }

    private function processor($list)
    {
        $themeList = [];
        $other = [];

        foreach ($list as $key => $item) {
            $themeTitle = $this->separateTheme($item[0], $key);
            if ($this->_isHeadTheme === $key) {
                $themeList[][0] = $themeTitle;
            } elseif ($this->_isHeadTheme !== 'other') {
                $themeList[(count($themeList) - 1)][1][] = $themeTitle;
            } else {
                $other[] = $themeTitle;
            }
        }
        $themeList['other'] = $other;
        return $themeList;
    }

    private function separateTheme($themeItem, $key)
    {
        $themeTitle = $themeItem;
        $arr = explode(' ', $themeItem, 2);
        if (is_numeric($arr[0])) {
            if ((int)$arr[0] !== 0) {
                $this->_isHeadTheme = $key;
            } else {
                $this->_isHeadTheme = 'other';
            }
            $themeTitle = $arr[1];
        }
        return $themeTitle;
    }
}
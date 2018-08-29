<?php

namespace App\Themes;

class ThemeList
{
    private $db;
    private $_srcThemeList;
    private $_isHeadTheme = null;
    private $themesListArray;

    public function __construct($category = 'ab')
    {
        $this->db = new DB();
        $category = ($category === 'cd') ? 'cd' : 'ab';
        $this->_srcThemeList = file_get_contents(__DIR__ . '/files/theme_list_' . $category . '.txt');

    }

    public function getList()
    {
        $line = $this->cleaningString($this->_srcThemeList);
        $line = json_decode($line);
        $this->themesListArray = $this->processor($line);
        $this->db->setDBThemes($this->themesListArray);
//        $this->saveToDBParentThemes();
//        $this->saveToDBChildrenThemes($this->db->getParentThemes());
        return $this->themesListArray;
    }

    private function cleaningString($string)
    {
        return str_replace("gwt_pdd_client_BiletVo_", "", $string);
    }

    private function processor($list)
    {
        //Создаем новую таблицу в локальной базе данных
        $this->db->createNewDB();

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
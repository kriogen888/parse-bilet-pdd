<?php

namespace App;

class ThemeList
{
    private $_srcThemeList;

    private $_isHeadTheme = null;

    public function __construct($category = 'ab')
    {
        $category = ($category === 'cd') ? 'cd' : 'ab';
        $this->_srcThemeList = file_get_contents(__DIR__ . '/../files/theme_list_' . $category . '.txt');

    }

    public function getList()
    {
        $line = $this->cleaningString($this->_srcThemeList);
        $line = json_decode($line);
        $line = $this->processor($line);
        return $line;
    }

    private function cleaningString($string)
    {
        return str_replace("gwt_pdd_client_BiletVo_", "", $string);
    }

    private function processor($list)
    {
        $themeList = [];

        foreach ($list as $key => $item) {
            $themeTitle = $this->separateTheme($item[0], $key);
            if ($this->_isHeadTheme === null || $this->_isHeadTheme === $key) {
                $themeList[][0] = $themeTitle;
            } else {
                $themeList[(count($themeList) - 1)][1][] = $themeTitle;
            }
        }
        return $themeList;
    }

    private function separateTheme($list, $key)
    {
        $themeTitle = $list;
        $arr = explode(' ', $list, 2);
        if (is_numeric($arr[0])) {
            $this->_isHeadTheme = $key;
            $themeTitle = $arr[1];
        }
        return $themeTitle;
    }
}
<?php

namespace App;

class ThemeList
{
    public static function getList($category = 'ab')
    {
        $category = ($category === 'cd') ? 'cd' : 'ab';
        return json_decode(file_get_contents(__DIR__ . '/../files/theme_list_' . $category . '.txt'));
    }
}
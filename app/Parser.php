<?php

namespace App;


use DirectoryIterator;
use SplFileObject;

class Parser
{
    private $billet_arr = [];

    /**
     * Get the version number of the application.
     * @return string
     */
    public function version()
    {
        return Config::VERSION;
    }

    public function run()
    {

        //Извлекаем из директории файлы
        foreach ($this->getFile(Config::TEXT_DIR) as $item) {

            //Разбиваем файл на строки
            $filename = $item->getBasename();

            $bilet = (int)substr($filename, 0, 2);
            $vopros = (int)substr($filename, 2, 2);
            $key = ($bilet - 1) * 20 + $vopros;

            $this->billet_arr[$key] = [
                'bilet' => $bilet,
                'vopros' => $vopros,
                'correct_answer' => (int)$item->current(),
            ];
            $item->next();
            $this->billet_arr[$key]['questions'] = $this->clearLine($item->current());
            $item->next();
            $this->billet_arr[$key]['answers'] = '';

            while ($item->valid()) {
                if ($this->clearLine($item->current()) == '*') break;
                $this->billet_arr[$key]['answers'] = $this->billet_arr[$key]['answers'] . $this->clearLine($item->current()) . "|";
                $item->next();
            }
            $item->next();
            //Удаляем номер билета из начала комментария
            $this->billet_arr[$key]['comment'] = $this->clearLine(substr($item->current(), 1 + strpos($item->current(), ' ')));
        }
        ksort($this->billet_arr);
        return $this->billet_arr;

    }

    /**
     * Generator. Extract the files from the directory
     *
     * @param $dir
     * @return \Generator
     */
    public function getFile($dir)
    {
        $dir_iterator = new DirectoryIterator($dir);
        foreach ($dir_iterator as $item) {
            //Проверяем, что это файл, а не точка
            if (!$dir_iterator->isFile()) continue;
            yield new SplFileObject($dir . $item);
        }
    }

    /**
     * Clears the line from the transfer of symbols and encodes
     *
     * @param $line
     * @return string
     */
    public function clearLine($line)
    {
        return iconv(Config::IN_CHARSET, Config::OUT_CHARSET, preg_replace('/\\r\\n?|\\n/', '', $line));
    }
}
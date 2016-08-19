<?php

namespace App;


use App\Config\Config;
use App\Config\DBConfig;
use DirectoryIterator;
use PDO;
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

    /**
     * Method run()
     * @return array
     */
    public function run()
    {
        return $this->fileToArray();
    }

    /**
     * Parse the file line by line and fill the array $billet_arr
     * @return array
     */
    private function fileToArray(): array
    {
        //Извлекаем из директории файлы
        foreach ($this->getFile(Config::TEXT_DIR) as $file) {

            //Разбиваем файл на строки
            $filename = $file->getBasename();

            $bilet_str = substr($filename, 0, 2);
            $vopros_str = substr($filename, 2, 2);
            $bilet = (int)$bilet_str;
            $vopros = (int)$vopros_str;
            $key = ($bilet - 1) * 20 + $vopros;
            //Заполняем массив $this->billet_arr
            $this->billet_arr[$key] = [
                'bilet' => $bilet,
                'vopros' => $vopros,
                'correct_answer' => (int)$file->current(),
            ];
            $file->next();
            $this->billet_arr[$key]['questions'] = $this->clearLine($file->current());
            $file->next();
            $this->billet_arr[$key]['answers'] = '';

            while ($file->valid()) {
                if ($this->clearLine($file->current()) == '*') break;
                $this->billet_arr[$key]['answers'] = $this->billet_arr[$key]['answers'] . $this->clearLine($file->current()) . "|";
                $file->next();
            }
            $file->next();
            //Удаляем номер билета из начала комментария если он там есть
            $flag_g = (((string)($this->billet_arr[$key]['vopros']) . ". ") == substr($file->current(), 0, 1 + strpos($file->current(), ' '))) ? TRUE : FALSE;
            $this->billet_arr[$key]['comment'] = $this->clearLine($file->current(), $flag_g);
            //Формируем имя файла картинки Pdd_01_02.jpg
            $file_img = "Pdd_" . $bilet_str . "_" . $vopros_str . ".jpg";
            $this->billet_arr[$key]['images'] = file_exists(Config::IMG_DIR . $file_img) ? $file_img : 'text.gif';
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
    private function getFile($dir)
    {
        $dir_iterator = new DirectoryIterator($dir);
        foreach ($dir_iterator as $item) {
            //Проверяем, что это файл, а не точка
            if (!$dir_iterator->isFile()) continue;
            yield new SplFileObject($dir . $item);
        }
    }

    /**
     * Clears the line from the garbage, the transfer of symbols and encodes
     *
     * @param $line
     * @param bool $comment
     * @return string
     */
    private function clearLine($line, $comment = FALSE)
    {
        if ($comment) {
            $line = substr($line, 1 + strpos($line, ' '));
        }
        return iconv(Config::IN_CHARSET, Config::OUT_CHARSET, preg_replace('/\\r\\n?|\\n/', '', $line));
    }

    public function DB()
    {
        foreach (PDO::getAvailableDrivers() as $driver) {
            echo $driver . "<br>";
        }
        $db = new PDO('mysql:host=' . DBConfig::HOST_REMOTE_DB . ';dbname=' . DBConfig::LOGIN_REMOTE_DB, DBConfig::LOGIN_REMOTE_DB, DBConfig::PASSWORD_REMOTE_DB);
        var_dump($db);
    }
}
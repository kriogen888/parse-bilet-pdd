<?php

namespace App;


use App\Config\Config;
use App\Config\DBConfig;
use DirectoryIterator;
use PDO;
use SplFileObject;

class Parser
{
    /**
     * Connect to remote DB
     * @var
     */

    private $remote_db;
    /**
     * Connect to local DB
     * @var
     */

    private $local_db;
    /**
     * Array of questions
     * @var array
     */

    private $billet_arr = [];

    /**
     * Array of old DB question
     * @var array
     */

    private $old_billet_arr = [];

    /**
     * Parser constructor.
     */
    public function __construct()
    {
        $this->remote_db = new PDO('mysql:host=' . DBConfig::HOST_REMOTE_DB . ';dbname=' . DBConfig::NAME_REMOTE_DB, DBConfig::LOGIN_REMOTE_DB, DBConfig::PASSWORD_REMOTE_DB);
        $this->local_db = new PDO('mysql:host=' . DBConfig::HOST_LOCAL_DB . ';dbname=' . DBConfig::NAME_LOCAL_DB, DBConfig::LOGIN_LOCAL_DB, DBConfig::PASSWORD_LOCAL_DB);
    }

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
     *
     */
    public function run()
    {
        //Заносим в свойства массив из удаленной (старые билеты) базы данных
        $this->getRemoteDB();
//        var_dump($this->old_billet_arr);
        //Заносим в свойства массив из исходных файлов (новые билеты)
        $this->fileToArray();
//        var_dump($this->billet_arr);

        $new_bilet_array = $this->mergeArray();
    }


    /**
     * Parse the file line by line and fill the array $billet_arr
     */
    private function fileToArray()
    {
        //Извлекаем из директории файлы
        foreach ($this->getFile(Config::TEXT_DIR) as $file) {

            //Разбиваем файл на строки
            $filename = $file->getBasename();

            $bilet_str = substr($filename, 0, 2);
            $vopros_str = substr($filename, 2, 2);
            $bilet = (int)$bilet_str;
            $vopros = (int)$vopros_str;
            $key = (($bilet - 1) * 20 + $vopros) - 1;
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
        return;
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


    /**
     * Fills an array of remote database
     */
    private function getRemoteDB()
    {
        $table_name = DBConfig::NAME_OLD_DB_BILLET;
        $sql = "SELECT id, bilet, vopros, tema, tema_dop FROM {$table_name} ORDER BY id";
        $sth = $this->remote_db->query($sql);
        $this->old_billet_arr = $sth->fetchAll(PDO::FETCH_ASSOC);

        return;
    }

    /**
     * Merge old_bilet and new_bilet arrays
     * @return array
     */
    private function mergeArray():array
    {
        $result_array = [];
        $i = 0;
        while ($i < 800) {
            if ($this->billet_arr[$i]['bilet'] == $this->old_billet_arr[$i]['bilet'] && $this->billet_arr[$i]['vopros'] == $this->old_billet_arr[$i]['vopros']) {
                $result_array[$i] = array_merge($this->old_billet_arr[$i], $this->billet_arr[$i]);
            }
            var_dump($result_array[$i]);
            $i++;
        }

        return $result_array;

    }

    private function setDBNewBilet()
    {
        $table_name = DBConfig::NAME_NEW_DB_BILLET;
        $stmt = $this->local_db->prepare("INSERT INTO {$table_name} (bilet,vopros,tema,images,questions,answers,correct_answer,comment,tema_dop) VALUES (:bilet,:vopros,:tema,:images,:questions,:answers,:correct_answer,:comment,:tema_dop)");
        $stmt->bindParam(':bilet', $bilet);
        $stmt->bindParam(':vopros', $vopros);
        $stmt->bindParam(':tema', $tema);
        $stmt->bindParam(':images', $images);
        $stmt->bindParam(':questions', $questions);
        $stmt->bindParam(':answers', $answers);
        $stmt->bindParam(':correct_answer', $correct_answer);
        $stmt->bindParam(':comment', $comment);
        $stmt->bindParam(':tema_dop', $tema_dop);

    }
}
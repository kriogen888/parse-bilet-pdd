<?php


namespace App\Themes;


class DB
{
    private $config;
//    private $pdo;
    private $localDBConfig = [
        'host' => '192.168.1.4',
        'dbname' => 'themes',
        'user' => 'igory',
        'password' => 'ghbvec785',
        'charset' => 'utf8',
    ];
    private $PDOObjectLocal;
    private $themes_table_name = 'themes';

    public function __construct()
    {
        $this->config = (object)$this->localDBConfig;
        try {
            $this->PDOObjectLocal = $this->setPDOObject($this->config);
        } catch (\PDOException $e) {
            echo "{$e->getMessage()}, {$e->getCode()}";
        }
    }

    protected function setPDOObject($config)
    {
        $dsn = 'mysql:host=' . $config->host . ';dbname=' . $config->dbname;
        $options = [];
        if (!empty($config->options)) {
            $options = $config->options->toArray();
        }
        $pdo = new \PDO($dsn, $config->user, $config->password, $options);
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $pdo->exec('set names ' . $config->charset);
        $pdo->exec("set sql_mode = ''");
        return $pdo;
    }

    public function getPDOObject()
    {
        return $this->PDOObjectLocal;
    }

    /**
     * Create new table in DB
     * @return string
     */
    public function createNewDB()
    {
        $table_name = $this->nonExistsTableName($this->themes_table_name);

        $sql = "
          CREATE TABLE $table_name (
            __id INT(11) NOT NULL AUTO_INCREMENT,
            _pid INT(11) NOT NULL,
            title TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
            PRIMARY KEY (__id)
          );
        ";

        $this->PDOObjectLocal->exec($sql);
        echo "Создана таблица $table_name";
        return $table_name;
    }

    /**
     * Searching for a new non-existing table name
     * @param $table_name
     * @return string
     */
    private function nonExistsTableName($table_name)
    {
        $name_prefix = 1;
        while ($this->tableExists($table_name)) {
            $table_name = $this->themes_table_name . "_" . $name_prefix;
            $name_prefix++;
        }
        $this->themes_table_name = $table_name;
        return $table_name;
    }

    /**
     * Check exists of the table
     * @param $table_name
     * @return bool
     */
    private function tableExists($table_name): bool
    {
        $res = $this->PDOObjectLocal->query("SHOW TABLES LIKE '$table_name'");
        return (boolean)($res->rowCount());
    }

    /**
     * Fill a database table
     * @param $themes_list_array
     */
    public function setDBThemes($themes_list_array)
    {
        $stmt = $this->PDOObjectLocal->prepare("INSERT INTO {$this->themes_table_name} (_pid,title) VALUES (:_pid,:title)");
        $stmt->bindParam(':_pid', $_pid);
        $stmt->bindParam(':title', $title);

        foreach ($themes_list_array as $key => $theme) {
            if (is_numeric($key)) {
                $_pid = 0;
                $title = $theme[0];
                if (!$stmt->execute()) die('Error! Insert to DB');

                if (isset($theme[1])) $this->saveChildrenThemes($theme[1], $this->PDOObjectLocal->lastInsertId());
            }
        }
    }

    private function saveChildrenThemes($childrenThemes, $pid)
    {
        $stmt = $this->PDOObjectLocal->prepare("INSERT INTO {$this->themes_table_name} (_pid,title) VALUES (:_pid,:title)");
        $stmt->bindParam(':_pid', $_pid);
        $stmt->bindParam(':title', $title);

        foreach ($childrenThemes as $item) {
//            dd($item, '$item', 2);
//            dd($pid, '$pid', 2);

            $_pid = $pid;
            $title = $item;

            if (!$stmt->execute()) die('Error! Insert to DB');
        }
    }
}
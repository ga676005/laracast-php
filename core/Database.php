<?php 

namespace Core;

// 開頭的 \ 代表用 global namespace 找，也只在這邊找
use \PDO;

// 如果寫 use PDO; 就會先在目前的 namespace (例如上面的 Core) 找，找不到才用 global namespace 找


class Database {
    public $connection;

    public function __construct($config, $username = 'root', $password = '') {
        $dns = 'mysql:' . http_build_query($config, '', ';'); // host=localhost;port=3306;dbname=laracast-php;charset=utf8mb4
        $this->connection = new PDO($dns, $username, $password, [
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
    }

    public function query($query, $params = []) {
        $statement = $this->connection->prepare($query);
        $statement->execute($params);
        return $statement;
    }

    public function lastInsertId() {
        return $this->connection->lastInsertId();
    }
}
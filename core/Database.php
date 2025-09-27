<?php 

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
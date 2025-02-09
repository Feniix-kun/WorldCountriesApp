<?php

namespace App\Rdb;

use mysqli;
use RuntimeException;

class SqlHelper{
    public function __construct()
    {
        $this->pingDb();
    }

    public function openDbConnection(): mysqli{
        $host = $_ENV['DB_HOST'];
        $port = $_ENV['DB_PORT'];
        $user = $_ENV['DB_USERNAME'];
        $password = $_ENV['DB_PASSWORD'];
        $database = $_ENV['DB_NAME'];

        $connection = new mysqli(
            hostname: $host,
            port: $port, 
            username: $user, 
            password: $password, 
            database: $database, 
        );
        if ($connection->connect_errno) {
            throw new RuntimeException(message: "Failed to connect to MySQL: ".$connection->connect_error);
        }
        return $connection;
    }
    private function pingDb() : void {
        $connection = $this->openDbConnection();
        $connection->close();
    }
}
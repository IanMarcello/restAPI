<?php

class Database
{
    public function __construct(
        private string $host,
        private string $name,
        private string $user,
        private string $password
    ) {
    }

    public function getConnection(): PDO
    {
        $dbconn = "mysql:host = {$this->host};dbname={$this->name};charset=utf8";

        return new PDO($dbconn, $this->user, $this->password, [
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_STRINGIFY_FETCHES => false
        ]);
    }
}

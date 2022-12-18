<?php

Class Database 
{
    public function __construct(private string $host, private string $name, private string $user, private string $password) {

    }

    public function getConnection() {
        $dsn = "mysql:host={$this->host};dbname={$this->name};charset=utf8";

        try {
            $pdo = new PDO ($dsn, $this->user, $this->password);
        } catch (Exception $e) {
            $pdo = null;
        }
        
        return $pdo;
    }
}
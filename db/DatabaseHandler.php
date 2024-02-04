<?php

ini_set('display_errors', 1);

class DatabaseHandler
{
    public $dbConnection;
    private $host;
    public $dbName;
    private $username;
    private $password;
    protected $isMigration;

    public function __construct($isMigration = false)
    {
        $this->isMigration = $isMigration;
        $this->setConfig();
        $this->connect();
    }

    private function setConfig()
    {
        $this->host = 'localhost';
        $this->dbName = 'rexx';
        $this->username = 'root';
        $this->password = '';
    }

    public function connect()
    {
        try {
            $this->createPdoConnection();

            if (!$this->isMigration){
                $this->connectToDatabae();
            }
        }
        catch (PDOException $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function createPdoConnection()
    {
        try {
            $this->dbConnection = new PDO("mysql:host=$this->host;", $this->username, $this->password, [PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true]);
            $this->dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        catch (PDOException $e) {

           die('Please config database connection and create database.');
        }
    }

    public function executeQuery(string $query): PDOStatement
    {
        return $this->dbConnection->query($query);
    }

    public function databaseExists(): bool
    {
        $query = "SELECT SCHEMA_NAME
            FROM information_schema.SCHEMATA
            WHERE SCHEMA_NAME = '$this->dbName'
        ";

        $result = $this->executeQuery($query);
        $rowCount = $result->rowCount();

        return $rowCount > 0;
    }

    public function connectToDatabae(): void
    {
        $query = "USE $this->dbName";

        $this->executeQuery($query);
    }
}

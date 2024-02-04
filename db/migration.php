<?php

ini_set('display_errors', 1);

require_once('DatabaseHandler.php');

(new Migration())->migrate();

class Migration
{
    protected $databaseHandler;

    public function __construct()
    {
        $this->databaseHandler = new DatabaseHandler(true);
    }

    public function migrate(): void
    {
        if (!$this->databaseHandler->databaseExists()) {
            $this->createDatabase();

            $this->databaseHandler->connectToDatabae();

            $this->createTables();
        }

        echo "Migration done.";
    }

    protected function createDatabase(): void
    {
        $dbName = $this->databaseHandler->dbName;
        $query = "CREATE DATABASE $dbName";

        $this->databaseHandler->executeQuery($query);
    }

    protected function createTables(): void
    {
        $employeesTableQuery = "CREATE TABLE IF NOT EXISTS employees (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL UNIQUE
    );";

        $eventsTableQuery = "CREATE TABLE IF NOT EXISTS events (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        fee DECIMAL(10, 2) NOT NULL,
        date DATETIME NOT NULL
    );";

        $participantsTableQuery = "CREATE TABLE IF NOT EXISTS participants (
        id INT AUTO_INCREMENT PRIMARY KEY,
        event_id INT NOT NULL,
        employee_id INT NOT NULL,
        FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
        FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE
    );";

        // Execute the queries to create the tables
        $this->databaseHandler->executeQuery($employeesTableQuery);
        $this->databaseHandler->executeQuery($eventsTableQuery);
        $this->databaseHandler->executeQuery($participantsTableQuery);

        echo "Employees, Events, and Participants tables created successfully.\n";
    }

}

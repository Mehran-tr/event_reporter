<?php

ini_set('display_errors', 1);

require_once('DatabaseHandler.php');

class Repository
{
    protected $databaseHandler;

    public function __construct()
    {
        $this->databaseHandler = new DatabaseHandler();
    }

    public function getEmployees(): IteratorAggregate
    {
        $query = "SELECT * FROM employees";
        return $this->databaseHandler->executeQuery($query);
    }

    public function getEvents(): IteratorAggregate
    {
        $query = "SELECT * FROM events";
        return $this->databaseHandler->executeQuery($query);
    }


    public function getParticipantsByTimePeriodAndFilters( $fromDate,  $toDate,  $eventId,  $employeeId): array
    {
        if (empty($fromDate) || empty($toDate)) {
            throw new InvalidArgumentException("Both 'fromDate' and 'toDate' must be provided.");
        }

        if (empty($eventId) && empty($employeeId)) {
            throw new InvalidArgumentException("Either 'eventId' or 'employeeId' must be provided.");
        }

        $query = "SELECT participants.*, employees.name AS employee_name, events.name AS event_name,
             events.fee AS event_fee, events.date AS event_date,
             (SELECT SUM(fee) FROM events WHERE date BETWEEN :from_date AND :to_date) AS total_fees
             FROM participants
             INNER JOIN employees ON participants.employee_id = employees.id
             INNER JOIN events ON participants.event_id = events.id
             WHERE events.date BETWEEN :from_date AND :to_date";

        if ($eventId > 0) {
            $query .= " AND events.id = :event_id";
        }
        if ($employeeId > 0) {
            $query .= " AND employees.id = :employee_id";
        }

        $statement = $this->databaseHandler->dbConnection->prepare($query);
        $params = [
            'from_date' => $fromDate,
            'to_date' => $toDate,
        ];

        if ($eventId > 0) {
            $params['event_id'] = $eventId;
        }
        if ($employeeId > 0) {
            $params['employee_id'] = $employeeId;
        }

        $statement->execute($params);
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }



    public function getEmployeeIdByEmail(string $email): int|bool
    {
        $statement = $this->databaseHandler->dbConnection->prepare("SELECT id FROM employees WHERE email = ?");
        $statement->execute([$email]);
        return $statement->fetchColumn();
    }

    public function createEmployee(string $name, string $email): int
    {
        $statement = $this->databaseHandler->dbConnection->prepare("INSERT INTO employees (name, email) VALUES (?, ?)");
        $statement->execute([$name, $email]);
        return $this->databaseHandler->dbConnection->lastInsertId();
    }

    public function getEventIdByName(string $name): int|bool
    {
        $statement = $this->databaseHandler->dbConnection->prepare("SELECT id FROM events WHERE name = ?");
        $statement->execute([$name]);
        return $statement->fetchColumn();
    }

    public function createEvent(string $name, float $fee, string $date): int
    {
        $statement = $this->databaseHandler->dbConnection->prepare("INSERT INTO events (name, fee, date) VALUES (?, ?, ?)");
        $statement->execute([$name, $fee, $date]);
        return $this->databaseHandler->dbConnection->lastInsertId();
    }

    public function createParticipant(int $employeeId, int $eventId): void
    {
        $statement = $this->databaseHandler->dbConnection->prepare("INSERT INTO participants (employee_id, event_id) VALUES (?, ?)");
        $statement->execute([$employeeId, $eventId]);
    }
}

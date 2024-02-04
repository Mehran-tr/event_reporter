<?php

ini_set('display_errors', 1);

require_once('../db/DatabaseHandler.php');
require_once('../db/Repository.php');

(new EventsImporter())->import();

class EventsImporter
{
    protected $databaseHandler;
    protected $repository;

    public function __construct()
    {
        $this->databaseHandler = new DatabaseHandler();
        $this->repository = new Repository();
    }

    public function import(): void
    {
        $data = $this->getJsonData();

        $this->saveData($data);
    }

    protected function getJsonData(): array
    {
        $jsonData = file_get_contents('data.json'); // Adjust the path as necessary

        return json_decode($jsonData, true);
    }

    protected function saveData(array $data): void
    {
        foreach ($data as $entry) {
            $employeeId = $this->repository->getEmployeeIdByEmail($entry['employee_mail']);
            if (!$employeeId) {
                $employeeId = $this->repository->createEmployee($entry['employee_name'], $entry['employee_mail']);
            }

            $eventId = $this->repository->getEventIdByName($entry['event_name']);
            if (!$eventId) {
                $eventId = $this->repository->createEvent($entry['event_name'], $entry['participation_fee'], $entry['event_date']);
            }

            $this->repository->createParticipant($employeeId, $eventId);
        }

        echo "Import done.";
    }
}

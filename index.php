<?php

ini_set('display_errors', 1);

require_once('db/DatabaseHandler.php');
require_once('db/Repository.php');

$databaseHandler = new DatabaseHandler();
$repository = new Repository();

$employees = $repository->getEmployees();

$events = $repository->getEvents();

$selectedEmployee = $_POST['employee'] ?? '';
$selectedEvent = $_POST['event'] ?? '';
$enteredFromDate = $_POST['from_date'] ?? '';
$enteredToDate = $_POST['to_date'] ?? '';

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $filteredParticipants = $repository->getParticipantsByTimePeriodAndFilters($enteredFromDate, $enteredToDate,$selectedEvent,$selectedEmployee);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Reporter</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            padding: 20px;
        }

        .header {
            margin-bottom: 30px;
        }

        .filter-form {
            margin-bottom: 20px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h1>Event Reporter</h1>
    </div>

    <form method="POST" action="" class="filter-form">
        <div class="form-group">
            <label for="employee">Employee:</label>
            <select id="employee" name="employee" class="form-control" required>
                <option value="">-- Select Employee --</option>
                <?php foreach ($employees as $employee): ?>
                    <option value="<?= htmlspecialchars($employee['id']); ?>"
                        <?= $selectedEmployee == $employee['id'] ? 'selected' : ''; ?>>
                        <?= htmlspecialchars($employee['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="event">Event:</label>
            <select id="event" name="event" class="form-control" required>
                <option value="">-- Select Event --</option>
                <?php foreach ($events as $event): ?>
                    <option value="<?= htmlspecialchars($event['id']); ?>"
                        <?= $selectedEvent == $event['id'] ? 'selected' : ''; ?>>
                        <?= htmlspecialchars($event['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="from_date">From Date:</label>
            <input type="date" id="from_date" name="from_date" value="<?php echo $enteredFromDate; ?>"
                   class="form-control" required>
        </div>

        <div class="form-group">
            <label for="to_date">To Date:</label>
            <input type="date" id="to_date" name="to_date" value="<?php echo $enteredToDate; ?>"
                   class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary">Filter</button>
    </form>

    <?php if (isset($filteredParticipants)): ?>
        <h2>Filtered Results:</h2>
        <table class="table">
            <thead>
            <tr>
                <th>Event ID</th>
                <th>Employee</th>
                <th>Event</th>
                <th>Fee</th>
                <th>Event Date</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($filteredParticipants as $sale): ?>
                <tr>
                    <td><?= htmlspecialchars($sale['id']); ?></td>
                    <td><?= htmlspecialchars($sale['employee_name']); ?></td>
                    <td><?= htmlspecialchars($sale['event_name']); ?></td>
                    <td><?= htmlspecialchars($sale['event_fee']); ?></td>
                    <td><?= htmlspecialchars($sale['event_date']); ?></td>
                </tr>
            <?php endforeach; ?>
            <tr class="total-row">
                <td colspan="3"><b>Total Fees</b></td>
                <td colspan="2"><?= array_sum(array_column($filteredParticipants, 'event_fee')); ?></td>
            </tr>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

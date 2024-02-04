# Event Reporter

A simple application written with pure PHP and MySQL.

## Prerequisites

- PHP 7 or higher
- MySQL/MariaDB
- An available localhost

## Scenario

Imagine an external event booking system exports a simple plain json export file of the newest bookings.
This data should be presented in our system.

## What needs to be done

Each scenario consists of:<br/>
- Design a database scheme for optimized storage.<br/>
- Please note that over time, large amounts of data will accumulate.<br/>
- Read the json data and save it to the database using php.<br/>
-  Create a simple page with filters for the employee name, event name and date.<br/>
- Output the filtered results in a table below the filters.<br/>
- Add a last row for the total price of all filtered entries.<br/>
- KISS - Keep it simple stupid!.<br/>

## Run

1. Clone the project in your localhost directory

    ```sh
    git clone https://github.com/Mehran-tr/event_reporter
    ```

2. Run the migration with below URL (For creating the DB and the needed tables).
    ```sh
    http://localhost/db/migration.php
    ```

3. Run the sales importer with below URL to import the data from the sales.json file in import folder.
    ```sh
    http://localhost/import/EventImporter.php
    ```

4. Open the report page and filter the sales.
    ```sh
    http://localhost/
    ```

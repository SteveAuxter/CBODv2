<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="mystyle.css">
    <title>Serial Number: Remote Powerwash</title>
</head>
<body>
    <?php include "serial_header.php" ?>
    <?php include "variables.php" ?>

    <ul class="menu">
        <li><a href="serial_main.php">Device Info</a></li>
        <li><a href="serial_wipeusers.php">Clear Profiles</a></li>
        <li><a class="active" href="serial_powerwash.php">Remote Powerwash</a></li>
        <li><a href="serial_disable.php">Disable/Enable</a></li>
        <li><a href="serial_telemetry.php">Telemetry Data</a></li>
        <li><a href="serial_help.php">Help</a></li>
    </ul>
    <hr>

    <form name="search" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="GET">
        Search: <input type="text" name="search_term">
        <input type="submit" value="Remote Powerwash">
    </form>
    <br><br>

    <?php try {
        // Check if the database file exists
        if (!file_exists($DBname)) {
            throw new Exception(
                "The database file named $DBname does not exist. Please go Utilities > Gather Data to upload your CSV and create the database."
            );
        }

        // Open the SQLite database
        $db = new SQLite3($DBname);

        // Check if the 'devices' table exists
        $table_check = $db->querySingle("SELECT name FROM sqlite_master WHERE type='table' AND name='devices';");
        if (!$table_check) {
            throw new Exception(
                "The table named 'devices' does not exist. Please go Utilities > Gather Data to upload (or re-upload) your CSV file."
            );
        }

        // If the search term is provided
        if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['search_term'])) {
            $search_term = trim($_GET['search_term']); // Trim the search term to avoid unnecessary spaces

            // Check if the search term is empty
            if (empty($search_term)) {
                echo "<div class='warning-message'>Please enter a search term.</div>";
            } else {
                $starttime = microtime(true);

                // Prepare the SQL query to search in multiple columns
                $stmt = $db->prepare("
                SELECT * FROM devices WHERE serialNumber LIKE :search_term
                ");
                $stmt->bindValue(':search_term', '%' . $search_term . '%', SQLITE3_TEXT);
                $result = $stmt->execute();
                $counter = 0;

                // Loop through the results to count how many are returned
                $results = [];
                while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
                    $results[] = $row;
                    $counter++;
                }

                if ($counter === 0) {
                    // No results found
                    echo "<div class='warning-message'>No results found for the search term '$search_term'.</div>";
                } elseif ($counter > 1) {
                    // Multiple results found
                    echo "<div class='warning-message'>Multiple results found for '$search_term'. The functionality of this page requires a unique result.</div>";
                } elseif ($counter === 1) {
                    // Exactly one result found
                    echo "<center>";
                    echo "<table class='counting'>";
                    echo "<tr>";
                    echo "<th><b>Asset ID</b></th>";
                    echo "<th><b>Serial #</b></th>";
                    echo "<th><b>Notes</b></th>";
                    echo "<th><b>Model</b></th>";
                    echo "<th><b>Status</b></th>";
                    echo "<th><b>Location</b></th>";
                    echo "<th><b>User</b></th>";
                    echo "<th><b>OS Version</b></th>";
                    echo "<th><b>Device ID</b></th>";
                    echo "</tr>";

                    foreach ($results as $row) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['annotatedAssetId']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['serialNumber']) . "</td>";
                        echo "<td>" . nl2br(htmlspecialchars(str_replace(['\\\\n', '\\n'], "\n", $row['notes']))) . "</td>";
                        echo "<td>" . htmlspecialchars($row['model']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['status']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['annotatedLocation']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['annotatedUser']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['osVersion']) . "</td>";
                        echo "<td><a href='https://admin.google.com/ac/chrome/devices/" . htmlspecialchars($row['deviceId']) . "' target='_blank'>" . $row['deviceId'] . "</a></td>";
                        echo "</tr>";
                    }
                    echo "</table>";
                    echo "</center>";

                    $endtime = microtime(true);
                    $duration = $endtime - $starttime;
                    echo "<br><center>Database query took " . number_format((float)$duration, 4) . " seconds.</center>";
                    echo "<hr>";
                    echo "<h3>Performing a remote powerwash on device with Serial Number <font color='#008CBA'>$search_term</font>. Here's what happened:</h3>";

                    // GAM Script: Proceed to query additional information from Google
                    $command1 = sprintf("$GAMpath issuecommand cros query id:%s command remote_powerwash doit", $search_term);
                    exec($command1, $infoBasic);

                    // Process and display the GAM data
                    foreach ($infoBasic as $data) {
                        echo $data;
                        echo "<br>";
                    }
                    echo "<br>";
                }
                // Close the SQLite connection
                $db->close();
            }
        }

    } catch (Exception $e) {
        // Display an error message if the database or table does not exist
        echo "<div class='danger-message'>DATABASE ERROR: " . $e->getMessage() . "</div>";
    }
    ?>

    <?php include "footer.php" ?>
</body>
</html>

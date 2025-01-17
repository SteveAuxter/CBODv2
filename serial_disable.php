<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="mystyle.css">
    <title>Serial Number: Disable/Reenable</title>
</head>
<body>
    <?php include "serial_header.php" ?>
    <?php include "variables.php" ?>

    <ul class="menu">
        <li><a href="serial_main.php">Device Info</a></li>
        <li><a href="serial_wipeusers.php">Clear Profiles</a></li>
        <li><a href="serial_powerwash.php">Remote Powerwash</a></li>
        <li><a class="active" href="serial_disable.php">Disable/Enable</a></li>
        <li><a href="serial_telemetry.php">Telemetry Data</a></li>
        <li><a href="serial_help.php">Help</a></li>
    </ul>
    <hr>

    <form name="search" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="GET">
        Search: <input type="text" name="search_term">
        <input type="submit" value="Disable or Reenable">
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
        // Check if the table exists
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
                        echo "<td><a href='https://admin.google.com/ac/chrome/devices/" . htmlspecialchars($row['deviceId']) . "' target='_blank'>" . htmlspecialchars($row['deviceId']) . "</a></td>";
                        echo "</tr>";
                    }
                    echo "</table>";
                    echo "</center>";

                    $endtime = microtime(true);
                    $duration = $endtime - $starttime;
                    echo "<br><center>Database query took " . number_format((float)$duration, 4) . " seconds.</center>";
                    echo "<hr>";

                    // GAM Script: Proceed to query additional information from Google
                    $command1 = sprintf("$GAMpath info cros query id:%s status", $search_term);
                    exec($command1, $infoStatus);
                    $gamStatus = trim(substr($infoStatus[1],10));

                    if ($gamStatus == 'ACTIVE') {
                        $command2 = sprintf("$GAMpath update cros query \"id:%s status:ACTIVE\" action disable", $search_term);
                        $sqlCommand = "UPDATE devices SET status = 'DISABLED' WHERE serialNumber LIKE :search_term";
                        echo "<h3>Current status is ACTIVE - sending DISABLE action to Serial Number <font color='#008CBA'>$search_term</font>. Here's what happened:</h3>";
                    } elseif ($gamStatus == 'DISABLED') {
                        $command2 = sprintf("$GAMpath update cros query \"id:%s status:DISABLED\" action reenable", $search_term);
                        $sqlCommand = "UPDATE devices SET status = 'ACTIVE' WHERE serialNumber LIKE :search_term";
                        echo "<h3>Current status is DISABLED - sending REENABLE action to Serial Number <font color='#008CBA'>$search_term</font>. Here's what happened:</h3>";
                    } else {
                        echo "The device status is neither ACTIVE nor DISABLED.<br>";
                    }

                    exec($command2,$infoBasic);

                    // Process and display the GAM data
                    foreach ($infoBasic as $data) {
                        echo $data;
                        echo "<br>";
                    }
                    echo "<br>";
                }
                $db->close();
                $db = new PDO("sqlite:$DBname");
                $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $stmt = $db->prepare($sqlCommand);
                $stmt->bindValue(':search_term', "%$search_term%", PDO::PARAM_STR);
                $stmt->execute();
                $db = null;
                echo "Updated device status in the local database to match device status in Google.<br>";
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

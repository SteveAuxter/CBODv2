<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="mystyle.css">
    <title>Counts: OS Version</title>
</head>
<body>
    <?php include "counts_header.php" ?>
    <?php include "variables.php" ?>

    <ul class="menu">
        <li><a href="counts_main.php">What do I do?</a></li>
        <li><a class="active" href="counts_osver.php">OS Version</a></li>
        <li><a href="counts_orgunit.php">Org Units</a></li>
        <li><a href="counts_model.php">Models</a></li>
        <li><a href="counts_location.php">Location</a></li>
        <li><a href="counts_auedates.php">AUE Dates</a></li>
    </ul>
    <hr>

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

        $starttime = microtime(true);
        // SQL query to retrieve OS VERSION and count distinct devices
        $sql = "SELECT osVersion, COUNT(DISTINCT deviceId) AS device_count FROM devices GROUP BY osVersion ORDER BY ROUND(osVersion,0) DESC";
        $result = $db->query($sql);

        // Build a table for query results
        echo "<center>";
        echo "<table class='counting'>";
        echo "<tr>";
        echo "<th><b>OS Version</b></th>";
        echo "<th><b># of Devices</b></th>";
        echo "</tr>";

        // Start a tally, fetch all results and display them
        $tally = 0;
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['osVersion']) . "</td>";
            echo "<td>" . htmlspecialchars($row['device_count']) . "</td>";
            echo "</tr>";
            $tally += $row["device_count"];
        }

        // Grand totals and close the table
        echo "<tr>";
        echo "<td><b>TOTAL COUNT</b></td>";
        echo "<td><b>" . htmlspecialchars($tally) . "</b></td>";
        echo "</tr>";
        echo "</table>";
        echo "</center>";

        $endtime = microtime(true);
        $duration = $endtime - $starttime;
        echo "<br><center>Process took " . number_format((float)$duration, 4) . " seconds.</center>";
        // Close the SQLite connection
        $db->close();

    } catch (Exception $e) {
        // Display an error message if the database or table does not exist
        echo "<div class='danger-message'>DATABASE ERROR: " . $e->getMessage() . "</div>";
    }
    ?>

    <?php include "footer.php" ?>
</body>
</html>

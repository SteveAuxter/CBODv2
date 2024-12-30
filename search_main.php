<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="mystyle.css">
    <title>Search: Find & Sort</title>
</head>
<body>
    <?php include "search_header.php"; ?>
    <?php include "variables.php"; ?>
    <!-- SEARCH submenu items -->
    <ul class="menu">
        <li><a class="active" href="search_main.php">Find & Sort</a></li>
        <li><a href="search_help.php">Help</a></li>
    </ul>
    <hr>
    <!-- Search form with sorting options (Asset ID default sort) -->
    <form name="search" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="GET">
        Enter full or partial Asset ID, Serial Number, or Note:
        <br><br>
        <input type="text" name="search_term">
        <input type="submit" value="Search">
        <br><br>
        Sort by:
        <input type="radio" name="sort_by" value="annotatedAssetId" checked />Asset ID
        <input type="radio" name="sort_by" value="serialNumber" />Serial Number
        <input type="radio" name="sort_by" value="notes" />Notes
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
        if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["search_term"])) {
            $starttime = microtime(true);
            $search_term = $_GET["search_term"];
            $sort_by = $_GET["sort_by"];
            // Validate the sort_by value to avoid SQL injection
            $allowed_sort_columns = ["annotatedAssetId", "serialNumber", "notes"];
            if (!in_array($sort_by, $allowed_sort_columns)) {
                $sort_by = "annotatedAssetId"; // Default sort colum
            }
            // Prepare the SQL query to search in multiple columns
            $stmt = $db->prepare("
            SELECT * FROM devices WHERE
            annotatedAssetId LIKE :search_term OR
            serialNumber LIKE :search_term OR
            notes LIKE :search_term
            ORDER BY $sort_by
            ");
            $stmt->bindValue(":search_term", "%" . $search_term . "%", SQLITE3_TEXT);
            $results = [];
            $query_result = $stmt->execute();
            // Fetch all results into an array
            while ($row = $query_result->fetchArray(SQLITE3_ASSOC)) {
                $results[] = $row;
            }
            $counter = count($results);
            // Display results in a table if there are matches
            if ($counter > 0) {
                echo "<center>";
                echo "<table class='counting'>";
                echo "<tr>";
                echo "<th><b>Asset ID</b></th>";
                echo "<th><b>Serial #</b></th>";
                echo "<th><b>Notes</b></th>";
                echo "<th><b>Model</b></th>";
                echo "<th><b>Location</b></th>";
                echo "<th><b>User</b></th>";
                echo "<th><b>OS Version</b></th>";
                echo "<th><b>Device ID</b></th>";
                echo "</tr>";
                foreach ($results as $row) {
                    echo "<tr>";
                    echo "<td><a href='assetid_main.php?search_term=" . htmlspecialchars($row['annotatedAssetId']) . "' target='_blank'>" . htmlspecialchars($row['annotatedAssetId']) . "</a></td>";
                    echo "<td><a href='serial_main.php?search_term=" . htmlspecialchars($row['serialNumber']) . "' target='_blank'>" . htmlspecialchars($row['serialNumber']) . "</a></td>";
                    echo "<td>" . nl2br(htmlspecialchars(str_replace(['\\\\n', '\\n'], "\n", $row['notes']))) . "</td>";
                    echo "<td>" . htmlspecialchars($row['model']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['annotatedLocation']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['annotatedUser']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['osVersion']) . "</td>";
                    echo "<td><a href='https://admin.google.com/ac/chrome/devices/" . htmlspecialchars($row['deviceId']) . "' target='_blank'>" . htmlspecialchars($row['deviceId']) . "</a></td>";
                    echo "</tr>";
                }
                echo "</table>";
                echo "</center>";
                echo "<br><center>Found $counter result(s).";
            } else {
                echo "<br><center>No result(s) found.";
            }
            $endtime = microtime(true);
            $duration = $endtime - $starttime;
            echo " Database query took " . number_format((float) $duration, 4) . " seconds.</center>";
            // Close the SQLite database
            $db->close();
        }
    } catch (Exception $e) {
        // Display an error message if the database or table does not exist
        echo "<p>ERROR MESSAGE: " . $e->getMessage() . "</p>";
    } ?>
    <?php include "footer.php"; ?>
</body>
</html>

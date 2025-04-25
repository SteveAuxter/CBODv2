<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="mystyle.css">
    <title>Utilities: Status</title>
</head>
<body>
    <?php include "utilities_header.php" ?>
    <?php include "variables.php" ?>

    <ul class="menu">
        <li><a class="active" href="utilities_main.php">Status</a></li>
        <li><a href="utilities_data1.php">Gather Data</a></li>
        <li><a href="utilities_data2.php">Manage Data</a></li>
        <li><a href="utilities_help.php">Help</a></li>
    </ul>
    <hr>
    <br>
    <?php try {
        // Check if the database file exists
        if (!file_exists($DBname)) {
            throw new Exception(
                "The database file named <b>$DBname</b> does not exist. Please go Utilities > Gather Data to upload your CSV and create the database."
            );
        }
        // Open the SQLite database
        $db = new SQLite3($DBname);
        // Check if the table exists
        $table_check = $db->querySingle("SELECT name FROM sqlite_master WHERE type='table' AND name='devices';");
        if (!$table_check) {
            echo "Database file last modified: <b>" . date('l, F jS, Y \a\t g:i:s a (T)', filemtime($DBname)) . "</b>";
            echo "<br>";
            throw new Exception(
                "The table named 'devices' does not exist. Please go Utilities > Gather Data to upload (or re-upload) your CSV file."
            );
        }
        if ($table_check) {
            echo "Database file last modified: <b>" . date('l, F jS, Y \a\t g:i:s a (T)', filemtime($DBname)) . "</b>";
            echo "<br>";
            $row_count_query = 'SELECT COUNT(*) as count FROM devices';
            $row_count_result = $db->query($row_count_query);
            $row_final_result = $row_count_result->fetchArray(SQLITE3_ASSOC);
            echo "<p>Number of rows (devices) in the database: <b>" . $row_final_result['count'] . "</b></p>";
            throw new Exception(
                "The database file named <b>$DBname</b> exists and the table named 'devices' exists</b> &#9745;"
            );
        }
        // Close the SQLite database
        $db->close();
    } catch (Exception $e) {
        echo "Database Status: " . $e->getMessage() . "<br>";
    }

    $commandGamVer = sprintf($GAMpath . ' version');
    exec($commandGamVer,$infoGamVer);
    echo "<p>GAM Version: <b>" . substr($infoGamVer[0],4,8) . "</b></p>";
    echo "<p>GAM Path: <b>" . substr($infoGamVer[4],6,32) . "</b></p>";
    echo "<p>PHP Version: <b>" . phpversion() . "</b></p>";
    ?>

    <?php include "footer.php" ?>
</body>
</html>

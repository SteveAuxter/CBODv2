<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="mystyle.css">
    <title>Utilities: Manage Data</title>
</head>
<body>
    <?php include "utilities_header.php" ?>
    <?php include "variables.php" ?>

    <ul class="menu">
        <li><a href="utilities_main.php">Status</a></li>
        <li><a href="utilities_data1.php">Gather Data</a></li>
        <li><a class="active" href="utilities_data2.php">Manage Data</a></li>
        <li><a href="utilities_help.php">Help</a></li>
    </ul>
    <hr>

    <br><br>
    <form method="post" enctype="multipart/form-data">
        <input type="submit" name="clear_table" value="Clear Table"><br><br>
        <input type="submit" name="delete_db" value="Delete Database"><br><br>
        <input type="submit" name="backup_db" value="Backup Database"><br><br>
        <input type="file" name="backup_file" accept=".db">
        <input type="submit" name="restore_db" value="Restore Database">
    </form>
    <?php
    $DBexists = file_exists($DBname);
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_POST['clear_table']) && $DBexists) {
            $db = new SQLite3($DBname);
            if ($db->exec("DELETE FROM devices")) {
                echo "Table cleared successfully!";
            } else {
                echo "Failed to clear the table.";
            }
            $db->close(); // Close the connection
        } elseif (isset($_POST['clear_table'])) {
            echo "Database does not exist. Cannot clear table.";
        }

        if (isset($_POST['delete_db']) && $DBexists) {
            $db = new SQLite3($DBname);
            $db->close(); // Close the connection

            if (unlink($DBname)) {
                echo "Database file deleted successfully!";
            } else {
                echo "Failed to delete the database file.";
            }
        } elseif (isset($_POST['delete_db'])) {
            echo "Database does not exist. Cannot delete file.";
        }

        // Backup Database
        if (isset($_POST['backup_db']) && $DBexists) {
            $backupFileName = 'backups/' . basename($DBname) . '_' . date('Ymd_His');
            if (copy($DBname, $backupFileName)) {
                echo "Database backed up successfully to $backupFileName!";
            } else {
                echo "Failed to back up the database.";
            }
        } elseif (isset($_POST['backup_db'])) {
            echo "Database does not exist. Cannot back up.";
        }

        // Restore Database
        if (isset($_POST['restore_db'])) {
            if (isset($_FILES['backup_file']) && $_FILES['backup_file']['error'] === UPLOAD_ERR_OK) {
                $backupFile = $_FILES['backup_file']['tmp_name'];
                $backupFileName = $_FILES['backup_file']['name'];

                // Check if the uploaded file is a valid SQLite database
                if (pathinfo($backupFileName, PATHINFO_EXTENSION) !== 'db') {
                    echo "Invalid file type. Please upload a .db file.";
                } else {
                    // Close the current database connection if it exists
                    if ($DBexists) {
                        $db = new SQLite3($DBname);
                        $db->close(); // Close the connection
                    }

                    // Move the uploaded backup file to replace the existing database
                    if (rename($backupFile, $DBname)) {
                        echo "Database restored successfully from $backupFileName!";
                    } else {
                        echo "Failed to restore the database.";
                    }
                }
            } else {
                echo "No backup file uploaded. Please select a .db file to restore.";
            }
        }
    }
    ?>
    
    <?php include "footer.php" ?>
</body>
</html>

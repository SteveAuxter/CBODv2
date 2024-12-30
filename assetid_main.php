<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="mystyle.css">
    <title>Asset ID: Device Info</title>
    <style>
    #rowInfoHeader {
        text-align: right;
        font-weight: bold;
    }
    #rowInfoData {
        border-bottom-style: solid;
        border-top-style: none;
        border-width: thin;
        text-align: left;
    }
    </style>
</head>
<body>
    <?php include "assetid_header.php" ?>
    <?php include "variables.php" ?>

    <ul class="menu">
        <li><a class="active" href="assetid_main.php">Device Info</a></li>
        <li><a href="assetid_wipeusers.php">Clear Profiles</a></li>
        <li><a href="assetid_powerwash.php">Remote Powerwash</a></li>
        <li><a href="assetid_disable.php">Disable/Enable</a></li>
        <!-- <li><a href="assetid_telemetry.php">Telemetry Data</a></li> -->
        <li><a href="assetid_help.php">Help</a></li>
    </ul>
    <hr>

    <form name="search" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="GET">
        Search: <input type="text" name="search_term">
        <input type="submit" value="Search for single Asset ID">
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
                $stmt = $db->prepare("SELECT * FROM devices WHERE annotatedAssetId LIKE :search_term");
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
                    echo "<h3>Asking Google about device with Asset ID <font color='#008CBA'>$search_term</font>. Here's what I found:</h3>";

                    // GAM Script: Proceed to query additional information from Google
                    $command1 = sprintf("$GAMpath print cros fields annotatedAssetId,annotatedLocation,annotatedUser,bootMode,deviceLicenseType,firmwareVersion,firstEnrollmentTime,lastEnrollmentTime,lastKnownNetwork,lastSync,macAddress,model,notes,orgUnitPath,osVersion,platformVersion,serialNumber,status query asset_id:%s", $search_term);
                    exec($command1, $infoBasic);

                    $command2 = sprintf("$GAMpath print crosactivity query asset_id:%s users", $search_term);
                    exec($command2, $crosActivity);

                    // Process and display the GAM data
                    $infoHeader = explode(",", $infoBasic[0]);
                    $infoData = explode(",", $infoBasic[1]);
                    $arrayLength = count($infoHeader);
                    $infoArray = array();
                    for ($x = 0; $x < $arrayLength; $x++) {
                        $infoArray[$infoHeader[$x]] = $infoData[$x];
                    }
                    // Clean up notes
                    $notesClean = str_replace(["\\\\n", "\\n"], "\n", $infoArray['notes']);
                    $notesCleaner = nl2br($notesClean);
                    // Adjust date formats
                    $syncDateAdj = date('l, F jS, Y \a\t g:i:s a (T)', strtotime($infoArray['lastSync']));
                    $firstEnrollDateAdj = date('l, F jS, Y \a\t g:i:s a (T)', strtotime($infoArray['firstEnrollmentTime']));
                    $lastEnrollDateAdj = date('l, F jS, Y \a\t g:i:s a (T)', strtotime($infoArray['lastEnrollmentTime']));
                    // Display the detailed result in a table
                    ?>
                    <table>
                        <tr><td id="rowInfoHeader"><?php echo "Asset ID:"; ?></td><td id="rowInfoData"><?php echo $infoArray['annotatedAssetId']; ?></td></tr>
                        <tr><td id="rowInfoHeader"><?php echo "Serial #:"; ?></td><td id="rowInfoData"><?php echo $infoArray['serialNumber']; ?></td></tr>
                        <tr><td id="rowInfoHeader"><?php echo "Notes:"; ?></td><td id="rowInfoData"><?php echo $notesCleaner; ?></td></tr>
                        <tr><td id="rowInfoHeader"><?php echo "Model:"; ?></td><td id="rowInfoData"><?php echo $infoArray['model']; ?></td></tr>
                        <?php if ($infoArray['status'] == 'ACTIVE' ) { ?>
                            <tr><td id="rowInfoHeader"><?php echo "Status:"; ?></td><td id="rowInfoData" style="color: green; font-weight: bold"><?php echo $infoArray['status']; ?></td></tr>
                        <?php } ?>
                        <?php if ($infoArray['status'] == 'DISABLED' ) { ?>
                            <tr><td id="rowInfoHeader"><?php echo "Status:"; ?></td><td id="rowInfoData" style="color: red; font-weight: bold"><?php echo $infoArray['status']; ?></td></tr>
                        <?php } ?>
                        <tr><td id="rowInfoHeader"><?php echo "Location:"; ?></td><td id="rowInfoData"><?php echo $infoArray['annotatedLocation']; ?></td></tr>
                        <tr><td id="rowInfoHeader"><?php echo "User:"; ?></td><td id="rowInfoData"><?php echo $infoArray['annotatedUser']; ?></td></tr>
                        <tr><td id="rowInfoHeader"><?php echo "Last Known LAN IP Addr:"; ?></td><td id="rowInfoData"><?php echo $infoArray['lastKnownNetwork.ipAddress']; ?></td></tr>
                        <tr><td id="rowInfoHeader"><?php echo "Last Known WAN IP Addr:"; ?></td><td id="rowInfoData"><?php echo $infoArray['lastKnownNetwork.wanIpAddress']; ?></td></tr>
                        <tr><td id="rowInfoHeader"><?php echo "WiFi MAC:"; ?></td><td id="rowInfoData"><?php echo $infoArray['macAddress']; ?></td></tr>
                        <tr><td id="rowInfoHeader"><?php echo "OS Version:"; ?></td><td id="rowInfoData"><?php echo $infoArray['osVersion']; ?></td></tr>
                        <tr><td id="rowInfoHeader"><?php echo "Platform Version:"; ?></td><td id="rowInfoData"><?php echo $infoArray['platformVersion']; ?></td></tr>
                        <tr><td id="rowInfoHeader"><?php echo "Firmware Version:"; ?></td><td id="rowInfoData"><?php echo $infoArray['firmwareVersion']; ?></td></tr>
                        <tr><td id="rowInfoHeader"><?php echo "Org Unit Path:"; ?></td><td id="rowInfoData"><?php echo $infoArray['orgUnitPath']; ?></td></tr>
                        <tr><td id="rowInfoHeader"><?php echo "Device ID:"; ?></td><td id="rowInfoData"><?php echo $infoArray['deviceId']; ?></td></tr>
                        <tr><td id="rowInfoHeader"><?php echo "Last Sync:"; ?></td><td id="rowInfoData"><?php echo $syncDateAdj . "<font color='#008CBA'> [" . $infoArray['lastSync'] . "]</font>"; ?></td></tr>
                        <tr><td id="rowInfoHeader"><?php echo "First Enrollment:"; ?></td><td id="rowInfoData"><?php echo $firstEnrollDateAdj . "<font color='#008CBA'> [" . $infoArray['firstEnrollmentTime'] . "]</font>"; ?></td></tr>
                        <tr><td id="rowInfoHeader"><?php echo "Last Enrollment:"; ?></td><td id="rowInfoData"><?php echo $lastEnrollDateAdj . "<font color='#008CBA'> [" . $infoArray['lastEnrollmentTime'] . "]</font>"; ?></td></tr>
                        <tr><td id="rowInfoHeader"><?php echo "Boot Mode:"; ?></td><td id="rowInfoData"><?php echo $infoArray['bootMode']; ?></td></tr>
                        <tr><td id="rowInfoHeader"><?php echo "Device License Type:"; ?></td><td id="rowInfoData"><?php echo $infoArray['deviceLicenseType']; ?></td></tr>
                    </table>
                    <?php

                    // Display GAM data about user list
                    echo "<br>";
                    $crosData = explode(",", $crosActivity[1]);
                    echo "<b>USER LIST:</b>";
                    echo "<br>";
                    echo (str_replace(' ', ' <br> ', $crosData[6]));
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

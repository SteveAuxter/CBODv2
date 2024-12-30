<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="mystyle.css">
    <title>Serial Number: Help</title>
</head>
<body>
    <?php include "serial_header.php" ?>

    <ul class="menu">
        <li><a href="serial_main.php">Device Info</a></li>
        <li><a href="serial_wipeusers.php">Clear Profiles</a></li>
        <li><a href="serial_powerwash.php">Remote Powerwash</a></li>
        <li><a href="serial_disable.php">Disable/Enable</a></li>
        <!-- <li><a href="serial_telemetry.php">Telemetry Data</a></li> -->
        <li><a class="active" href="serial_help.php">Help</a></li>
    </ul>
    <hr>
    <?php
    echo "<h3>Serial Number: Device Info</h3>";
    echo "<p>";
    echo "Enter a Serial Number and click Search for single Serial #.<br>";
    echo "CBOD will first query the local database for an <b>EXACT MATCH</b> and provide a single result if one is found.<br>";
    echo "NOTE: If no exact match is found the process stops and a message appears indicating as much.<br>";

    echo "<p>";
    echo "Assuming a match is found in the local database ...<br>";
    echo "CBOD will then use GAMADV-XTD3 to query Google directly and provide basic device info on the same Serial Number.<br>";
    echo "The result from Google will include the Recent User List, where the topmost username is the most recent user of that device.<br>";
    echo "NOTE: The result from Google is the most current data available and may be newer or different than the local database.<br>";

    echo "<h3>Serial Number: Clear Profiles</h3>";
    echo "<p>";
    echo "Enter a Serial Number and click Clear Profiles.<br>";
    echo "CBOD will use GAMADV-XTD3 to issue the 'wipe_users' command to the device based on the Serial Number query.<br>";
    echo "NOTE: It is helpful to make sure the device is on while performing this action.<br>";

    echo "<h3>Serial Number: Remote Powerwash</h3>";
    echo "<p>";
    echo "Enter a Serial Number and click Remote Powerwash.<br>";
    echo "CBOD will use GAMADV-XTD3 to issue the 'remote_powerwash' command to the device based on the Serial Number query.<br>";
    echo "NOTE: It is helpful to make sure the device is on while performing this action.<br>";

    echo "<h3>Serial Number: Disable/Enable</h3>";
    echo "<p>";
    echo "Enter a Serial Number and click Disable or Reenable.<br>";
    echo "CBOD will first use GAMADV-XTD3 to query the current status of the device - either ACTIVE or DISABLED - from Google Workspace.<br>";
    echo "CBOD will then use GAMADV-XTD3 to update (toggle) the status of the device based on the Serial Number query.<br>";
    ?>
    <?php include "footer.php" ?>
</body>
</html>

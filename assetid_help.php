<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="mystyle.css">
    <title>Asset ID: Help</title>
    <style>
    span {
        background-color: #f3f3f3;
        font-family: monospace;
        white-space: nowrap;
    }
    </style>
</head>
<body>
    <?php include "assetid_header.php" ?>

    <ul class="menu">
        <li><a href="assetid_main.php">Device Info</a></li>
        <li><a href="assetid_wipeusers.php">Clear Profiles</a></li>
        <li><a href="assetid_powerwash.php">Remote Powerwash</a></li>
        <li><a href="assetid_disable.php">Disable/Enable</a></li>
        <li><a href="assetid_telemetry.php">Telemetry Data</a></li>
        <li><a class="active" href="assetid_help.php">Help</a></li>
    </ul>
    <hr>
    <?php
    echo "<h3>The Process</h3>";
    echo "<p>";
    echo "In order to execute any of the GAM commands, CBOD will first query the local database for an exact match of the Asset ID entered. <br>";
    echo "If no exact match is found the process stops immediately and a message appears indicating as much. No GAM commands are executed. <br>";
    echo "Assuming an exact match is found in the local database, that info is displayed first and then GAM commands are executed. <br>";

    echo "<h3>Asset ID: Device Info</h3>";
    echo "<p>";
    echo "<span> gam print cros fields annotatedAssetId,annotatedLocation,annotatedUser,bootMode,deviceLicenseType,firmwareVersion,firstEnrollmentTime,lastEnrollmentTime,lastKnownNetwork,lastSync,macAddress,model,notes,orgUnitPath,osVersion,platformVersion,serialNumber,status query asset_id:&lt;AssetID&gt; </span><br>";
    echo "<span> gam print crosactivity query asset_id:&lt;AssetID&gt; users </span><br>";
    echo "The results from Google will include the Recent User List, where the topmost username is the most recent user of that device. <br>";

    echo "<h3>Asset ID: Clear Profiles</h3>";
    echo "<p>";
    echo "<span> gam cros_query asset_id:&lt;AssetID&gt; issuecommand command wipe_users doit </span><br>";
    echo "<b>NOTE:</b> It is helpful to make sure the device is on while performing this action. <br>";

    echo "<h3>Asset ID: Remote Powerwash</h3>";
    echo "<p>";
    echo "<span> gam cros_query asset_id:&lt;AssetID&gt; issuecommand command remote_powerwash doit </span><br>";
    echo "<b>NOTE:</b> It is helpful to make sure the device is on while performing this action. <br>";

    echo "<h3>Asset ID: Disable/Enable</h3>";
    echo "<p>";
    echo "Query for current status <span> gam info cros query asset_id:&lt;AssetID&gt; status </span><br>";
    echo "If status = ACTIVE then <span> gam update cros query \"asset_id:&lt;AssetID&gt; status:ACTIVE\" action disable </span><br>";
    echo "If status = DISABLED then <span> gam update cros query \"asset_id:&lt;AssetID&gt; status:DISABLED\" action reenable </span><br>";

    echo "<h3>Asset ID: Telemetry</h3>";
    echo "<p>";
    echo "<span> gam info crostelemetry &lt;SerialNumber&gt; </span><br>";
    echo "<b>NOTE:</b> This command only works with a valid Serial Number, which is gathered from the local database when querying the Asset ID. <br>";
    ?>
    <?php include "footer.php" ?>
</body>
</html>

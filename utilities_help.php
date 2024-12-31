<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="mystyle.css">
    <title>Utilities: Help</title>
</head>
<body>
    <?php include "utilities_header.php" ?>
    <?php include "variables.php" ?>

    <ul class="menu">
        <li><a href="utilities_main.php">Status</a></li>
        <li><a href="utilities_data1.php">Gather Data</a></li>
        <li><a href="utilities_data2.php">Manage Data</a></li>
        <li><a class="active" href="utilities_help.php">Help</a></li>
    </ul>
    <hr>
    <?php
    echo "<h3>Gathering your data</h3>";
    echo "<p>To use CBODv2 effectively, there are two basic steps:</p>";
    echo "Utilities > Gather Data > STEP (1): use the specified GAM command to gather your device data and store it as a CSV file.<br>";
    echo "Utilities > Gather Data > STEP (2): upload the CSV file generated from the previous step to create the SQLite database.<br>";
    echo "That's it! Now get going!<br>";
    echo "<p>Things to keep in mind:</p>";
    echo "Gathering the data as a CSV creates a snapshot of all your device data at that exact moment. "
        . "Most of the data that is collected is static, but some elements are subject to change. "
        . "Depending on the amount of changes in your Chromebook environment - and to avoid a split-brain dataset - "
        . "you may want to consider executing STEP (1) on a periodic schedule that suits your needs.<br>";
    echo "<br>";
    echo "You can perform STEP (2) as many times as needed without fear of breaking anything. "
        . "Specifically, if you have an existing SQLite database and upload a new CSV, the existing data is overwritten - not updated. "
        . "Speaking to the idea of a periodic schedule, you could perform STEP (1) followed by STEP (2) every day, once a week, once a month, etc.<br>";
    echo "<br>";
    echo "Please understand that the GAM command in STEP (1) purposefully omits devices where status = DEPROVISIONED. "
        . "The GAM command targets devices where status = ACTIVE or status = DISABLED.<br>";

    echo "<h3>Managing your data</h3>";
    echo "<p>These are tools provided as a courtesy and can be used if you are having issues with your SQLite database. These tools are not essential to the function of CBODv2.</p>";
    echo "Utilities > Manage Data > Clear Table: this will empty the 'devices' table while leaving the SQLite database file in place.<br>";
    echo "Utilities > Manage Data > Delete Database: this will remove the SQLite database completely from your system. You will need to restore the database from backup or go back to Gather Data and repeat those steps.<br>";
    echo "Utilities > Manage Data > Backup Database: this will create a backup file of the original SQLite database.<br>";
    echo "Utilities > Manage Data > Restore Database: restore from a previously generated backup.<br>";

    echo "<h3>Check the Github Wiki</h3>";
    echo "<p>You can find additional information at the <a href='https://github.com/SteveAuxter/CBODv2/wiki/Utilities'>CBODv2 Github Wiki</a>.";
    ?>
    <?php include "footer.php" ?>
</body>
</html>

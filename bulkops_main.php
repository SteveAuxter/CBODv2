<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="mystyle.css">
    <title>Bulk Operations: What do I do?</title>
</head>
<body>
    <?php include "bulkops_header.php" ?>
    <?php include "variables.php" ?>
    
    <ul class="menu">
        <li><a class="active" href="bulkops_main.php">What do I do?</a></li>
        <li><a href="bulkops_wipeusers.php">Clear Profiles</a></li>
        <li><a href="bulkops_powerwash.php">Remote Powerwash</a></li>
        <li><a href="bulkops_clearcustom.php">Clear Custom Fields</a></li>
        <li><a href="bulkops_newlocation.php">Update Location</a></li>
        <li><a href="bulkops_moveorgunit.php">Move Org Unit</a></li>
    </ul>
    <hr>
    <?php
    echo "<h3>Bulk Operations: What do I do?</h3>";
    echo "<p>";
    echo "These pages are for generating bulk operation GAM commands that can be copied and pasted into a command prompt. <br>";
    echo "You have to select the data type you are providing because each command is unique to each data type. <br>";
    echo "Enter the relevant data in the open text box and then click Generate Commands. <br>";
    echo "A code box will appear with the commands you can easily copy and paste. Please be sure to check for errors! <br>";
    echo "<h3>Bulk Operations: Clear Profiles</h3>";
    echo "<p>";
    echo "gam cros &lt;DeviceID&gt; issuecommand command wipe_users doit (fastest option, no query involved) <br>";
    echo "gam cros_sn &lt;SerialNumber&gt; issuecommand command wipe_users doit <br>";
    echo "gam cros_query asset_id:&lt;AssetID&gt; issuecommand command wipe_users doit <br>";
    echo "<h3>Bulk Operations: Remote Powerwash</h3>";
    echo "<p>";
    echo "gam cros &lt;DeviceID&gt; issuecommand command remote_powerwash doit (fastest option, no query involved) <br>";
    echo "gam cros_sn &lt;SerialNumber&gt; issuecommand command remote_powerwash doit <br>";
    echo "gam cros_query asset_id:&lt;AssetID&gt; issuecommand command remote_powerwash doit <br>";
    echo "<h3>Bulk Operations: Clear Custom Fields</h3>";
    echo "<p>";
    echo "gam cros &lt;DeviceID&gt; update user \"\" notes \"\" assetid \"\" location \"\" (fastest option, no query involved) <br>";
    echo "gam cros_sn &lt;SerialNumber&gt; update user \"\" notes \"\" assetid \"\" location \"\" <br>";
    echo "gam cros_query asset_id:&lt;AssetID&gt; update user \"\" notes \"\" assetid \"\" location \"\" <br>";
    echo "(FYI: sending double quotes \"\" is the equivalent of sending 'empty' or 'no value') <br>";
    echo "<h3>Bulk Operations: Move Org Unit</h3>";
    echo "<p>";
    echo "gam cros &lt;DeviceID&gt; update ou &lt;OrgUnitPath&gt; (fastest option, no query involved) <br>";
    echo "gam cros_sn &lt;SerialNumber&gt; update ou &lt;OrgUnitPath&gt; <br>";
    echo "gam cros_query asset_id:&lt;AssetID&gt; update ou &lt;OrgUnitPath&gt; <br>";
    echo "This operation has a third selection: you choose an available Organization Unit in your workspace from a dropdown. <br>";
    ?>

    <?php include "footer.php" ?>
</body>
</html>

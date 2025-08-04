<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="mystyle.css">
    <title>Experimental: Main</title>
</head>
<body>
    <?php include "beta_header.php" ?>
    <?php include "variables.php" ?>

    <ul class="menu">
        <li><a class="active" href="beta_main.php">What's This?</a></li>
        <li><a href="battery_info.php">Battery Info</a></li>
        <li><a href="storage_info.php">Storage Info</a></li>
        <li><a href="user_device_history.php">User Device History</a></li>
    </ul>
    <hr>

    <?php
    echo "<h3>Experimental Tools</h3>";
    echo "<p>These are tools that are still in a testing phase or don't have an official home (yet). </p>";
    echo "<p>These tools are intended to gather information about your devices directly from Google. </p>";
    echo "<p>The commands executed do not modify any data in the database. </p>";
    echo "<p>The worst thing that might happen is you attempt to run a tool and no data is returned. </p>";
    ?>

    <?php include "footer.php" ?>
</body>
</html>

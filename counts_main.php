<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="mystyle.css">
    <title>Counts: What do I do?</title>
</head>
<body>
    <?php include "counts_header.php" ?>
    <?php include "variables.php" ?>

    <ul class="menu">
        <li><a class="active" href="counts_main.php">What do I do?</a></li>
        <li><a href="counts_osver.php">OS Version</a></li>
        <li><a href="counts_orgunit.php">Org Units</a></li>
        <li><a href="counts_model.php">Models</a></li>
        <li><a href="counts_location.php">Location</a></li>
        <li><a href="counts_auedates.php">AUE Dates</a></li>
    </ul>
    <hr>
    <?php
    echo "<h3>Counts: What do I do?</h3>";
    echo "Click through each sub-menu item: OS Version, Org Units, etc. <br>";
    echo "The sub-menu title should indicate what is being queried and counted. <br>";
    echo "Theses counts are computed by using the locally stored database. <br>";
    echo "More pre-defined queries <i>may</i> be added in the future. <br>";
    ?>

    <?php include "footer.php" ?>
</body>
</html>

<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="mystyle.css">
    <title>Search: Help</title>
</head>
<body>
    <?php include "search_header.php" ?>
    <?php //include "search_submenu.php" ?>

    <ul class="menu">
        <li><a href="search_main.php">Find & Sort</a></li>
        <li><a class="active" href="search_help.php">Help</a></li>
    </ul>
    <hr>
    <?php
    echo "<h3>Search: Find & Sort</h3>";
    echo "<p>Enter full or partial Asset ID, Serial Number, or Note - then press <b>Search</b>. </p>";
    echo "<p>(Hint) Leave the search field blank - then press <b>Search</b> - this will show all devices. </p>";
    echo "<p>(Hint) You can also use percent (%) and underscore (_) for wildcard searches. Percent represents zero, one or multiple characters. Underscore represents exactly one character. </p>";
    echo "<p>Sorting will always default to Asset ID, but can be toggled to Serial Number or Notes field. </p>";
    echo "<p>Clicking linked Asset ID will open Asset ID > Device Info, showing details direct from Google query - <i>including</i> Recent User List. </p>";
    echo "<p>Clicking linked Serial Number will open Serial Number > Device Info, showing details direct from Google query - <i>including</i> Recent User List. </p>";
    echo "<p>Clicking linked Device ID will open the device page directly in Google Admin console. </p>";
    echo "<h3>Check the Github Wiki</h3>";
    echo "<p>You can find additional information at the <a href='https://github.com/SteveAuxter/CBODv2/wiki/Search'>CBODv2 Github Wiki</a>. </p>";
    ?>

    <?php include "footer.php" ?>
</body>
</html>

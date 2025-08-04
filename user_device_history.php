<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="mystyle.css">
    <title>User Device History</title>
</head>
<body>
    <?php include "beta_header.php"; ?>
    <?php include "variables.php"; ?>

    <ul class="menu">
        <li><a href="beta_main.php">What's This?</a></li>
        <li><a href="battery_info.php">Battery Info</a></li>
        <li><a href="storage_info.php">Storage Info</a></li>
        <li><a class="active" href="user_device_history.php">User Device History</a></li>
    </ul>
    <hr>

    <form name="search" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="GET">
        Enter username (full or partial) or email address (full or partial) of someone in the organization, then press Search:
        <br><br>
        <input type="text" name="search_term">
        <input type="submit" value="Search">
    </form>

    <?php
    echo "<p>";
    echo "The command used here: <b>gam print cros fields annotatedAssetId,serialNumber,notes,model,osVersion,orgUnitPath,recentUsers query status:ACTIVE | grep (search_term)</b> <br>";
    echo "This command will query every active Chromebook in your organization based on the search information entered <br>";
    echo "Please be patient - querying all the active Chromebooks in your organization will take time <br>";
    echo "Once it has gathered any data, a table will be presented below with your results <br>";
    //echo "<br>";
    if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['search_term'])) {
        $search_term = trim($_GET['search_term']); // Trim the search term to avoid unnecessary spaces
        $search_term_lowercase = strtolower($search_term);

        $command1 = sprintf("$GAMpath print cros fields annotatedAssetId,serialNumber,notes,model,osVersion,orgUnitPath,recentUsers query status:ACTIVE | grep %s", $search_term_lowercase);
        exec($command1, $infoBasic);
        if(empty($infoBasic)) {
            echo "<center>";
            echo "<h3>Sorry, there were no results returned for <font color='#008CBA'>$search_term</font>.</h3>";
            echo "</center>";
        }
        if(!empty($infoBasic)) {
            echo "<center>";
            echo "<h3>These are the results returned for <font color='#008CBA'>$search_term</font>.</h3>";
            echo "<table class='counting' id='deviceTable'>";
            echo "<tr>";
            echo "<th><b>Asset ID</b></th>";
            echo "<th><b>Serial #</b></th>";
            echo "<th><b>Notes</b></th>";
            echo "<th><b>Model</b></th>";
            echo "<th><b>OS Version</b></th>";
            echo "<th><b>Org Unit Path</b></th>";
            echo "<th><b>Username</b></th>";
            echo "</tr>";

            foreach ($infoBasic as $data) {
                $explodeData = explode(",", $data);
                echo "<tr>";
                echo "<td>" . $explodeData[1] . "</td>"; // annotatedAssetId
                echo "<td>" . $explodeData[2] . "</td>"; // serialNumber
                echo "<td>" . nl2br(htmlspecialchars(str_replace(['\\\\n', '\\n'], "\n", $explodeData[3]))) . "</td>"; // notes
                echo "<td>" . $explodeData[4] . "</td>"; // model
                echo "<td>" . $explodeData[5] . "</td>"; // osVersion
                echo "<td>" . $explodeData[6] . "</td>"; // orgUnitPath
                echo "<td>" . $explodeData[7] . "</td>"; // username matching search_term
                echo "</tr>";
            }
            echo "</table>";
            echo "</center>";
            echo "<br>";
        }
    }
    ?>

    <?php include "footer.php" ?>
</body>
</html>

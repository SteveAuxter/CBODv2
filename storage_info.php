<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="mystyle.css">
    <title>Storage Info</title>
</head>
<body>
    <?php include "beta_header.php" ?>
    <?php include "variables.php" ?>

    <ul class="menu">
        <li><a href="beta_main.php">What's This?</a></li>
        <li><a href="battery_info.php">Battery Info</a></li>
        <li><a class="active" href="storage_info.php">Storage Info</a></li>
    </ul>
    <hr>

    <br>
    <form method="POST">
        <button type="submit" name="execute">Gather Storage Info</button>
    </form>

    <?php
    echo "<h3>Make sure your Chromebooks are reporting storage info by following the instructions below! You only need to do this once!</h3>";
    echo "<p>";
    echo "Google Admin console > Devices > Chrome > Settings <br>";
    echo "Select an appropriate organizational unit that contains your Chromebooks. <br>";
    echo "Select <b>Device settings</b>, scroll down to <b>User and device reporting</b> > select <b>Report device telemetry</b>. <br>";
    echo "Enable the option for <b>Storage status</b>, then SAVE your settings. <u>It's possible this might already be enabled by default</u>. <br>";
    echo "Wait for your Chromebooks to check in to Google and send telemetry data (about 24 hours). <br>";
    echo "<p>";
    echo "The command used here: <b>gam config csv_output_header_filter \"deviceId,serialNumber,storageInfo.availableDiskBytes,storageStatusReport.0.disk.0.sizeBytes,storageStatusReport.0.reportTime\" print crostelemetry</b> <br>";
    echo "This command will automatically query every active Chromebook in your organization for storage telemetry data <br>";
    echo "Once it has gathered all the relevant data, a table will be presented below with your data <br>";
    echo "<hr>";
    echo "<br>";

    ?><button id="copyButton">Copy Table</button><?php

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['execute'])) {
        //$command1 = sprintf("$GAMpath print crostelemetry fields storageInfo,storageStatusReport,serialNumber");
        $command1 = sprintf("$GAMpath config csv_output_header_filter \"deviceId,serialNumber,storageInfo.availableDiskBytes,storageStatusReport.0.disk.0.sizeBytes,storageStatusReport.0.reportTime\" print crostelemetry");
        exec($command1, $infoBasic);
        array_shift($infoBasic); // This shifts the array by one, removing the headers

        echo "<center>";
        echo "<table class='counting' id='storageTable'>";
        echo "<tr>";
        echo "<th><b>Counter</b></th>";
        echo "<th><b>Device ID</b></th>";
        echo "<th><b>Serial #</b></th>";
        echo "<th><b>Free GB</b></th>";
        echo "<th><b>Used GB</b></th>";
        echo "<th><b>Total GB</b></th>";
        echo "<th><b>% Free</b></th>";
        echo "<th><b>% Used</b></th>";
        echo "<th><b>Last Storage Report</b></th>";
        echo "</tr>";

        $counter = 0;
        foreach ($infoBasic as $data) {
            $explodeData = explode(",", $data);
            $counter++;
            echo "<tr>";
            echo "<td>" . $counter . "</td>";
            echo "<td>" . $explodeData[0] . "</td>"; // deviceId
            echo "<td>" . $explodeData[1] . "</td>"; // serialNumber
            $freeSpaceRaw = (int)$explodeData[2]; // storageInfo.availableDiskBytes
            $freeSpace = round($freeSpaceRaw/1073741824,2); // Free space has to be divided by 1024^3 for proper conversion
            echo "<td>" . $freeSpace . "</td>";
            if ($explodeData[3]) { // storageStatusReport.0.disk.0.sizeBytes
                $totalSpaceRaw = (int)$explodeData[3];
                if ($totalSpaceRaw >= 15000000000 && $totalSpaceRaw <= 16000000000) {
                    $diskSpace = 16;
                }
                if ($totalSpaceRaw >= 31000000000 && $totalSpaceRaw <= 33000000000) {
                    $diskSpace = 32;
                }
                if ($totalSpaceRaw >= 62000000000 && $totalSpaceRaw <= 64000000000) {
                    $diskSpace = 64;
                }
                if ($totalSpaceRaw >= 127000000000 && $totalSpaceRaw <= 129000000000) {
                    $diskSpace = 128;
                }
                $usedSpace = $diskSpace - $freeSpace;
                echo "<td>" . $usedSpace . "</td>";
                echo "<td>" . $diskSpace . "</td>";
                $freeSpacePct = round(($freeSpace/$diskSpace) * 100,0);
                $usedSpacePct = round(($usedSpace/$diskSpace) * 100,0);
                echo "<td>" . $freeSpacePct . "%" . "</td>";
                echo "<td>" . $usedSpacePct . "%" . "</td>";
            }
            if (!$explodeData[3]) { // storageStatusReport.0.disk.0.sizeBytes
                echo "<td>" . "" . "</td>";
                echo "<td>" . "" . "</td>";
                echo "<td>" . "" . "</td>";
                echo "<td>" . "" . "</td>";
            }
            if ($explodeData[4]) {  // storageStatusReport.0.reportTime
                $reportDateAdj = date('Y-m-d h:i:s a (T)', strtotime($explodeData[4]));
                echo "<td>" . $reportDateAdj . "</td>";
            }
            if (!$explodeData[4]) {  // storageStatusReport.0.reportTime
                echo "<td>" . "No Data - check manually" . "</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
        echo "</center>";
        echo "<br>";
    }
    ?>

    <script>
        document.getElementById('copyButton').addEventListener('click', function() {
            const table = document.getElementById('storageTable');
            const range = document.createRange();
            range.selectNode(table);
            window.getSelection().removeAllRanges(); // Clear current selection
            window.getSelection().addRange(range); // Select the table

            try {
                // Copy the selected content to the clipboard
                const successful = document.execCommand('copy');
                const msg = successful ? 'successful' : 'unsuccessful';
                console.log('Copying table was ' + msg);
            } catch (err) {
                console.error('Oops, unable to copy: ', err);
            }

            // Clear selection
            window.getSelection().removeAllRanges();
        });
    </script>

    <?php //include "footer.php" ?>
</body>
</html>

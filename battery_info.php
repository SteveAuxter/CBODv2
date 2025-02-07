<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="mystyle.css">
    <title>Battery Info</title>
</head>
<body>
    <?php include "beta_header.php" ?>
    <?php include "variables.php" ?>

    <ul class="menu">
        <li><a href="beta_main.php">What's This?</a></li>
        <li><a class="active" href="battery_info.php">Battery Info</a></li>
        <li><a href="storage_info.php">Storage Info</a></li>
    </ul>
    <hr>

    <br>
    <form method="POST">
        <button type="submit" name="execute">Gather Battery Info</button>
    </form>

    <?php
    echo "<h3>Make sure your Chromebooks are reporting battery info by following the instructions below! You only need to do this once!</h3>";
    echo "<p>";
    echo "Google Admin console > Devices > Chrome > Settings <br>";
    echo "Select an appropriate organizational unit that contains your Chromebooks. <br>";
    echo "Select <b>Device settings</b>, scroll down to <b>User and device reporting</b> > select <b>Report device telemetry</b>. <br>";
    echo "Enable the option for <b>Power status</b>, then SAVE your settings. <br>";
    echo "Wait for your Chromebooks to check in to Google and send telemetry data (about 24 hours). <br>";
    echo "<p>";
    echo "The command used here: <b>gam config csv_output_header_filter \"deviceId,serialNumber,batteryInfo.0.designCapacity,batteryStatusReport.0.batteryHealth,batteryStatusReport.0.fullChargeCapacity,batteryStatusReport.0.reportTime\" print crostelemetry</b> <br>";
    echo "This command will automatically query every active Chromebook in your organization for battery telemetry data <br>";
    echo "Once it has gathered all the relevant data, a table will be presented below with your data <br>";
    echo "<hr>";
    echo "<br>";

    ?><button id="copyButton">Copy Table</button><?php

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['execute'])) {
        //$command1 = sprintf("$GAMpath print crostelemetry fields batteryInfo,batteryStatusReport,serialNumber");
        $command1 = sprintf("$GAMpath config csv_output_header_filter \"deviceId,serialNumber,batteryInfo.0.designCapacity,batteryStatusReport.0.batteryHealth,batteryStatusReport.0.fullChargeCapacity,batteryStatusReport.0.reportTime\" print crostelemetry");
        exec($command1, $infoBasic);
        array_shift($infoBasic); // This shifts the array by one, removing the headers

        echo "<center>";
        echo "<table class='counting' id='batteryTable'>";
        echo "<tr>";
        echo "<th><b>Counter</b></th>";
        echo "<th><b>Device ID</b></th>";
        echo "<th><b>Serial #</b></th>";
        echo "<th><b>Design Capacity</b></th>";
        echo "<th><b>Charge Capacity</b></th>";
        echo "<th><b>Battery Health</b></th>";
        echo "<th><b>Last Battery Report</b></th>";
        echo "<th><b>Batt Pct</b></th>";
        echo "</tr>";

        $counter = 0;
        foreach ($infoBasic as $data) {
            $explodeData = explode(",", $data);
            $counter++;
            echo "<tr>";
            echo "<td>" . $counter . "</td>";
            echo "<td>" . $explodeData[0] . "</td>"; // deviceId
            echo "<td>" . $explodeData[1] . "</td>"; // serialNumber
            if ($explodeData[2]) { // batteryInfo.0.designCapacity
                echo "<td>" . $explodeData[2] . "</td>";
                $battDesignCapacity = (int)$explodeData[2];
            }
            if (!$explodeData[2]) { // batteryInfo.0.designCapacity
                echo "<td>No Data</td>";
            }
            if ($explodeData[4]) { // batteryStatusReport.0.fullChargeCapacity
                echo "<td>" . $explodeData[4] . "</td>";
                $battChargeCapacity = (int)$explodeData[4];
            }
            if (!$explodeData[4]) { // batteryStatusReport.0.fullChargeCapacity
                echo "<td>No Data</td>";
            }
            echo "<td>" . $explodeData[3] . "</td>"; // batteryStatusReport.0.batteryHealth
            if ($explodeData[5]) { // batteryStatusReport.0.reportTime
                $reportDateAdj = date('Y-m-d h:i:s a (T)', strtotime($explodeData[5]));
                echo "<td>" . $reportDateAdj . "</td>";
            }
            if (!$explodeData[5]) { // batteryStatusReport.0.reportTime
                echo "<td>" . "" . "</td>";
            }
            if ($explodeData[2] && $explodeData[4]) {
                $battPercentageDec = $battChargeCapacity / $battDesignCapacity;
                $battPercentagePct = round((float)$battPercentageDec * 100);
                echo "<td>" . $battPercentagePct . "%" . "</td>";
            }
            if (!$explodeData[2] && !$explodeData[4]) {
                echo "<td>" . "" . "</td>";
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
            const table = document.getElementById('batteryTable');
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

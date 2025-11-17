<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="mystyle.css">
    <title>Utilities: Gather Data</title>

    <script>
    function copyToClipboard(text) {
        const textarea = document.createElement('textarea');
        textarea.value = text;
        document.body.appendChild(textarea);
        textarea.select();
        document.execCommand('copy');
        document.body.removeChild(textarea);
        alert('Command copied to clipboard!');
    }
</script>

</head>
<body>
    <?php include "utilities_header.php" ?>
    <?php include "variables.php" ?>

    <ul class="menu">
        <li><a href="utilities_main.php">Status</a></li>
        <li><a class="active" href="utilities_data1.php">Gather Data</a></li>
        <li><a href="utilities_data2.php">Manage Data</a></li>
        <li><a href="utilities_help.php">Help</a></li>
    </ul>
    <hr>

    <?php
    $fullCommand = $GAMpath . ' config csv_output_column_delimiter ";" print cros fields annotatedAssetId,annotatedLocation,annotatedUser,autoUpdateExpiration,bootMode,ethernetMacAddress,firmwareVersion,lastEnrollmentTime,lastSync,macAddress,manufactureDate,model,notes,orgUnitPath,osVersion,platformVersion,serialNumber,status queries "status:ACTIVE","status:DISABLED" > CBODv2_' . date('Ymd_His') . '.csv';
    $escapedCommand = str_replace('\\', '\\\\', $fullCommand);
    echo "<b>STEP (1):</b> Use the GAM command below to gather all the necessary Chromebook data from Google. The name of the CSV file at the end of the line can be changed, if needed.";
    echo "<br><br>";
    echo "<div class='code-block'>";
    echo '<button class="copy-button" onclick="copyToClipboard(\'' . htmlspecialchars($escapedCommand) . '\')">Copy</button>';
    echo "<pre>" . htmlspecialchars($fullCommand) . "</pre>";
    echo "</div>";
    echo "<br><br>";
    echo "<b>STEP (2):</b> Upload the CSV created from the previous step to create/update the database.";
    echo "<br><br>";
    ?>

    <form enctype="multipart/form-data" method="post">
        <label for="csv_file">Choose CSV file:</label><br><br>
        <input type="file" id="csv_file" name="csv_file" accept=".csv"><br><br>
        <input type="submit" value="Upload">
    </form>
    <br>

    <?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_FILES['csv_file']) && $_FILES['csv_file']['error'] == 0) {
            $file = $_FILES['csv_file']['tmp_name'];
            $fileMimeType = mime_content_type($file);

            // Ensure the uploaded file is a CSV
            if ($fileMimeType == 'text/plain' || $fileMimeType == 'text/csv') {
                // Create (connect to) SQLite database in file
                $db = new SQLite3($DBname);

                // Check if the table exists
                $result = $db->query("SELECT name FROM sqlite_master WHERE type='table' AND name='devices'");
                if ($result->fetchArray()) {
                    // Clear the table
                    $db->exec("DELETE FROM devices");
                } else {
                    // Create the table if it doesn't exist
                    $db->exec("CREATE TABLE devices (
                        id INTEGER PRIMARY KEY,
                        deviceId TEXT,
                        annotatedAssetId TEXT,
                        annotatedLocation TEXT,
                        annotatedUser TEXT,
                        autoUpdateExpiration TEXT,
                        bootMode TEXT,
                        ethernetMacAddress TEXT,
                        firmwareVersion TEXT,
                        lastEnrollmentTime TEXT,
                        lastSync TEXT,
                        macAddress TEXT,
                        manufactureDate TEXT,
                        model TEXT,
                        notes TEXT,
                        orgUnitPath TEXT,
                        osVersion TEXT,
                        platformVersion TEXT,
                        serialNumber TEXT,
                        status TEXT
                    )");
                }

                // Open the CSV file
                if (($handle = fopen($file, 'r')) !== FALSE) {
                    fgetcsv($handle, 1000, ';', '"', '\\'); // Skip the header row

                    // Prepare statement for inserting rows
                    $stmt = $db->prepare("INSERT INTO devices (
                        deviceId,
                        annotatedAssetId,
                        annotatedLocation,
                        annotatedUser,
                        autoUpdateExpiration,
                        bootMode,
                        ethernetMacAddress,
                        firmwareVersion,
                        lastEnrollmentTime,
                        lastSync,
                        macAddress,
                        manufactureDate,
                        model,
                        notes,
                        orgUnitPath,
                        osVersion,
                        platformVersion,
                        serialNumber,
                        status
                    ) VALUES (
                        :deviceId,
                        :annotatedAssetId,
                        :annotatedLocation,
                        :annotatedUser,
                        :autoUpdateExpiration,
                        :bootMode,
                        :ethernetMacAddress,
                        :firmwareVersion,
                        :lastEnrollmentTime,
                        :lastSync,
                        :macAddress,
                        :manufactureDate,
                        :model,
                        :notes,
                        :orgUnitPath,
                        :osVersion,
                        :platformVersion,
                        :serialNumber,
                        :status
                    )");

                    while (($devices = fgetcsv($handle, 1000, ';', '"', '\\')) !== FALSE) {
                        // Validate row data
                        if (count($devices) == 19) {
                            // Bind values and execute insert statement
                            $stmt->bindValue(':deviceId', $devices[0], SQLITE3_TEXT);
                            $stmt->bindValue(':annotatedAssetId', $devices[1], SQLITE3_TEXT);
                            $stmt->bindValue(':annotatedLocation', $devices[2], SQLITE3_TEXT);
                            $stmt->bindValue(':annotatedUser', $devices[3], SQLITE3_TEXT);
                            $stmt->bindValue(':autoUpdateExpiration', $devices[4], SQLITE3_TEXT);
                            $stmt->bindValue(':bootMode', $devices[5], SQLITE3_TEXT);
                            $stmt->bindValue(':ethernetMacAddress', $devices[6], SQLITE3_TEXT);
                            $stmt->bindValue(':firmwareVersion', $devices[7], SQLITE3_TEXT);
                            $stmt->bindValue(':lastEnrollmentTime', $devices[8], SQLITE3_TEXT);
                            $stmt->bindValue(':lastSync', $devices[9], SQLITE3_TEXT);
                            $stmt->bindValue(':macAddress', $devices[10], SQLITE3_TEXT);
                            $stmt->bindValue(':manufactureDate', $devices[11], SQLITE3_TEXT);
                            $stmt->bindValue(':model', $devices[12], SQLITE3_TEXT);
                            $stmt->bindValue(':notes', $devices[13], SQLITE3_TEXT);
                            $stmt->bindValue(':orgUnitPath', $devices[14], SQLITE3_TEXT);
                            $stmt->bindValue(':osVersion', $devices[15], SQLITE3_TEXT);
                            $stmt->bindValue(':platformVersion', $devices[16], SQLITE3_TEXT);
                            $stmt->bindValue(':serialNumber', $devices[17], SQLITE3_TEXT);
                            $stmt->bindValue(':status', $devices[18], SQLITE3_TEXT);
                            $stmt->execute();
                        } else {
                            echo "Skipping invalid row with incorrect number of columns.";
                        }
                    }
                    fclose($handle);
                    echo "<div class='success-message'>Data imported successfully!</div>";
                } else {
                    echo "<div class='danger-message'>Unable to open the CSV file. Please try again.</div>";
                }
                $db->close();  // Close the connection
            } else {
                echo "<div class='warning-message'>Invalid file type. Please upload a CSV file.</div>";
            }
        } elseif (isset($_FILES['csv_file'])) {
            echo "<div class='danger-message'>Failed to upload the file. Please select a CSV file.</div>";
        }
    }
    ?>

    <?php include "footer.php" ?>
</body>
</html>


<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="mystyle.css">
    <title>Bulk Operations: Update Location</title>
    <style>
    form {
        margin-bottom: 20px;
    }
    fieldset {
        margin-bottom: 15px;
        padding: 10px;
    }
    pre {
        white-space: pre-wrap;
        word-wrap: break-word;
        background-color: #f4f4f4;
        padding: 10px;
        border-radius: 5px;
        max-height: 400px;
        overflow-y: auto;
    }
    </style>
</head>
<body>
    <?php include "bulkops_header.php"; ?>
    <?php include "variables.php"; ?>

    <ul class="menu">
        <li><a href="bulkops_main.php">What do I do?</a></li>
        <li><a href="bulkops_wipeusers.php">Clear Profiles</a></li>
        <li><a href="bulkops_powerwash.php">Remote Powerwash</a></li>
        <li><a href="bulkops_clearcustom.php">Clear Custom Fields</a></li>
        <li><a class="active" href="bulkops_newlocation.php">Update Location</a></li>
        <li><a href="bulkops_moveorgunit.php">Move Org Unit</a></li>
    </ul>
    <hr>

    <?php
    function generateCommand($data, $dataType, $location) {
        $locationQuoted = '"' . $location . '"';
        switch ($dataType) {
            case "device_id":
            return "gam cros $data update location $locationQuoted";
            case "serial_number":
            return "gam cros_sn $data update location $locationQuoted";
            case "asset_id":
            return "gam cros_query asset_id:$data update location $locationQuoted";
            default:
            return "Invalid data type selected.";
        }
    }

    $commands = [];
    $error = "";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $dataType = isset($_POST["data_type"]) ? trim($_POST["data_type"]) : "";
        $inputData = isset($_POST["input_data"]) ? trim($_POST["input_data"]) : "";
        $locationField = isset($_POST["location_field"]) ? trim($_POST["location_field"]) : "";

        if (empty($dataType) || empty($inputData) || empty($locationField)) {
            $error = "Please select a data type, provide input data, and enter a location.";
        } else {
            $inputData = str_replace(",", "\n", $inputData);
            $dataArray = array_filter(array_map('trim', explode("\n", $inputData)));

            $commands = array_map(function($data) use ($dataType, $locationField) {
                return generateCommand($data, $dataType, $locationField);
            }, $dataArray);
        }
    }
    ?>

    <form method="POST">
        <fieldset>
            <legend>Select Data Type:</legend>
            <label>
                <input type="radio" name="data_type" value="device_id" required
                <?php echo (isset($dataType) && $dataType=="device_id") ? "checked" : ""; ?>>
                Device ID
            </label><br>
            <label>
                <input type="radio" name="data_type" value="serial_number" required
                <?php echo (isset($dataType) && $dataType=="serial_number") ? "checked" : ""; ?>>
                Serial Number
            </label><br>
            <label>
                <input type="radio" name="data_type" value="asset_id" required
                <?php echo (isset($dataType) && $dataType=="asset_id") ? "checked" : ""; ?>>
                Asset ID
            </label>
        </fieldset>
        <br>

        <label for="input_data">Enter Data:</label><br>
        <small>(You can use new lines or commas to separate entries)</small><br>
        <textarea id="input_data" name="input_data" rows="10" cols="40" required><?php echo isset($inputData) ? htmlspecialchars($inputData) : ''; ?></textarea>
        <br><br>

        <label for="location_field">New Location (update custom field):</label><br>
        <input type="text" name="location_field" id="location_field" value="<?php echo isset($locationField) ? htmlspecialchars($locationField) : ''; ?>" required>
        <br><br>

        <button type="submit">Generate Commands</button>
    </form>

    <?php if (!empty($commands)): ?>
        <div class="bulk-output">
            <button class="select-all-button" onclick="copyToClipboard()">Select All</button>
            <pre id="output-block"><?php echo implode("\n", $commands); ?></pre>
        </div>
    <?php elseif (!empty($error)): ?>
        <p class="bulk-error"><?php echo $error; ?></p>
    <?php endif; ?>

    <script>
    function copyToClipboard() {
        const outputBlock = document.getElementById("output-block");
        navigator.clipboard.writeText(outputBlock.textContent).then(() => {
            // Add a temporary highlight effect
            outputBlock.style.backgroundColor = "#d4edda"; // light green
            outputBlock.style.transition = "background-color 0.5s";

            setTimeout(() => {
                outputBlock.style.backgroundColor = "#f4f4f4"; // revert to original
            }, 800);
        }).catch(err => {
            console.error("Failed to copy commands: ", err);
            // Optionally, add a small error style
            outputBlock.style.backgroundColor = "#f8d7da"; // light red
            setTimeout(() => {
                outputBlock.style.backgroundColor = "#f4f4f4";
            }, 800);
        });
    }
    </script>

    <?php include "footer.php"; ?>
</body>
</html>

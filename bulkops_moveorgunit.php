<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="mystyle.css">
    <title>Bulk Operations: Move Org Unit</title>
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
    <?php include "bulkops_header.php" ?>
    <?php include "variables.php" ?>

    <ul class="menu">
        <li><a href="bulkops_main.php">What do I do?</a></li>
        <li><a href="bulkops_allinone.php">All-in-One</a></li>
        <li><a href="bulkops_clearcustom.php">Clear Custom Fields</a></li>
        <li><a href="bulkops_newlocation.php">Update Location</a></li>
        <li><a class="active" href="bulkops_moveorgunit.php">Move Org Unit</a></li>
    </ul>
    <hr>

    <?php
    $orgUnits = [];
    $command = sprintf("$GAMpath print orgs orgUnitPath");
    exec($command, $output, $resultCode);

    if ($resultCode === 0) {
        foreach ($output as $line) {
            $line = trim($line);
            if (!empty($line) && $line !== "orgUnitPath") {
                $orgUnits[] = $line;
            }
        }
    } else {
        $error = "Failed to retrieve organizational units.";
    }

    // Check if the form was submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Get the data type and input from the form
        $dataType = htmlspecialchars(trim($_POST["data_type"]));
        $inputData = htmlspecialchars(trim($_POST["input_data"]));
        $selectedOU = htmlspecialchars(trim($_POST["org_unit"]));

        // Validate the input
        if (!empty($dataType) && !empty($inputData) && !empty($selectedOU)) {
            // Normalize the input to handle both comma-separated and newline-separated data
            $inputData = str_replace(",", "\n", $inputData);
            $dataArray = array_filter(array_map('trim', explode("\n", $inputData)));

            // Generate commands based on the selected data type
            $commands = array_map(function($data) use ($dataType, $selectedOU) {
                $selectedOUquotes = '"' . $selectedOU . '"';
                switch ($dataType) {
                    case "device_id":
                    return "gam cros " . $data . " update ou " . $selectedOUquotes;
                    case "serial_number":
                    return "gam cros_sn " . $data . " update ou " . $selectedOUquotes;
                    case "asset_id":
                    return "gam cros_query asset_id:" . $data . " update ou " . $selectedOUquotes;
                    default:
                    return "Invalid data type selected.";
                }
            }, $dataArray);
        } else {
            $error = "Please select a data type, provide the input, and choose an organizational unit.";
        }
    }
    ?>

    <form method="POST">
        <fieldset>
            <legend>Select Data Type:</legend>
            <label>
                <input type="radio" name="data_type" value="device_id" required>
                Device ID
            </label><br>
            <label>
                <input type="radio" name="data_type" value="serial_number" required>
                Serial Number
            </label><br>
            <label>
                <input type="radio" name="data_type" value="asset_id" required>
                Asset ID
            </label>
        </fieldset>
        <br>

        <label for="input_data">Enter Data:</label><br>
        <small>(You can use new lines or commas to separate entries)</small><br>
        <textarea id="input_data" name="input_data" rows="10" cols="40" required></textarea><br>
        <br>

        <label for="org_unit">Select Organizational Unit:</label>
        <select id="org_unit" name="org_unit" required>
            <option value="">-- Choose an Org Unit --</option>
            <?php foreach ($orgUnits as $ou): ?>
                <option value="<?php echo htmlspecialchars($ou); ?>"><?php echo htmlspecialchars($ou); ?></option>
            <?php endforeach; ?>
        </select><br><br>

        <button type="submit">Generate Commands</button>
    </form>

    <?php if (isset($commands)): ?>
        <div class="bulk-output">
            <button class="select-all-button" onclick="copyToClipboard()">Select All</button>
            <pre id="output-block"><?php echo implode("\n", $commands); ?></pre>
        </div>
    <?php elseif (isset($error)): ?>
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

    <?php include "footer.php" ?>
</body>
</html>

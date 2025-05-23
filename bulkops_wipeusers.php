<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="mystyle.css">
    <title>Bulk Operations: Clear Profiles</title>
    <style>
    form {
        margin-bottom: 20px;
    }
    </style>
</head>
<body>
    <?php include "bulkops_header.php" ?>
    <?php include "variables.php" ?>

    <ul class="menu">
        <li><a href="bulkops_main.php">What do I do?</a></li>
        <li><a class="active" href="bulkops_wipeusers.php">Clear Profiles</a></li>
        <li><a href="bulkops_powerwash.php">Remote Powerwash</a></li>
        <li><a href="bulkops_clearcustom.php">Clear Custom Fields</a></li>
        <li><a href="bulkops_moveorgunit.php">Move Org Unit</a></li>
    </ul>
    <hr>

    <?php
    // Check if the form was submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Get the data type and input from the form
        $dataType = htmlspecialchars(trim($_POST["data_type"]));
        $inputData = htmlspecialchars(trim($_POST["input_data"]));

        // Validate the input
        if (!empty($dataType) && !empty($inputData)) {
            // Normalize the input to handle both comma-separated and newline-separated data
            $inputData = str_replace(",", "\n", $inputData);
            $dataArray = array_filter(array_map('trim', explode("\n", $inputData)));

            // Generate commands based on the selected data type
            $commands = array_map(function($data) use ($dataType) {
                switch ($dataType) {
                    case "device_id":
                        return "gam cros " . $data . " issuecommand command wipe_users doit";
                    case "serial_number":
                        return "gam cros_sn " . $data . " issuecommand command wipe_users doit";
                    case "asset_id":
                        return "gam cros_query asset_id:" . $data . " issuecommand command wipe_users doit";
                    default:
                        return "Invalid data type selected.";
                }
            }, $dataArray);
        } else {
            $error = "Please select a data type and provide the input.";
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
        <textarea id="input_data" name="input_data" rows="10" cols="40" required></textarea><br><br>
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
                alert("Commands copied to clipboard!");
            }).catch(err => {
                console.error("Failed to copy commands: ", err);
            });
        }
    </script>

    <?php include "footer.php" ?>
</body>
</html>

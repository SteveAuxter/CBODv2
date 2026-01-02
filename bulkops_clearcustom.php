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
        <li><a class="active" href="bulkops_clearcustom.php">Clear Custom Fields</a></li>
        <li><a href="bulkops_newlocation.php">Update Location</a></li>
        <li><a href="bulkops_moveorgunit.php">Move Org Unit</a></li>
    </ul>
    <hr>

    <?php
    // When the page is (re)loaded all the checkboxes are enabled by default
    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        $clearFields = ["user", "notes", "assetid", "location"];
    }

    // Check if the form was submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Get the data type and input from the form
        $dataType = trim($_POST["data_type"] ?? "");
        $clearFields = $_POST["clear_fields"] ?? [];
        $inputData = trim($_POST["input_data"] ?? "");

        // Validate the input
        if (!empty($dataType) && !empty($inputData)) {
            $allowedDataTypes = ["device_id", "serial_number", "asset_id"];
            if (!in_array($dataType, $allowedDataTypes, true)) {
                $error = "Invalid data type selected.";
            }

            $allowedClearFields = ["user", "notes", "assetid", "location"];
            $clearFields = array_values(array_intersect($clearFields, $allowedClearFields));
            if (empty($clearFields)) {
                $error = "Please select at least one field to clear.";
            }

            if (!isset($error)) {
                // Normalize the input to handle both comma-separated and newline-separated data
                $inputData = str_replace(",", "\n", $inputData);
                $dataArray = array_filter(array_map('trim', explode("\n", $inputData)));

                $updateParts = array_map(fn($f) => $f . ' ""', $clearFields);
                $updateString = implode(" ", $updateParts);

                // Generate commands based on the selected data type
                $commands = array_map(function ($data) use ($dataType, $updateString) {
                    switch ($dataType) {
                        case "device_id":
                        return "gam cros {$data} update {$updateString}";
                        case "serial_number":
                        return "gam cros_sn {$data} update {$updateString}";
                        case "asset_id":
                        return "gam cros_query asset_id:{$data} update {$updateString}";
                    }
                }, $dataArray);
            }
        } else {
            $error = "Please make your selection(s) and provide the input.";
        }
    }
    ?>

    <form method="POST">
        <fieldset>
            <legend>Select Data Type:</legend>
            <label>
                <input type="radio" name="data_type" value="device_id" required
                <?php if (($dataType ?? "") === "device_id") echo "checked"; ?>>
                Device ID
            </label><br>
            <label>
                <input type="radio" name="data_type" value="serial_number" required
                <?php if (($dataType ?? "") === "serial_number") echo "checked"; ?>>
                Serial Number
            </label><br>
            <label>
                <input type="radio" name="data_type" value="asset_id" required
                <?php if (($dataType ?? "") === "asset_id") echo "checked"; ?>>
                Asset ID
            </label>
        </fieldset>

        <fieldset>
            <legend>Fields to Clear:</legend>
            <label>
                <input type="checkbox" name="clear_fields[]" value="user"
                <?= in_array("user", $clearFields ?? []) ? "checked" : "" ?>>
                User
            </label><br>
            <label>
                <input type="checkbox" name="clear_fields[]" value="notes"
                <?= in_array("notes", $clearFields ?? []) ? "checked" : "" ?>>
                Notes
            </label><br>
            <label>
                <input type="checkbox" name="clear_fields[]" value="assetid"
                <?= in_array("assetid", $clearFields ?? []) ? "checked" : "" ?>>
                Asset ID
            </label><br>
            <label>
                <input type="checkbox" name="clear_fields[]" value="location"
                <?= in_array("location", $clearFields ?? []) ? "checked" : "" ?>>
                Location
            </label>
        </fieldset>
        <br>
        <label for="input_data">Enter Data:</label><br>
        <small>(You can use new lines or commas to separate entries)</small><br>
        <!-- The <textarea> line below needs to remain a single line, otherwise it will add unecessary spaces into the textbox -->
        <textarea id="input_data" name="input_data" rows="10" cols="40" required><?= htmlspecialchars($inputData ?? "") ?></textarea>
        <br><br>
        <button type="submit">Generate Commands</button>
    </form>

    <?php if (isset($commands)): ?>
        <div class="bulk-output">
            <button class="select-all-button" onclick="copyToClipboard()">Select All</button>
            <pre id="output-block"><?php echo htmlspecialchars(implode("\n", $commands)); ?></pre>
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

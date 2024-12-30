<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="mystyle.css">
    <title>DataTables: Search Builder</title>

    <link href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/3.2.0/css/buttons.dataTables.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/datetime/1.5.4/css/dataTables.dateTime.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/responsive/3.0.3/css/responsive.dataTables.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/searchbuilder/1.8.1/css/searchBuilder.dataTables.min.css" rel="stylesheet">

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.2.0/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.2.0/js/buttons.colVis.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.2.0/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.2.0/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/datetime/1.5.4/js/dataTables.dateTime.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/3.0.3/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/searchbuilder/1.8.1/js/dataTables.searchBuilder.min.js"></script>

    <!--
    DOM settings explained, default config:
    B = Buttons
    Q = SearchBuilder
    l = Length changing input control
    f = Filtering input
    t = the Table
    i = Information summary
    p = Pagination control
    r = pRocessing display element
    -->

<script type="text/javascript">
$(document).ready(function() {
    $('#myTable').DataTable( {
        lengthMenu:
        [
            [10, 25, 100, -1],
            [10, 25, 100, "All"]
        ],
        responsive: true,
        dom: "QBtlrip",
        buttons:
        [
            {extend: 'copy', title: null, exportOptions: {columns: ':visible'}},
            {extend: 'csv', title: 'Your_CSV_Filename', exportOptions: {columns: ':visible'}},
            {extend: 'excel', title: 'Your_XLSX_Filename', exportOptions: {columns: ':visible'}},
            {extend: 'pdf', title: 'CBODv2 Datatables PDF', exportOptions: {columns: ':visible'}},
            {extend: 'print', title: 'CBODv2 Datatables Print', exportOptions: {columns: ':visible'}},
            {extend: 'colvis', text: 'Toggle Columns'}
        ]
    } );
} );
</script>

</head>
<body>
    <?php include "datatables_header.php" ?>
    <?php include "variables.php" ?>

    <ul class="menu">
        <li><a href="datatables_main.php">Full List</a></li>
        <li><a class="active" href="datatables_builder.php">Search Builder</a></li>
        <li><a href="datatables_help.php">Help</a></li>
    </ul>
    <hr>

    <?php try {
        // Check if the database file exists
        if (!file_exists($DBname)) {
            throw new Exception(
                "The database file named $DBname does not exist. Please go Utilities > Gather Data to upload your CSV and create the database."
            );
        }

        // Open the SQLite database
        $db = new SQLite3($DBname);

        // Check if the table exists
        $table_check = $db->querySingle("SELECT name FROM sqlite_master WHERE type='table' AND name='devices';");
        if (!$table_check) {
            throw new Exception(
                "The table named 'devices' does not exist. Please go Utilities > Gather Data to upload (or re-upload) your CSV file."
            );
        }

        // Prepare the SQL query to select all records from the devices table
        $query_result = $db->query("SELECT * FROM devices");
        ?>
        <!-- Exit PHP and draw HTML table -->
        <table id="myTable" class="cell-border display responsive nowrap" style="width:100%">
            <thead>
                <tr>
                    <th>Asset ID</th>
                    <th>Serial #</th>
                    <th>Notes</th>
                    <th>Model</th>
                    <th>Status</th>
                    <th>Location</th>
                    <th>User</th>
                    <th>Org Unit Path</th>
                    <th>Device ID</th>
                    <th>OS Version</th>
                    <th>Platform Version</th>
                    <th>Firmware Version</th>
                    <!-- <th>Boot Mode</th> -->
                    <!-- <th>Last Enrollment</th> -->
                    <!-- <th>Last Sync</th> -->
                    <!-- <th>Manufacture Date</th> -->
                    <!-- <th>Auto Update Exp</th> -->
                    <th>WiFi MAC</th>
                    <th>Wired MAC</th>
                </tr>
            </thead>
            <?php
            // Fetch all results and display them
            while ($row = $query_result->fetchArray(SQLITE3_ASSOC)) {
                ?>
                <!-- Exit PHP and populate HTML table -->
                <tr align="center">
                    <td><?php echo $row["annotatedAssetId"]; ?></td>
                    <td><?php echo $row["serialNumber"]; ?></td>
                    <td><?php echo (str_replace('\\\n', '<br>', $row["notes"])); ?></td>
                    <td><?php echo $row["model"]; ?></td>
                    <td><?php echo $row["status"]; ?></td>
                    <td><?php echo $row["annotatedLocation"]; ?></td>
                    <td><?php echo $row["annotatedUser"]; ?></td>
                    <td><?php echo $row["orgUnitPath"]; ?></td>
                    <td><?php echo $row["deviceId"]; ?></td>
                    <td><?php echo $row["osVersion"]; ?></td>
                    <td><?php echo $row["platformVersion"]; ?></td>
                    <td><?php echo $row["firmwareVersion"]; ?></td>
                    <!-- <td><//?php echo $row["bootMode"]; ?></td> -->
                    <!-- <td><//?php echo $row["lastEnrollmentTime"]; ?></td> -->
                    <!-- <td><//?php echo $row["lastSync"]; ?></td> -->
                    <!-- <td><//?php echo $row["manufactureDate"]; ?></td> -->
                    <!-- <td><//?php echo $row["autoUpdateExpiration"]; ?></td> -->
                    <td><?php echo $row["macAddress"]; ?></td>
                    <td><?php echo $row["ethernetMacAddress"]; ?></td>
                </tr>
                <?php
            }
            ?>
            <!-- Exit PHP and finalize HTML table -->
        </table>
        <?php
        $db->close(); // Close the SQLite connection
    } catch (Exception $e) {
        // Display an error message if the database or table does not exist
        echo "<div class='danger-message'>DATABASE ERROR: " . $e->getMessage() . "</div>";
    }
    echo "<br><br>" ?>

    <?php include "footer.php" ?>
</body>
</html>

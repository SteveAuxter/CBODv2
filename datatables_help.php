<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="mystyle.css">
    <link rel="icon" type="image/x-icon" href="favicon/favicon.ico">
    <title>DataTables: Help</title>
</head>
<body>
    <?php include "datatables_header.php" ?>

    <ul class="menu">
        <li><a href="datatables_main.php">Full List</a></li>
        <li><a href="datatables_builder.php">Search Builder</a></li>
        <li><a class="active" href="datatables_help.php">Help</a></li>
    </ul>
    <hr>

    <ul>
        <li><h3><a href="https://datatables.net/" target="_blank">DataTables</a> | Advanced interaction features for your tables</h3></li>
        <p>
            DataTables makes use of <b>A LOT</b> of javascript and plug-ins.
            The first thing you may notice is that it takes a few seconds for the page to render, as it is organizing and presenting the full collection to you, sorted by Asset ID.
            <p>
                The default number of entries shown when the page is first loaded is <b>10</b>. You can change this to 10, 25, 100 or All. The setting is found in the lower left corner under the table - you may have to scroll down.
                <p>
                    Depending on the size of your display, resolution, or zoom level, you will probably notice green plus signs in the leftmost column. Clicking the green plus sign will expand and show the remainder of the data that couldn't fit on screen.
                    You can use the <b>Toggle Columns</b> dropdown button to turn on/off specific columns for display purposes. Please know that all columns are automatically toggled on whenever the DataTables page is reloaded.
                    <p>
                        As you type in the <b>Search</b> box to the upper right, your results are automatically filtered as you type. Text is case-insensitive meaning "ABC" is the same as "abc".
                        <b>Search</b> literally searches and filters everything - even the columns you cannot see or are toggled off.
                        <p>
                            There are 5 options for exporting your data or search results. These are the <b>Copy | CSV | Excel | PDF | Print</b> buttons above the table.<br>
                            - <b>Copy</b> will copy your data into your local clipboard, to be pasted into some other program.<br>
                            - <b>CSV</b> will download a Comma Separated Values (CSV) version of your data.<br>
                            - <b>Excel</b> will download a Microsoft Excel (XLSX) version of your data.<br>
                            - <b>PDF</b> will download an Adobe Reader (PDF) version of your data.<br>
                            - <b>Print</b> will make a printer-friendly version and then launch your local printer dialog.<br>
                            <p>
                                It is recommended that you use the <b>Toggle Columns</b> option to gather exactly the columns you want (you may not <i>need</i> everything) before exporting or printing.
                                Please know that regardless of how many entries are showing, these 5 options will gather all filtered entries.
                                For example, if the processing message in the lower left displays "Showing 1 to 25 of 100 entries" your export or print will automatically include all 100 entries - not just the 25 shown.

                            </ul>

                            <?php include "footer.php" ?>
                        </body>
                        </html>

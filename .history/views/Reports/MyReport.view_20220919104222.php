<?php
use \koolreport\widgets\koolphp\Table;
use \koolreport\widgets\koolphp\Card;

?>
<html>
    <head>
    <title>My Report</title>
    </head>
    <body>
        <h1>It works</h1>
        <?php


Card::create(array(
    "value" => 1233,
    "title" => "Month Sale",
));

echo '<br><br><br><br><br>';



Table::create([
    "dataSource" => $this->dataStore("x_ScadCon _SC"),
    "paging"=>array(
        "pageSize"=>25,
        "pageIndex"=>0,
    ),
],);


$report->export()->pdf(array(
    "format" => "A4",
    "orientation" => "landscape",
    "margin" => "1in",
))->toBrowser("myfile.pdf");


?>
    </body>
</html>
 
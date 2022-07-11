<?php

require("../config.php");

$query = "SELECT name, descriptor FROM Category WHERE available = '1' ORDER BY name|placement_order";
$result = dbQuery($query);
$categories = [];
while ($row = dbFetchAssoc($result)) {
    $office['name'] = $row['name'];
    $office['descriptor'] = $row['descriptor'];
    array_push($categories, $office);
}
cook($categories);

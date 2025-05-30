<?php

require("../../../config.php");

$query = "SELECT name, descriptor FROM Category WHERE available = '1' ORDER BY placement_order,name ASC";
$result = dbQuery($query);
$categories = [];

while ($row = dbFetchAssoc($result)) {
    $category = array('name' => $row['name'], 'descriptor' => $row['descriptor']);
    array_push($categories, $category);
}

cook($categories);

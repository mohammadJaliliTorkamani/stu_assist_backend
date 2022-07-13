<?php

require("../../config.php");
require_once('../../forums_utils.php');

$halls = [];
$category = $_GET['category'];

$query = "SELECT id, name, descriptor FROM Hall WHERE category = '$category' AND available = '1' ORDER BY placement_order,name ASC";
$result = dbQuery($query);

while ($row = dbFetchAssoc($result)) {
    $hall['id'] = (int)$row['id'];
    $hall['name'] = $row['name'];
    $hall['descriptor'] = $row['descriptor'];
    $hall['numberOfTopics'] = getNumberOfTopics($row['id']);

    if ($hall['numberOfTopics'] > 0)
        $hall['lastTopic'] = getLastTopic($row['id']);
        
    array_push($halls, $hall);
}

cook($halls);

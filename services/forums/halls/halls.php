<?php

require("../../../config.php");
require_once('../../../utils/forums_utils.php');

$halls = [];
$category = $_GET['category'];

$query = "SELECT id, name, descriptor FROM Hall WHERE category = '$category' AND available = '1' ORDER BY placement_order,name ASC";
$result = dbQuery($query);

while ($row = dbFetchAssoc($result)) {
    $hall = array(
        'id' => (int)$row['id'],
        'name' => $row['name'],
        'descriptor' => $row['descriptor'],
        'numberOfTopics' => getNumberOfTopics($row['id']),
        'lastTopic' => getLastTopic($row['id'])
    );
    array_push($halls, $hall);
}

cook($halls);

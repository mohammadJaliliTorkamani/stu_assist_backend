<?php

require("../../../config.php");
require_once("../../../utils/forums_utils.php");

$hallID = $_GET['hall'];
$halls = [];

$query = "SELECT id, name, descriptor FROM Hall WHERE id = '$hallID' AND available = '1'";
$result = dbQuery($query);
$numberOfRecords = dbNumRows($result);

if ($numberOfRecords > 0) {
    $row = dbFetchAssoc($result);
    $hall = array(
        'id' => (int)$row['id'],
        'name' => $row['name'],
        'descriptor' => $row['descriptor'],
        'numberOfTopics' => getNumberOfTopics($row['id']),
        'lastTopic' =>  getNumberOfTopics($row['id']) > 0 ? getLastTopic($row['id']) : null
    );
}

cook($numberOfRecords > 0 ? $hall : null);

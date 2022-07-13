<?php

require("../../config.php");
require_once("../../forums_utils.php");

$hallID = $_GET['hall'];
$halls = [];

$query = "SELECT id, name, descriptor FROM Hall WHERE id = '$hallID' AND available = '1'";
$result = dbQuery($query);

$row = dbFetchAssoc($result);

$hall['id'] = (int)$row['id'];
$hall['name'] = $row['name'];
$hall['descriptor'] = $row['descriptor'];
$hall['numberOfTopics'] = getNumberOfTopics($row['id']);

if ($hall['numberOfTopics'] > 0)
    $hall['lastTopic'] = getLastTopic($row['id']);

cook($hall);

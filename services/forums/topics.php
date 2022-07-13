<?php

require("../../config.php");
require_once('../../comments_utils.php');

$topics = [];
$hall = $_GET['hall'];

$query = "SELECT Topic.id AS id, Topic.name AS name, Topic.content as content, 
Topic.number_of_views AS number_of_views FROM Topic, Topic_Hall_Relation_Own 
WHERE Topic_Hall_Relation_Own.hall = '$hall' AND Topic.available = '1' AND 
Topic.id = Topic_Hall_Relation_Own.topic";

$result = dbQuery($query);
while ($row = dbFetchAssoc($result)) {
    $topic['id'] = (int)$row['id'];
    $topic['name'] = $row['name'];
    $topic['content'] = $row['content'];
    $topic['numberOfViews'] = (int)$row['number_of_views'];
    $topic['numberOfComments'] = getNumberOfComments((int)$row['id']);
    $topic['lastComment'] = $topic['numberOfComments'] > 0 ? getLastComment((int)$row['id']) : 0;
    array_push($topics, $topic);
}

cook($topics);

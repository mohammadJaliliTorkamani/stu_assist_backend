<?php

require("../../config.php");
require_once('../../utils/comments_utils.php');

$topics = [];
$hall = $_GET['hall'];

$query = "SELECT id, name, content, number_of_views FROM Topic WHERE hall = '$hall' AND available = '1'";

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

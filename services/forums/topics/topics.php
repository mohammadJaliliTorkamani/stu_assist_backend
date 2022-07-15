<?php

require("../../../config.php");
require_once('../../../utils/comments_utils.php');

$topics = [];
$hall = $_GET['hall'];

$query = "SELECT id, name, content, number_of_views FROM Topic WHERE hall = '$hall' AND available = '1'";

$result = dbQuery($query);
while ($row = dbFetchAssoc($result)) {
    $topic = array(
        'id' => (int)$row['id'],
        'name' => $row['name'],
        'content' => $row['content'],
        'numberOfViews' => (int)$row['number_of_views'],
        'numberOfComments' => getNumberOfComments((int)$row['id']),
        'lastComment' => getLastComment((int)$row['id'])
    );
    array_push($topics, $topic);
}

cook($topics);

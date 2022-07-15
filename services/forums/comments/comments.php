<?php

require("../../../config.php");
require_once('../../../utils/comments_utils.php');

$comments = [];
$topicID = $_GET['id'];

$query = "SELECT id, content, creator, creation_date, creation_time 
FROM Comment 
WHERE topic = '$topicID' AND available = '1' 
ORDER BY creation_date, creation_time DESC";

$result = dbQuery($query);

while ($row = dbFetchAssoc($result)) {

    $comment = array(
        'id' => (int)$row['id'],
        'message' => $row['content'],
        'creatorID' => (int)$row['creator'],
        'commentDateTime' => $row['creation_date'] . " " . $row['creation_time']
    );

    array_push($comments, $comment);
}

cook($comments);

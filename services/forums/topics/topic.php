<?php

require("../../../config.php");
require_once('../../../utils/comments_utils.php');

$topicID = $_GET['topic'];

$query = "SELECT name, content, creator, creation_date, creation_time FROM Topic 
WHERE id = '$topicID' AND Topic.available = '1'";

$result = dbQuery($query);
$numberOfRecords = dbNumRows($result);

if ($numberOfRecords > 0) {
    $row = dbFetchAssoc($result);

    $topic = array(
        'id' => (int)$topicID,
        'name' => $row['name'],
        'content' => $row['content'],
        'creatorID' => (int)$row['creator'],
        'postDateTime' => $row['creation_date'] . " " . $row['creation_time']
    );
}

cook($numberOfRecords > 0 ? $topic : null);

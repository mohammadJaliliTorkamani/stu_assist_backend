<?php

require("../../../config.php");
require_once('../../../utils/comments_utils.php');
require_once("../../../utils/user_utils.php");

$topicID = $_GET['topic'];

$query = "SELECT name, content, creator, creation_date, creation_time FROM Topic 
WHERE id = '$topicID' AND Topic.available = '1'";

$result = dbQuery($query);
$numberOfRecords = dbNumRows($result);

if ($numberOfRecords > 0) {
    $row = dbFetchAssoc($result);

    $token = getToken();
    if (!isValid($token))
        $topic = array(
            'id' => (int)$topicID,
            'name' => $row['name'],
            'content' => $row['content'],
            'creatorID' => (int)$row['creator'],
            'liked' => null,
            'postDateTime' => $row['creation_date'] . " " . $row['creation_time']
        );
    else {
        $userPhone = getPhoneNumber($token);
        $query = "SELECT id FROM User_Topic_Relation_Like WHERE user_phone = '$userPhone' AND topic_id = '$topicID'";
        $result = dbQuery($query);
        $liked = dbNumRows($result) == 1 ? true : false;
        $topic = array(
            'id' => (int)$topicID,
            'name' => $row['name'],
            'content' => $row['content'],
            'creatorID' => (int)$row['creator'],
            'liked' => $liked,
            'postDateTime' => $row['creation_date'] . " " . $row['creation_time']
        );
    }
}

cook($numberOfRecords > 0 ? $topic : null);

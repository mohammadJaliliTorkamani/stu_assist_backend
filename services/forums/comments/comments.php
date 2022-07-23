<?php

require("../../../config.php");
require_once('../../../utils/comments_utils.php');
require_once("../../../utils/user_utils.php");

$comments = [];
$topicID = $_GET['id'];

$query = "SELECT id, content, creator, creation_date, creation_time 
FROM Comment 
WHERE topic = '$topicID' AND available = '1' 
ORDER BY creation_date, creation_time DESC";

$result = dbQuery($query);

while ($row = dbFetchAssoc($result)) {
    $token = getToken();
    if (!isValid($token)) {
        $comment = array(
            'id' => (int)$row['id'],
            'message' => $row['content'],
            'liked' => null,
            'creatorID' => (int)$row['creator'],
            'commentDateTime' => $row['creation_date'] . " " . $row['creation_time']
        );
    } else {
        $commentID = (int)$row['id'];
        $userPhone = getPhoneNumber($token);
        $LikeQuery = "SELECT id FROM User_Comment_Relation_Like WHERE user_phone = '$userPhone' AND comment_id = '$commentID'";
        $likeResult = dbQuery($LikeQuery);
        $liked = dbNumRows($likeResult) == 1 ? true : false;
        $comment = array(
            'id' => (int)$row['id'],
            'message' => $row['content'],
            'liked' => $liked,
            'creatorID' => (int)$row['creator'],
            'commentDateTime' => $row['creation_date'] . " " . $row['creation_time']
        );
    }


    array_push($comments, $comment);
}

cook($comments);

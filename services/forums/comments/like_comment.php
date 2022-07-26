<?php

require("../../../config.php");
require_once("../../../utils/user_utils.php");
require_once("../../../utils/comments_utils.php");

$token = getToken();
$commentID = (int)$_POST['id'];
$likeCommand = (int)$_POST['like'];
$currentDate = date('Y-m-d');
$currentTime = date('H:i:s');

if (isValid($token)) {
    if (isCommentExists($commentID)) {
        $userID = getUserID($token);
        if ($likeCommand) {
            $existenceCheckQuery = "SELECT id FROM User_Comment_Relation_Like WHERE user = '$userID' AND comment_id = '$commentID'";
            $existenceCheckResult = dbQuery($existenceCheckQuery);
            if (dbNumRows($existenceCheckResult) == 0)
                $query = "INSERT INTO User_Comment_Relation_Like (user, comment_id, creation_date, creation_time) VALUES ('$userID','$commentID','$currentDate','$currentTime')";
            else {
                cook(null);
                return;
            }
        } else
            $query = "DELETE FROM User_Comment_Relation_Like WHERE user = '$userID' AND comment_id = '$commentID'";

        $result = dbQuery($query);
        if ($result)
            cook(null);
        else
            cook(null, true, 'خطای داخلی سرور');
    } else
        cook(null, true, 'نظر وجود ندارد');
} else
    cook(null, true, 'نشست نامعتبر');

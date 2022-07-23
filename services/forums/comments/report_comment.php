<?php

require("../../../config.php");
require_once("../../../utils/user_utils.php");
require_once("../../../utils/comments_utils.php");

$token = getToken();
$commentID = (int)$_POST['id'];
$reason = $_POST['reason'];
$currentDate = date('Y-m-d');
$currentTime = date('H:i:s');

if (isValid($token)) {
    if (isCommentExists($commentID)) {
        $phone = getPhoneNumber($token);
        $query = "INSERT INTO User_Comment_Relation_Report (user_phone, comment_id, reason, creation_date, creation_time) VALUES ('$phone','$commentID','$reason','$currentDate','$currentTime')";
        $result = dbQuery($query);
        if ($result)
            cook(null);
        else
            cook(null, true, 'Error while operating');
    } else
        cook(null, true, 'No such comment');
} else
    cook(null, true, 'invalid token');

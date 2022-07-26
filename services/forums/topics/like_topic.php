<?php

require("../../../config.php");
require_once("../../../utils/user_utils.php");
require_once("../../../utils/topics_utils.php");

$token = getToken();
$topicID = (int)$_POST['id'];
$likeCommand = (int)$_POST['like'];
$currentDate = date('Y-m-d');
$currentTime = date('H:i:s');

if (isValid($token)) {
    if (isTopicExists($topicID)) {
        $userID = getUserID($token);

        if ($likeCommand) {
            $existenceCheckQuery = "SELECT id FROM User_Topic_Relation_Like WHERE user = '$userID' AND topic_id = '$topicID'";
            $existenceCheckResult = dbQuery($existenceCheckQuery);
            if (dbNumRows($existenceCheckResult) === 0)
                $query = "INSERT INTO User_Topic_Relation_Like (user, topic_id, creation_date, creation_time) VALUES ('$userID','$topicID','$currentDate','$currentTime')";
            else {
                cook(null);
                return;
            }
        } else
            $query = "DELETE FROM User_Topic_Relation_Like WHERE user = '$userID' AND topic_id = '$topicID'";

        $result = dbQuery($query);
        if ($result)
            cook(null);
        else
            cook(null, true, 'خطای داخلی سرور');
    } else
        cook(null, true, 'تاپیک وجود ندارد');
} else
    cook(null, true, 'نشست نامعتبر');

<?php

require("../../../config.php");
require_once("../../../utils/user_utils.php");
require_once("../../../utils/topics_utils.php");

$token = getToken();
$topicIDs = (int)$_POST['id'];
$likeCommand = (int)$_POST['like'];
$currentDate = date('Y-m-d');
$currentTime = date('H:i:s');

if (isValid($token)) {
    if (isTopicExists($topicIDs)) {
        $phone = getPhoneNumber($token);

        if ($likeCommand) {
            $existenceCheckQuery = "SELECT id FROM Topic_User_Relation_Like WHERE user_phone = '$phone' AND topic_id = '$topicIDs'";
            $existenceCheckResult = dbQuery($existenceCheckQuery);
            if (dbNumRows($existenceCheckResult) == 0)
                $query = "INSERT INTO Topic_User_Relation_Like (user_phone, topic_id, creation_date, creation_time) VALUES ('$phone','$topicIDs','$currentDate','$currentTime')";
            else {
                cook(null);
                return;
            }
        } else
            $query = "DELETE FROM Topic_User_Relation_Like WHERE user_phone = '$phone' AND topic_id = '$topicIDs'";

        $result = dbQuery($query);
        if ($result)
            cook(null);
        else
            cook(null, true, 'Error while operating');
    } else
        cook(null, true, 'No such topic');
} else
    cook(null, true, 'invalid token');

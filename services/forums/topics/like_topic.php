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
        $phone = getPhoneNumber($token);

        if ($likeCommand) {
            $existenceCheckQuery = "SELECT id FROM User_Topic_Relation_Like WHERE user_phone = '$phone' AND topic_id = '$topicID'";
            $existenceCheckResult = dbQuery($existenceCheckQuery);
            if (dbNumRows($existenceCheckResult) == 0)
                $query = "INSERT INTO User_Topic_Relation_Like (user_phone, topic_id, creation_date, creation_time) VALUES ('$phone','$topicID','$currentDate','$currentTime')";
            else {
                cook(null);
                return;
            }
        } else
            $query = "DELETE FROM User_Topic_Relation_Like WHERE user_phone = '$phone' AND topic_id = '$topicID'";

        $result = dbQuery($query);
        if ($result)
            cook(null);
        else
            cook(null, true, 'خطای داخلی سرور');
    } else
        cook(null, true, 'تاپیک وجود ندارد');
} else
    cook(null, true, 'نشست نامعتبر');

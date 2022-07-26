<?php

require("../../../config.php");
require_once("../../../utils/user_utils.php");
require_once("../../../utils/topics_utils.php");

$token = getToken();
$topicIDs = (int)$_POST['id'];
$reason = $_POST['reason'];
$currentDate = date('Y-m-d');
$currentTime = date('H:i:s');

if (isValid($token)) {
    if (isTopicExists($topicIDs)) {
        $phone = getPhoneNumber($token);
        $query = "INSERT INTO User_Topic_Relation_Report (user_phone, topic_id, reason, creation_date, creation_time) VALUES ('$phone','$topicIDs','$reason','$currentDate','$currentTime')";
        $result = dbQuery($query);
        if ($result)
            cook(null);
        else
            cook(null, true, 'خطای داخلی سرور');
    } else
        cook(null, true, 'تاپیک وجود ندارد');
} else
    cook(null, true, 'نشست نامعتبر');

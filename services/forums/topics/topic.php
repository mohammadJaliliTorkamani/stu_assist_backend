<?php

require("../../../config.php");
require_once('../../../utils/comments_utils.php');
require_once('../../../utils/topics_utils.php');
require_once("../../../utils/user_utils.php");
require_once("../../../utils/forums_utils.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $content = $_POST['content'];
    $category = $_POST['category'];
    $hallID = $_POST['hall'];

    $token = getToken();

    if (isValid($token)) {
        if (hallExistsInCategory($category, $hallID)) {
            if (isNewTopic($name)) {
                $craetorID = getUserID($token);
                if (hasValidFullName($craetorID)) {
                    $topicID = createTopic($name, $content, $hallID, $craetorID);
                    cook($topicID);
                } else
                    cook(null, true, 'لطفا ابتدا از حساب کاربری خود نام و نام خانوادگی خود را تکمیل نمایید');
            } else
                cook(null, true, 'تاپیک مورد نظر تکراری می باشد');
        } else
            cook(null, true, 'داده های ورودی اشتباه است');
    } else
        cook(null, true, 'نشست نامعتبر');
} else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $topicID = $_GET['topic'];
    $query = "SELECT name, content, creator, creation_date, creation_time FROM Topic WHERE id = '$topicID' AND Topic.available = '1'";
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
                'numberOfLikes' => getNumberOfLikes((int)$topicID),
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
                'numberOfLikes' => getNumberOfLikes((int)$topicID),
                'postDateTime' => $row['creation_date'] . " " . $row['creation_time']
            );
        }
    }

    cook($numberOfRecords > 0 ? $topic : null);
}

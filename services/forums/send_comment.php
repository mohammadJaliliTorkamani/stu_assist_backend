<?php

require("../../config.php");
require_once("../../utils/user_utils.php");

$topicID = $_POST['id'];
$content = $_POST['content'];

function topicExists($topicID)
{
    $query = "SELECT id FROM Topic WHERE id = '$topicID' AND available = '1'";
    $result = dbQuery($query);
    return dbNumRows($result) > 0;
}

function createComment($craetorID, $content, $topicID)
{
    $currentDate = date('Y-m-d');
    $currentTime = date('H:i:s');
    $query = "INSERT INTO Comment(content, creator, topic, creation_date, creation_time) 
    VALUES ('$content', '$craetorID', '$topicID', '$currentDate', '$currentTime')";
    return dbQuery($query);
}

$token = getToken();

if (isValid($token)) {
    if (topicExists($topicID)) {
        $craetorID = getUserID($token);
        if (hasValidFullName($craetorID)) {
            createComment($craetorID, $content, $topicID);
            cook(null);
        } else
                cook(null, true, 'لطفا ابتدا از حساب کاربری خود نام و نام خانوادگی خود را تکمیل نمایید');
    } else {
        cook(null, true, 'داده های ورودی اشتباه است');
    }
} else
    cook(null, true, 'invalid token');

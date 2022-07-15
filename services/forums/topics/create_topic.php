<?php

require("../../../config.php");
require_once("../../../utils/user_utils.php");
require_once("../../../utils/forums_utils.php");

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
    cook(null, true, 'invalid token');

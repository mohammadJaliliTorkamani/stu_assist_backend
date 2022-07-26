<?php

require("../../../config.php");
require_once("../../../utils/user_utils.php");
require_once("../../../utils/forums_utils.php");
require_once("../../../utils/comments_utils.php");

$topicID = $_POST['id'];
$content = $_POST['content'];

$token = getToken();

if (isValid($token)) {
    if (topicExists($topicID)) {
        $craetorID = getUserID($token);
        if (hasValidFullName($craetorID)) {
            createComment($craetorID, $content, $topicID);
            cook(null);
        } else
            cook(null, true, 'لطفا ابتدا از حساب کاربری خود نام و نام خانوادگی خود را تکمیل نمایید');
    } else
        cook(null, true, 'داده های ورودی اشتباه است');
} else
    cook(null, true, 'نشست نامعتبر');

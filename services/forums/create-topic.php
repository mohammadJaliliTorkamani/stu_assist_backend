<?php

require("../../config.php");
require_once("../../utils/user_utils.php");

$name = $_POST['name'];
$content = $_POST['content'];
$category = $_POST['category'];
$hallID = $_POST['hall'];

function isNewTopic($name)
{
    $query = "SELECT name FROM Topic WHERE name = '$name'";
    $result = dbQuery($query);
    return dbNumRows($result) == 0;
}

function hallExistsInCategory($category, $hallID)
{
    $query = "SELECT Hall.id FROM Category, Hall WHERE Category.name = Hall.category AND Category.name = '$category' AND Hall.id = '$hallID'";
    $result = dbQuery($query);
    return dbNumRows($result) > 0;
}

function createTopic($name, $content, $hallID, $craetorID)
{
    $currentDate = date('Y-m-d');
    $currentTime = date('H:i:s');
    $query = "INSERT INTO Topic(name, creator, content, hall, creation_date, creation_time) 
    VALUES ('$name', '$craetorID', '$content', '$hallID', '$currentDate', '$currentTime')";
    dbQuery($query);
    return dbInsertId();
}

$token = getToken();

if (isValid($token)) {
    if (hallExistsInCategory($category, $hallID)) {
        if (isNewTopic($name)) {
            $craetorID = getUserID($token);
            $topicID = createTopic($name, $content, $hallID, $craetorID);
            cook($topicID);
        } else
            cook(null, true, 'تاپیک مورد نظر تکراری می باشد');
    } else {
        cook(null, true, 'داده های ورودی اشتباه است');
    }
} else
    cook(null, true, 'invalid token');

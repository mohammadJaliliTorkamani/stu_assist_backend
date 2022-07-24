<?php

function isTopicExists($topicID)
{
    $query = "SELECT id FROM Topic WHERE id = '$topicID' AND available = '1'";
    $result = dbQuery($query);
    return dbNumRows($result);
}

function getNumberOfLikes($topicID)
{
    $query = "SELECT id FROM User_Topic_Relation_Like WHERE topic_id = '$topicID'";
    $result = dbQuery($query);
    return dbNumRows($result);
}

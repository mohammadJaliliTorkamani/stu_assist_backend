<?php

function isTopicExists($topicID)
{
    $query = "SELECT id FROM Topic WHERE id = '$topicID' AND available = '1'";
    $result = dbQuery($query);
    return dbNumRows($result);
}

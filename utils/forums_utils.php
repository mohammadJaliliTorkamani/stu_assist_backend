<?php

function getLastTopic($hallID)
{
    $lastTopicID = getLastTopicID($hallID);
    $query = "SELECT name, creation_date, creation_time, number_of_views FROM Topic WHERE id = '$lastTopicID' AND available = '1'";
    $result = dbQuery($query);
    $row = dbFetchAssoc($result);
    $topic['id'] = (int)$lastTopicID;
    $topic['name'] = $row['name'];
    $topic['numberOfViews'] = (int)$row['number_of_views'];

    $dateTime = strtotime($row['creation_date'] . " " . $row['creation_time']);
    $currentDateTime = strtotime(date('Y-m-d') . " " . date('H:i:s'));
    $subtractionInMunite = round(abs($currentDateTime - $dateTime) / 60, 2);

    if ($subtractionInMunite < 60)
        $topic['lastTopicDateEquivalent'] = ((int)$subtractionInMunite) . " دقیقه پیش";
    else {
        if ($subtractionInMunite < 24 * 60)
            $topic['lastTopicDateEquivalent'] = ((int)($subtractionInMunite / 60)) . " ساعت پیش";
        else
            $topic['lastTopicDateEquivalent'] = ((int)($subtractionInMunite / (60 * 24))) . " روز پیش";
    }

    return $topic;
}

function getNumberOfTopics($hallID)
{
    $query = "SELECT id FROM Topic WHERE hall = '$hallID'";
    $result = dbQuery($query);
    return dbNumRows($result);
}

function getLastTopicID($hallID)
{
    $query = "SELECT id FROM Topic WHERE hall = '$hallID'  ORDER BY id DESC";
    $result = dbQuery($query);
    return dbFetchAssoc($result)['id'];
}

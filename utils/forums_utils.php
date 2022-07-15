<?php

function getLastTopic($hallID)
{
    $lastTopicID = getLastTopicID($hallID);
    if ($lastTopicID == null) {
        $topic['id'] = -1;
        $topic['name'] = null;
        $topic['numberOfViews'] = -1;
        $topic['lastTopicDateEquivalent'] = null;
    } else {
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
    if (dbNumRows($result) == 0)
        return null;
    return dbFetchAssoc($result)['id'];
}

function topicExists($topicID)
{
    $query = "SELECT id FROM Topic WHERE id = '$topicID' AND available = '1'";
    $result = dbQuery($query);
    return dbNumRows($result) > 0;
}
<?php

function getLastTopic($hallID)
{
    $lastTopicID = getLastTopicID($hallID);
    if ($lastTopicID == null) {
        $topic = array(
            'id' => -1,
            'name' => null,
            'numberOfViews' => -1,
            'lastTopicDateEquivalent' => null
        );
    } else {
        $query = "SELECT name, creation_date, creation_time, number_of_views 
        FROM Topic 
        WHERE id = '$lastTopicID' AND available = '1'";

        $result = dbQuery($query);

        $row = dbFetchAssoc($result);

        $dateTime = strtotime($row['creation_date'] . " " . $row['creation_time']);
        $currentDateTime = strtotime(date('Y-m-d') . " " . date('H:i:s'));
        $subtractionInMunite = round(abs($currentDateTime - $dateTime) / 60, 2);

        $topic = array(
            'id' => (int)$lastTopicID,
            'name' => $row['name'],
            'numberOfViews' => (int)$row['number_of_views'],
            'lastTopicDateEquivalent' => $subtractionInMunite < 60 ? ((int)$subtractionInMunite) . " دقیقه پیش" : ($subtractionInMunite < 24 * 60 ? ((int)($subtractionInMunite / 60)) . " ساعت پیش" : ((int)($subtractionInMunite / (60 * 24))) . " روز پیش"
            )
        );
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

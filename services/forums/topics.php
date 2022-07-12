<?php

require("../../config.php");

$hall = $_GET['hall'];

function getNumberOfComments($topicIdD)
{
    $query = "SELECT id FROM Comment WHERE topic = '$topicIdD'";
    $result = dbQuery($query);
    return dbNumRows($result);
}

function getLastCommentID($topicID){
    $query = "SELECT id FROM Comment WHERE topic = '$topicID'  ORDER BY id DESC";
    $result = dbQuery($query);
    return dbFetchAssoc($result)['id'];
}

function getLastCommentOf($topicID){
    $lastCommentID = getLastCommentID($topicID);
    $query = "SELECT content, creation_date, creation_time FROM Comment WHERE topic = '$topicID' AND available = '1'";
    $result = dbQuery($query);
    $row = dbFetchAssoc($result);
    $comment['id'] = (int)$lastCommentID;
    $comment['content'] = $row['content'];

    $dateTime = strtotime($row['creation_date'] . " " . $row['creation_time']);
    $currentDateTime = strtotime(date('Y-m-d') . " " . date('H:i:s'));
    $subtractionInMunite = round(abs($currentDateTime - $dateTime) / 60, 2);

    if ($subtractionInMunite < 60)
        $comment['lastCommentDateEquivalent'] = ((int)$subtractionInMunite) . " دقیقه پیش";
    else {
        if ($subtractionInMunite < 24 * 60)
            $comment['lastCommentDateEquivalent'] = ((int)($subtractionInMunite / 60)) . " ساعت پیش";
        else
            $comment['lastCommentDateEquivalent'] = ((int)($subtractionInMunite / (60 * 24))) . " روز پیش";
    }

    return $comment;
}

$query = "SELECT Topic.id AS id, Topic.name AS name, Topic.content as content FROM Topic, Topic_Hall_Relation_Own WHERE Topic_Hall_Relation_Own.hall = '$hall' AND
Topic.available = '1' AND Topic.id = Topic_Hall_Relation_Own.topic";
$result = dbQuery($query);
$topics = [];
while ($row = dbFetchAssoc($result)) {
    $topic['id'] = (int)$row['id'];
    $topic['name'] = $row['name'];
    $topic['content'] = $row['content'];

    $topic['numberOfComments'] = getNumberOfComments((int)$row['id']);
    $topic['lastComment'] = $topic['numberOfComments'] > 0 ? getLastCommentOf((int)$row['id']) : 0;

    array_push($topics, $topic);
}

cook($topics);

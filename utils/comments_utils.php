<?php

function getNumberOfComments($topicIdD)
{
    $query = "SELECT id FROM Comment WHERE topic = '$topicIdD'";
    $result = dbQuery($query);
    return dbNumRows($result);
}

function getLastComment($topicID)
{
    $query = "SELECT User.id AS id, User.name AS fn, User.last_name AS ln, Comment.creation_date AS creation_date, 
    Comment.creation_time AS creation_time FROM Comment, User WHERE User.id = Comment.creator AND Comment.topic = '$topicID' AND available = '1'";
    $result = dbQuery($query);
    $numberOfRecords = dbNumRows($result);

    $comment = array(
        'creatorID' => -1,
        'creator' => null,
        'lastCommentDateEquivalent' => null
    );

    if ($numberOfRecords > 0) {
        $row = dbFetchAssoc($result);

        $dateTime = strtotime($row['creation_date'] . " " . $row['creation_time']);
        $currentDateTime = strtotime(date('Y-m-d') . " " . date('H:i:s'));
        $subtractionInMunite = round(abs($currentDateTime - $dateTime) / 60, 2);

        $comment = array(
            'creatorID' => (int)$row['id'],
            'creator' => $row['fn'] . " " . $row['ln'],
            'lastCommentDateEquivalent' => $subtractionInMunite < 60 ?  ((int)$subtractionInMunite) . " دقیقه پیش" : ($subtractionInMunite < 24 * 60 ? ((int)($subtractionInMunite / 60)) . " ساعت پیش" : ((int)($subtractionInMunite / (60 * 24))) . " روز پیش")
        );
    }
    return $comment;
}

function createComment($craetorID, $content, $topicID)
{
    $currentDate = date('Y-m-d');
    $currentTime = date('H:i:s');
    $query = "INSERT INTO Comment(content, creator, topic, creation_date, creation_time) 
    VALUES ('$content', '$craetorID', '$topicID', '$currentDate', '$currentTime')";
    return dbQuery($query);
}

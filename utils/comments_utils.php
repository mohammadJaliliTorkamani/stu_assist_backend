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
    $row = dbFetchAssoc($result);

    $comment['creatorID'] = -1;
    $comment['creator'] = null;
    $comment['lastCommentDateEquivalent'] = null;

    $numberOfRecords = dbNumRows($result);
    if ($numberOfRecords > 0) {
        $comment['creatorID'] = (int)$row['id'];
        $comment['creator'] = $row['fn'] . " " . $row['ln'];
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
    }
    return $comment;
}

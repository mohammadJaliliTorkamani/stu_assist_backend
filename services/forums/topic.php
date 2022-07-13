<?php

require("../../config.php");
require_once('../../utils/comments_utils.php');

$topicID = $_GET['topic'];

$query = "SELECT name, content, creator, creation_date, creation_time FROM Topic 
WHERE id = '$topicID' AND Topic.available = '1'";

$result = dbQuery($query);
$row = dbFetchAssoc($result);
$topic['id'] = (int)$topicID;
$topic['name'] = $row['name'];
$topic['content'] = $row['content'];
$topic['creatorID'] = (int)$row['creator'];
$topic['postDateTime'] = $row['creation_date'] . " " . $row['creation_time'];

cook($topic);

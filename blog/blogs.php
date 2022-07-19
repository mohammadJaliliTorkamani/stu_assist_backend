<?php

require("../config.php");
require_once('../utils/blog_utils.php');

$query = "SELECT * FROM BlogCategory";
$result = dbQuery($query);
$categoryPosts = [];

while ($row = dbFetchAssoc($result)) {
    $record['CategoryID'] = (int)$row['id'];
    $record['CategoryName'] = $row['name'];
    $record['posts'] = getPostsOfCategory((int)$row['id']);

    array_push($categoryPosts, $record);
}

cook($categoryPosts);

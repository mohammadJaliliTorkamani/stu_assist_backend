<?php

require_once('../utils/blog_utils.php');
$query = "SELECT * FROM BlogCategory";
$result = dbQuery($query);
$categoryPosts = [];
while ($row = dbFetchAssoc($result)) {
    $record['CategoryID'] = (int)$row['id'];
    $record['CategoryName'] = $row['name'];
    $posts = getPostsOfCategory((int)$row['id']);
    $record['posts'] = $posts;
    array_push($record, $categoryPosts);
}

cook($categoryPosts);

<?php

require("../config.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ceil = !isset($_GET['capacity']) ? 5 : $_GET['capacity'];

    $query = "SELECT BlogPost.title, overview, content, Photo.path as path, 
    BlogPost.creation_date as CraetionDate, BlogPost.creation_time as CraetionTime 
    FROM BlogPost, Photo 
    WHERE BlogPost.photo = Photo.id AND BlogPost.available = '1' 
    ORDER BY BlogPost.id DESC 
    LIMIT $ceil";

    $result = dbQuery($query);
    $posts = [];

    while ($row = dbFetchAssoc($result))
        array_push($posts, $row);

    cook($posts);
} else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $BlogPostID = $_GET['id'];
    $query = "SELECT BlogPost.title, overview, content, Photo.path as photoPath, 
    BlogPost.creation_date as creationDate, BlogPost.creation_time as creationTime 
    FROM BlogPost, Photo 
    WHERE BlogPost.photo = Photo.id AND BlogPost.id = '$BlogPostID' AND BlogPost.available = '1'";

    $result = dbQuery($query);
    $row = dbFetchAssoc($result);

    cook($row);
}

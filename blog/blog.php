<?php

require("../config.php");


$BlogPostID = $_GET['id'];
$query = "SELECT BlogPost.title, overview, content, Photo.path as photoPath, 
    BlogPost.creation_date as creationDate, BlogPost.creation_time as creationTime 
    FROM BlogPost, Photo 
    WHERE BlogPost.photo = Photo.id AND BlogPost.id = '$BlogPostID' AND BlogPost.available = '1'";

$result = dbQuery($query);
$row = dbFetchAssoc($result);

cook($row);

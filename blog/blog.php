<?php

require("../config.php");

$BlogPostID = $_GET['id'];
$query = "SELECT BlogPost.title, overview, content, Photo.path as path, 
BlogPost.creation_date as CraetionDate, BlogPost.creation_time as CraetionTime 
FROM BlogPost, Photo 
WHERE BlogPost.photo = Photo.id AND BlogPost.id = '$BlogPostID' AND BlogPost.available = '1'";

$result = dbQuery($query);
$row = dbFetchAssoc($result);

cook($row);

<?php

function getPostsOfCategory($categoryID)
{
    $query = "SELECT blog_post
    FROM BlogPost,_BlogPost_BlogCategory_Relation 
    WHERE blog_category = '$categoryID' 
    AND BlogPost.id = _BlogPost_BlogCategory_Relation.blog_post 
    AND BlogPost.available = '1'";

    $postsQuery = "SELECT BlogPost.id, BlogPost.title, overview, Photo.path as photoPath, BlogPost.creation_date as craetionDate, BlogPost.creation_time as craetionTime 
    FROM BlogPost, Photo 
    WHERE BlogPost.id IN (" . $query . ")";
    $result = dbQuery($postsQuery);
    $posts = [];

    while ($row = dbFetchAssoc($result))
        array_push($posts, $row);

    return $posts;
}

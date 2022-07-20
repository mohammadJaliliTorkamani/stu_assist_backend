<?php

function getPostsOfCategory($categoryID)
{
    $query = "SELECT blog_post
    FROM BlogPost,Relation_BlogPost_BlogCategory 
    WHERE blog_category = '$categoryID' 
    AND BlogPost.id = Relation_BlogPost_BlogCategory.blog_post 
    AND BlogPost.available = '1'";

    $postsQuery = "SELECT BlogPost.title, overview, Photo.path as path, BlogPost.creation_date as CraetionDate, BlogPost.creation_time as CraetionTime 
    FROM BlogPost, Photo 
    WHERE BlogPost.id IN (" . $query . ")";
    $result = dbQuery($postsQuery);
    $posts = [];

    while ($row = dbFetchAssoc($result))
        array_push($posts, $row);

    return $posts;
}

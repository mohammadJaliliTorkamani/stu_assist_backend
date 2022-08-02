<?php

require("../config.php");
require_once('../utils/blog_utils.php');

$title = $_POST['title'];
$overview = $_POST['overview'];
$content = $_POST['content'];
$blogCategoryID = $_POST['blog_category_id'];
$available = !isset($_POST['available']) ? 1 : $_POST['available'];

$info = pathinfo($_FILES['photo']['name']);

function completeBlogPost($blogPostID, $photoID, $blogCategoryID)
{
    $query = "UPDATE BlogPost SET photo = '$photoID' WHERE id = '$blogPostID'";
    dbQuery($query);

    $query = "INSERT INTO BlogPost_BlogCategory_Relation (blog_post, blog_category) 
    VALUES ('$blogPostID','$blogCategoryID')";

    return dbQuery($query);
}

function uploadAndInsertPhoto($blogPostID, $newname, $file)
{
    $currentDate = date('Y-m-d');
    $currentTime = date('H:i:s');

    $target = '../../media/blogposts_photos/' . $newname;
    move_uploaded_file($file, $target);

    $absolutePath = "https://stu-assist.ir/media/blogposts_photos/" . $newname;
    $photoTitle = "Blog Post Photo - " . $blogPostID;

    $query = "INSERT INTO Photo (title,path,creation_date,creation_time) 
    VALUES ('$photoTitle','$absolutePath','$currentDate','$currentTime')";
    dbQuery($query);

    return dbInsertId();
}

function sendToTelegramChannel($title, $overview, $postURL, $photoID)
{
    $APIToken = "5454811194:AAHDkD9LaDHYxfKbIDmJwkkOPacgNpslcC8";

    $query = "SELECT path FROM Photo WHERE id = '$photoID'";
    $result = dbQuery($query);
    $photoPath = dbFetchAssoc($result)['path'];

    $chat_id = '@stu_assist';
    $caption = $title . "\n" . $overview . $postURL;

    $bot_url    = "https://api.telegram.org/bot$APIToken/";
    $url        = $bot_url . "sendPhoto?chat_id=" . $chat_id;

    $post_fields = array(
        // 'chat_id'   => $chat_id,
        'photo'     => new CURLFile($photoPath)
    );

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "Content-Type:multipart/form-data"
    ));
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

    $output = curl_exec($ch);
    echo curl_error($ch) . "\n";
    return json_encode($output);
}

$blogPostID = createInitialBlogPost($title, $overview, $content, $available, $blogCategoryID);
$newname = "Blog_Post" . $blogPostID . "." . $info['extension'];;
$photoID = uploadAndInsertPhoto($blogPostID, $newname, $_FILES['photo']['tmp_name']);


echo sendToTelegramChannel($title, $overview, "https://stu-assist.ir/blogs/" . $blogPostID, $photoID);

if (completeBlogPost($blogPostID, $photoID, $blogCategoryID))
    echo "Done";
else
    echo "Failed";

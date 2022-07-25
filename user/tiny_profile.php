<?php

require("../config.php");

$userID = $_GET['id'];

$query = "SELECT name, last_name, profile_photo FROM User WHERE id = '$userID'";
$result = dbQuery($query);
$row = dbFetchAssoc($result);

$user = array('fullName' => ($row['name'] === null ? "" : $row['name']) . " " . ($row['last_name'] === null ? "" : $row['last_name']));

$profilePhotoID = (int)$row['profile_photo'];
if ($profilePhotoID !== -1) {
    $photoQuery = "SELECT path FROM Photo where id = '$profilePhotoID'";
    $photoResult = dbQuery($photoQuery);
    $photo = dbFetchAssoc($photoResult);
    $user['photo'] = $photo;
} else
    $user['photo'] = null;

cook($user);

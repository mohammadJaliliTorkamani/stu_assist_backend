<?php

require("../config.php");

$username = $_POST['username'];
$password = $_POST['password'];

function createToken($token, $userID)
{
    $currentDate = date('Y-m-d');
    $currentTime = date('H:i:s');
    $expirationDate = date('Y-m-d', strtotime('+7 days'));
    $expirationTime = date('H:i:s');

    $query = "INSERT INTO Token(value, user, expiration_date, expiration_time, is_valid, creation_date, creation_time)
    VALUES('$token', '$userID', '$expirationDate','$expirationTime','1','$currentDate','$currentTime' )";
    $result = dbQuery($query);
    if ($result == TRUE)
        cook($token);
    else
        cook(null, true, 'خطای داخلی سرور');
}

function createLoginCredential($userID, $username)
{
    $currentDate = date('Y-m-d');
    $currentTime = date('H:i:s');
    $flavor = $userID . "#Mohammad" . $currentDate . "#Mohammad" . $currentTime . "#Mohammad" . $username;
    $token = hash('sha256', $flavor);
    createToken($token, $userID);
}

$result = dbQuery("SELECT id, username, password FROM User WHERE type = '1'");

while ($row = dbFetchAssoc($result)) {
    $_userID = $row['id'];
    $_username = $row['username'];
    $_passwordHash = hash('sha256', $row['password']);
    if ($username === $_username && $password === $_passwordHash) {
        createLoginCredential($_userID, $username);
        return;
    }
}

cook(null, true, 'کاربر وجود ندارد');

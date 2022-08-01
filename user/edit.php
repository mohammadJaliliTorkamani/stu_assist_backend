<?php

require("../config.php");
require_once('../utils/user_utils.php');

$firstName = $_POST['firstName'];
$lastName = $_POST['lastName'];
$biography = $_POST['biography'];
$country = $_POST['country'];
$state = $_POST['state'];

$token = getToken();

if (isValid($token)) {
    $userID = getUserID($token);
    $query = "UPDATE User SET name = '$firstName', 
    last_name = '$lastName', 
    biography = '$biography' WHERE id = '$userID'";
    dbQuery($query);

    $query = "UPDATE Address SET country = '$country', 
    state = '$state' 
    WHERE id IN (SELECT address FROM User where id = '$userID')";
    dbQuery($query);
    cook(null, false, 'به روز رسانی انجام شد');
} else
    cook(null, true, 'نشست نامعتبر');

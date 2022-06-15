<?php

require("../config.php");
require_once('../user_utils.php');

$fullName = trim($_GET['fullName']," ");
$fullNameArray = explode(' ', $fullName);
$name = $fullNameArray[0];
$lastName = '';
if (count($fullNameArray) > 1)
    $lastName = join(' ', array_slice($fullNameArray, 1));

$token = getToken();

function isValidFullName()
{
    global $fullNameArray;
    return count($fullNameArray) >= 2;
}

if (isValid($token)) {
    if (isValidFullName()) {
        $phoneNumber = getPhoneNumber($token);
        $query = "UPDATE User SET name = '$name', last_name = '$lastName' WHERE phone = '$phoneNumber'";
        dbQuery($query);
        cook(null, false, 'به روز رسانی انجام شد');
    } else
        cook(null, true, 'فرمت مقدار ارسالی نامعتبر است');
} else
    cook(null, true, 'invalid token');

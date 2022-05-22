<?php

require("../config.php");

$OTP = $_POST['otp_code'];
$phoneNumber = $_POST['phone_number'];
$currentDate = date('Y-m-d');
$currentTime = date('H:i:s');
$expirationDate = date('Y-m-d', strtotime('+10 year'));
$expirationTime = date('H:i:s');

function createToken($token)
{
    global $phoneNumber, $expirationDate, $expirationTime, $currentDate, $currentTime, $OTP;
    $query = "INSERT INTO Token(value, user_phone, expiration_date, expiration_time, is_valid, creation_date, creation_time)
    VALUES('$token', '$phoneNumber', '$expirationDate','$expirationTime','1','$currentDate','$currentTime' )";
    $result = dbQuery($query);
    if ($result == TRUE) {
        $sql = "UPDATE User SET type = '1' WHERE phone = '$phoneNumber'";
        dbQuery($sql);
        $sql = "DELETE FROM OTP Where user_phone = '$phoneNumber' AND value = '$OTP'";
        dbQuery($sql);
        sendResponseCode();
    } else {
        sendResponseCode(false);
    }
}



$sql = "SELECT expiration_date, expiration_time FROM OTP WHERE user_phone = '$phoneNumber' and value = '$OTP'";
$result = dbQuery($sql);

if (dbNumRows($result) > 0) {
    $row = dbFetchAssoc($result);
    $OTP_expirationDate = strtotime($row['expiration_date']);
    $OTP_expirationTime = strtotime($row['expiration_time']);

    if (($OTP_expirationDate > strtotime($currentDate))
        || (($OTP_expirationDate == strtotime($currentDate)) && ($OTP_expirationTime > strtotime($currentTime)))
    ) {
        $flavor = $phoneNumber . $currentDate . $currentTime . $OTP;
        $token = hash('sha256', $flavor);
        createToken($token);
        sendResponseCode();
    } else {
        sendResponseCode(false);
    }
} else {
    sendResponseCode(false);
}

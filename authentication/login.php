<?php

require("../config.php");
header('Access-Control-Allow-Origin: *'); //is needed for local port communications
header("Access-Control-Allow-Headers: Content-Type");


$phoneNumber = $_POST['phone_number'];
$OTP_digits = 5;
$currentDate = date('Y-m-d');
$currentTime = date('H:i:s');
$expirationDate = date('Y-m-d', strtotime('+1 day'));
$expirationTime = date('H:i:s');
$OTPCode = rand(pow(10, $OTP_digits - 1), pow(10, $OTP_digits) - 1);

function createWallet()
{
    $wallet_query = "INSERT INTO Wallet(balance) VALUES ('0')";
    dbQuery($wallet_query);
    return dbInsertId();
}

function createUser($walletID)
{
    global $phoneNumber, $currentDate, $currentTime;
    $user_query = "INSERT INTO User(phone, wallet_id, creation_date, creation_time)
    VALUES('$phoneNumber','$walletID','$currentDate','$currentTime')";
    $result = dbQuery($user_query);
}

function createOTP()
{
    global $OTPCode, $phoneNumber, $expirationDate, $expirationTime, $currentDate, $currentTime;
    $query = "INSERT INTO OTP(value, user_phone, expiration_date, expiration_time, creation_date, creation_time)
     VALUES('$OTPCode', '$phoneNumber', '$expirationDate','$expirationTime','$currentDate','$currentTime' )";
    return dbQuery($query);
}

$sql = "SELECT * FROM User WHERE phone = '$phoneNumber' and type = '1'";
$result = dbQuery($sql);

if (dbNumRows($result) == 0) {
    $wallet_id = createWallet();
    createUser($wallet_id);
}

$result = createOTP();

if ($result === TRUE) {
    sendResponseCode();
} else {
    sendResponseCode(false);
}
exit;

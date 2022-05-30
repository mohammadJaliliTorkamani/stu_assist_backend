<?php

require("../config.php");
require_once("../sms_service.php");

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
    return dbQuery($user_query);
}

function createOTP()
{
    global $OTPCode, $phoneNumber, $expirationDate, $expirationTime, $currentDate, $currentTime;

    dbQuery("DELETE FROM OTP WHERE user_phone = '$phoneNumber'");
    $query = "INSERT INTO OTP(value, user_phone, expiration_date, expiration_time, creation_date, creation_time)
     VALUES('$OTPCode', '$phoneNumber', '$expirationDate','$expirationTime','$currentDate','$currentTime' )";
    return dbQuery($query);
}

function cleanUp()
{
    global $phoneNumber;
    dbQuery("DELETE FROM Wallet WHERE id IN (SELECT Wallet.id FROM Wallet,User WHERE User.wallet_id = Wallet.id AND User.phone = '$phoneNumber' AND User.type = '0')");
    dbQuery("DELETE FROM User WHERE phone = '$phoneNumber' AND type = '0'");
}

function sendSMS()
{
    global $phoneNumber, $OTPCode;

    try {
        date_default_timezone_set("Asia/Tehran");

        // your sms.ir panel configuration
        $APIKey = "30cc1df5415d4e2361c82a02";

        $SecretKey = "KimiaMohammad_L95";
        $LineNumber = "30002101001825";
        $APIURL = "https://ws.sms.ir/";

        // your mobile numbers
        $MobileNumbers = array($phoneNumber);


        // your text messages
        $Messages = array('با تشکر از ثبت نام شما، کد فعالسازی عبارت است از : ' . $OTPCode . "    Stu-Assist.ir");

        // sending date
        $SendDateTime = date("Y-m-d") . "T" . date("H:i:s");

        $SmsIR_SendMessage = new SmsIR_SendMessage($APIKey, $SecretKey, $LineNumber, $APIURL);
        $SendMessage = $SmsIR_SendMessage->sendMessage($MobileNumbers, $Messages, $SendDateTime);
        var_dump($SendMessage);
    } catch (Exception $e) {
    }
}

$result = dbQuery("SELECT * FROM User WHERE phone = '$phoneNumber' and type = '1'");

$isNewUser = false;
if (dbNumRows($result) == 0) {
    cleanUp();
    $wallet_id = createWallet();
    createUser($wallet_id);
    $isNewUser = true;
}

if (createOTP()) {
    sendSMS();
    cook(null, false, 'OTP was sent to ' . ($isNewUser ? 'a new user' : 'the existing user'));
} else
    cook(null, true, 'Something went wrong');

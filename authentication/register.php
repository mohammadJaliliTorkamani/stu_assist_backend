<?php

require("../config.php");
require_once("../sms_service.php");

$username = $_POST['username'];
$password = $_POST['password'];
$firstName = $_POST['first_name'];
$lastName = $_POST['last_name'];
$bio = $_POST['bio'] === null || trim($_POST['bio']) === '' ? NULL : $_POST['bio'];
$phone = $_POST['phone'];
$country = $_POST['country'];
$state = $_POST['state'];
$INITIAL_WALLET_BALANCE_RIALS = 20000;

function createWallet($initBalance)
{
    $wallet_query = "INSERT INTO Wallet(balance) VALUES ('$initBalance')";
    dbQuery($wallet_query);
    return dbInsertId();
}

function createAddress($country, $state)
{
    $address_query = "INSERT INTO Address(country, state) VALUES ('$country','$state')";
    dbQuery($address_query);
    return dbInsertId();
}

function createUser($phone, $firstName, $lastName, $username, $password, $bio, $addressID, $walletID)
{
    $currentDate = date('Y-m-d');
    $currentTime = date('H:i:s');
    $query = "INSERT INTO User(phone, name, last_name, username, password, biography, type, address, wallet_id, creation_date, creation_time)
    VALUES('$phone','$firstName','$lastName','$username','$password','$bio','0','$addressID','$walletID','$currentDate','$currentTime')";
    if (dbQuery($query))
        return dbInsertId();
    return -1;
}


function sendOTP($userID, $phone)
{
    $OTPCode = createOTP($userID);
    sendSMS($phone, $OTPCode);
}

function createOTP($userID)
{
    $currentDate = date('Y-m-d');
    $currentTime = date('H:i:s');
    $expirationDate = date('Y-m-d', strtotime('+1 day'));
    $expirationTime = date('H:i:s');
    $OTP_digits = 5;
    $OTPCode = rand(pow(10, $OTP_digits - 1), pow(10, $OTP_digits) - 1);

    dbQuery("DELETE FROM OTP WHERE user = '$userID'");
    $query = "INSERT INTO OTP(value, user, expiration_date, expiration_time, creation_date, creation_time)
     VALUES('$OTPCode', '$userID', '$expirationDate','$expirationTime','$currentDate','$currentTime' )";
    dbQuery($query);
    return $OTPCode;
}

function sendSMS($phone, $OTPCode)
{
    try {
        date_default_timezone_set("Asia/Tehran");

        // your sms.ir panel configuration
        $APIKey = "30cc1df5415d4e2361c82a02";
        $SecretKey = "KimiaMohammad_L95";
        $APIURL = "https://ws.sms.ir/";
        $templateID = "68425";

        // message data
        $data = array(
            "ParameterArray" => array(
                array(
                    "Parameter" => "VerificationCode",
                    "ParameterValue" => $OTPCode
                )
            ),
            "Mobile" => $phone,
            "TemplateId" => $templateID
        );

        $SmsIR_UltraFastSend = new SmsIR_UltraFastSend($APIKey, $SecretKey, $APIURL);
        $UltraFastSend = $SmsIR_UltraFastSend->ultraFastSend($data);
        // var_dump($UltraFastSend);
    } catch (Exception $e) {
        // echo 'Error UltraFastSend : '.$e->getMessage();
    }
}

$result1 = dbQuery("SELECT id FROM User WHERE username = '$username' AND type = '1'");
$result2 = dbQuery("SELECT id FROM User WHERE phone = '$phone' AND type = '1'");

if (dbNumRows($result1) > 0)
    cook(null, true, 'نام کاربری رزرو شده است');
else  if (dbNumRows($result2) > 0)
    cook(null, true, 'شما قبلا با این شماله تلفن در سامانه ثبت نام کرده اید');
else {
    $walletID = createWallet($INITIAL_WALLET_BALANCE_RIALS);
    $addressID = createAddress($country, $state);
    $userID = createUser($phone, $firstName, $lastName, $username, $password, $bio, $addressID, $walletID);
    if ($userID !== -1) {
        sendOTP($userID, $phone);
        cook("کد فعالسازی به شماره تلفن ارسال شد");
    } else
        cook(null, true, 'خطای داخلی سرور');
}

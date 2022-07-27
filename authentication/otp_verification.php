<?php

require("../config.php");
require_once("../sms_service.php");
require_once('../utils/user_utils.php');

$OTP = $_POST['otp_code'];
$phoneNumber = $_POST['phone_number'];
$currentDate = date('Y-m-d');
$currentTime = date('H:i:s');
$expirationDate = date('Y-m-d', strtotime('+10 year'));
$expirationTime = date('H:i:s');

$OTP_digits = 5;
$expirationDate = date('Y-m-d', strtotime('+1 day'));
$expirationTime = date('H:i:s');
$OTPCode = rand(pow(10, $OTP_digits - 1), pow(10, $OTP_digits) - 1);

// function createWallet()
// {
//     $wallet_query = "INSERT INTO Wallet(balance) VALUES ('20000')";
//     dbQuery($wallet_query);
//     return dbInsertId();
// }

// function createUser($walletID)
// {
//     global $phoneNumber, $currentDate, $currentTime;
//     $user_query = "INSERT INTO User(phone, wallet_id, creation_date, creation_time)
//     VALUES('$phoneNumber','$walletID','$currentDate','$currentTime')";
//     return dbQuery($user_query);
// }

// function createOTP()
// {
//     global $OTPCode, $phoneNumber, $expirationDate, $expirationTime, $currentDate, $currentTime;
//     $userID = getUserIDFromPhone($phoneNumber);
//     dbQuery("DELETE FROM OTP WHERE user = '$userID'");
//     $query = "INSERT INTO OTP(value, user, expiration_date, expiration_time, creation_date, creation_time)
//      VALUES('$OTPCode', '$userID', '$expirationDate','$expirationTime','$currentDate','$currentTime' )";
//     return dbQuery($query);
// }

// function cleanUp()
// {
//     global $phoneNumber;
//     dbQuery("DELETE FROM Wallet WHERE id IN (SELECT Wallet.id FROM Wallet,User WHERE User.wallet_id = Wallet.id AND User.phone = '$phoneNumber' AND User.type = '0')");
//     dbQuery("DELETE FROM User WHERE phone = '$phoneNumber' AND type = '0'");
// }

// function sendSMS()
// {
//     global $phoneNumber, $OTPCode;

//     try {
//         date_default_timezone_set("Asia/Tehran");

//         // your sms.ir panel configuration
//         $APIKey = "30cc1df5415d4e2361c82a02";
//         $SecretKey = "KimiaMohammad_L95";
//         $APIURL = "https://ws.sms.ir/";
//         $templateID = "66723";

//         // message data
//         $data = array(
//             "ParameterArray" => array(
//                 array(
//                     "Parameter" => "VerificationCode",
//                     "ParameterValue" => $OTPCode
//                 )
//             ),
//             "Mobile" => $phoneNumber,
//             "TemplateId" => $templateID
//         );

//         $SmsIR_UltraFastSend = new SmsIR_UltraFastSend($APIKey, $SecretKey, $APIURL);
//         $UltraFastSend = $SmsIR_UltraFastSend->ultraFastSend($data);
//         var_dump($UltraFastSend);
//     } catch (Exception $e) {
//         // echo 'Error UltraFastSend : '.$e->getMessage();
//     }
// }


function createToken($token)
{
    global $phoneNumber, $expirationDate, $expirationTime, $currentDate, $currentTime, $OTP;
    $userID = getUserIDFromPhone($phoneNumber);
    $query = "INSERT INTO Token(value, user, expiration_date, expiration_time, is_valid, creation_date, creation_time)
    VALUES('$token', '$userID', '$expirationDate','$expirationTime','1','$currentDate','$currentTime' )";
    $result = dbQuery($query);
    if ($result == TRUE) {
        dbQuery("UPDATE User SET type = '1' WHERE phone = '$phoneNumber'");
        dbQuery("DELETE FROM OTP Where user = '$userID' AND value = '$OTP'");
        cook($token);
    } else
        cook(null, true, 'خطای داخلی سرور');
}

$userID = getUserIDFromPhone($phoneNumber);
$sql = "SELECT expiration_date, expiration_time FROM OTP WHERE user = '$userID' and value = '$OTP'";
$result = dbQuery($sql);

if (dbNumRows($result) > 0) {
    $row = dbFetchAssoc($result);
    $OTP_expirationDate = strtotime($row['expiration_date']);
    $OTP_expirationTime = strtotime($row['expiration_time']);

    if (($OTP_expirationDate > strtotime($currentDate)) || (($OTP_expirationDate == strtotime($currentDate)) && ($OTP_expirationTime > strtotime($currentTime)))) {
        $flavor = $phoneNumber . "#Mohammad" . $currentDate . "#Mohammad" . $currentTime . "#Mohammad" . $OTP;
        $token = hash('sha256', $flavor);
        createToken($token);
    } else
        cook(null, true, 'کد فعالسازی منقضی شده است');
} else
    cook(null, true, 'کد فعالسازی یافت نشد');

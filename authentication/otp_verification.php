<?php

require("../config.php");
require_once('../utils/user_utils.php');

$OTP = $_POST['otp_code'];
$phoneNumber = $_POST['phone'];
$currentDate = date('Y-m-d');
$currentTime = date('H:i:s');

function cleanUp($userID)
{
    global $phoneNumber;
    dbQuery("DELETE FROM Wallet WHERE id IN (SELECT Wallet.id FROM Wallet,User WHERE User.wallet_id = Wallet.id AND User.phone = '$phoneNumber' AND User.type = '0')");
    dbQuery("DELETE FROM User WHERE phone = '$phoneNumber' AND type = '0'");
    dbQuery("DELETE FROM OTP WHERE user = '$userID'");
}

function activateUser($userID)
{
    dbQuery("UPDATE User SET type = '1' WHERE id = '$userID'");
}

$userID = getUserIDFromPhone($phoneNumber);
$sql = "SELECT expiration_date, expiration_time FROM OTP WHERE user = '$userID' and value = '$OTP'";
$result = dbQuery($sql);

if (dbNumRows($result) > 0) {
    $row = dbFetchAssoc($result);
    $OTP_expirationDate = strtotime($row['expiration_date']);
    $OTP_expirationTime = strtotime($row['expiration_time']);

    if (($OTP_expirationDate > strtotime($currentDate)) || (($OTP_expirationDate == strtotime($currentDate)) && ($OTP_expirationTime > strtotime($currentTime)))) {
        activateUser($userID);
        cleanUp($userID);
        cook("فعالسازی انجام شد");
    } else
        cook(null, true, 'کد فعالسازی منقضی شده است');
} else
    cook(null, true, 'کد فعالسازی یافت نشد');

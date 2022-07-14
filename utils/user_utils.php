<?php

function getToken()
{
    $headers = getallheaders();
    $val = $headers['Authorization'];
    return trim(substr($val, 7));
}

function isValid($token)
{

    if ($token === null || $token === '')
        return false;

    $currentDate = date('Y-m-d');
    $currentTime = date('H:i:s');
    $result = dbQuery("SELECT expiration_date, expiration_time from Token WHERE value = '$token' AND is_valid = '1'");
    if (dbNumRows($result) > 0) {
        $row = dbFetchAssoc($result);
        $OTP_expirationDate = strtotime($row['expiration_date']);
        $OTP_expirationTime = strtotime($row['expiration_time']);
        return ($OTP_expirationDate > strtotime($currentDate)) || (($OTP_expirationDate == strtotime($currentDate)) && ($OTP_expirationTime > strtotime($currentTime)));
    }
}

function getPhoneNumber($token)
{
    $result = dbQuery("SELECT user_phone FROM Token WHERE value = '$token'");
    return dbFetchAssoc($result)['user_phone'];
}

function getUserID($token)
{
    $result = dbQuery("SELECT User.id AS id FROM User, Token WHERE Token.value = '$token'");
    return dbFetchAssoc($result)['id'];
}

function getWalletID($token)
{
    $result = dbQuery("SELECT User.wallet_id FROM Token, User WHERE value = '$token' AND User.phone = Token.user_phone");
    return dbFetchAssoc($result)['wallet_id'];
}

function getBalance($token)
{
    $query = "SELECT balance FROM Token, User, Wallet 
    WHERE Token.value = '$token' AND Token.user_phone = User.phone AND Wallet.id = User.wallet_id";
    $result = dbQuery($query);
    return intval(dbFetchAssoc($result)['balance']);
}

function updateBalance($token, $newBalance)
{
    $query = "UPDATE Wallet SET balance = '$newBalance' WHERE id IN (SELECT Wallet.id FROM Token,User, Wallet 
    WHERE Token.value = '$token' AND Token.user_phone = User.phone AND Wallet.id = User.wallet_id)";
    return dbQuery($query);
}

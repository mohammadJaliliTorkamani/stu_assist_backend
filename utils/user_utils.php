<?php

function getToken()
{
    $headers = getallheaders();
    if (!array_key_exists('Authorization', $headers))
        return null;
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

function hasValidFullName($userID)
{
    $result = dbQuery("SELECT name, last_name FROM User WHERE id = '$userID'");
    $row = dbFetchAssoc($result);
    $name = $row['name'];
    $lastName = $row['last_name'];

    if ($name == null || $name === '' || $name === ' ' || $lastName == null || $lastName === '' || $lastName === ' ')
        return false;
    return true;
}

function getUserID($token)
{
    $result = dbQuery("SELECT User.id AS id FROM User, Token WHERE Token.value = '$token' AND User.phone = Token.user_phone");
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

function getUserInfo($userID)
{
    $query = "SELECT id, name, last_name, profile_photo FROM User WHERE id = '$userID' AND type = '1'";
    $result = dbQuery($query);
    $row = dbFetchAssoc($result);

    $user =  array(
        'id' => $row['id'],
        'name' => $row['name'] == null ? '' : $row['name'],
        'last_name' => $row['last_name'] == null ? '' : $row['last_name']
    );

    $profilePhotoID = (int)$row['profile_photo'];

    if ($profilePhotoID === -1)
        $user['photo'] = null;
    else {
        $photoQuery = "SELECT path FROM Photo where id = '$profilePhotoID'";
        $photoResult = dbQuery($photoQuery);
        $user['photo'] = dbFetchAssoc($photoResult);
    }

    return $user;
}

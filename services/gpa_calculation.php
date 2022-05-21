<?php

require("../config.php");

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

    $query = "SELECT * from Token WHERE value = '$token'";
    $result = dbQuery($query);

    return dbNumRows($result) > 0;
}

function getCoupons($token)
{
    $query = "SELECT balance FROM Token,User, Wallet 
    WHERE Token.value = '$token' AND Token.user_phone = User.phone AND Wallet.id = User.wallet_id";
    $result = dbQuery($query);
    $balance = intval(dbFetchAssoc($result)['balance']);
    return $balance;
}

function hasEnoughCoupons($coupons)
{
    return $coupons > 0;
}

function decreaseCoupon($token, $newCoupons)
{
    $query = "SELECT Wallet.id FROM Token,User, Wallet 
    WHERE Token.value = '$token' AND Token.user_phone = User.phone AND Wallet.id = User.wallet_id";
    $result = dbQuery($query);
    $walletID = dbFetchAssoc($result)['id'];
    $query = "UPDATE Wallet SET balance = '$newCoupons' WHERE id = '$walletID'";
    return dbQuery($query);
}

$token = getToken();
if (isValid($token)) {
    $coupons = getCoupons($token);
    if (hasEnoughCoupons($coupons)) {
        $min = $_GET['min'];
        $max = $_GET['max'];
        $grade = $_GET['grade'];
        $gpa = 3 * (($max - $grade) / ($max - $min)) + 1;
        $result = decreaseCoupon($token, $coupons - 1);
        if ($result === TRUE) {
            sendResponseCode();
            $result = [];
            $result['error'] = false;
            $result['data'] = $gpa;
            $result['message'] = '';
            echo (json_encode($result));
        } else
            sendResponseCode(false);
    } else {
        $result = [];
        $result['error'] = true;
        $result['data'] = null;
        $result['message'] = 'اتمام کوپن';
        echo (json_encode($result));
    }
} else
    sendResponseCode(false);

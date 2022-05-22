<?php

require("../config.php");

function getToken()
{
    $headers = getallheaders();
    $val = $headers['Authorization'];
    return trim(substr($val, 7));
}

function isActiveService($serviceName)
{
    $query = "SELECT * FROM Service WHERE name = '$serviceName' AND is_active = '1'";
    $result = dbQuery($query);
    return dbNumRows($result) == 1;
}

function isValid($token)
{
    if ($token === null || $token === '')
        return false;

    $query = "SELECT * from Token WHERE value = '$token' AND is_valid = '1'";
    $result = dbQuery($query);

    return dbNumRows($result) > 0;
}

function getBalance($token)
{
    $query = "SELECT balance FROM Token,User, Wallet 
    WHERE Token.value = '$token' AND Token.user_phone = User.phone AND Wallet.id = User.wallet_id";
    $result = dbQuery($query);
    return intval(dbFetchAssoc($result)['balance']);
}

function hasEnoughBalance($balance, $serviceCost)
{
    return  $balance >= $serviceCost;
}

function getCost($service_name)
{
    $costQuery = "SELECT price FROM Service WHERE name = '$service_name' AND is_active = '1'";
    $result = dbQuery($costQuery);
    if (dbNumRows($result) == 1) {
        return intval(dbFetchAssoc($result)['price']);
    }
    return -1;
}

function getServiceCost($serviceName)
{
    $query = "SELECT price FROM Service WHERE name = '$serviceName'";
    $result = dbQuery($query);
    return intval(dbFetchAssoc($result)['price']);
}

function getPhoneNumber($token)
{
    $query = "SELECT user_phone FROM Token WHERE value = '$token'";
    $result = dbQuery($query);
    return dbFetchAssoc($result)['user_phone'];
}

function decreaseBalance($token, $newBalance)
{
    $query = "SELECT Wallet.id FROM Token,User, Wallet 
    WHERE Token.value = '$token' AND Token.user_phone = User.phone AND Wallet.id = User.wallet_id";
    $result = dbQuery($query);
    $walletID = dbFetchAssoc($result)['id'];
    $query = "UPDATE Wallet SET balance = '$newBalance' WHERE id = '$walletID'";
    return dbQuery($query);
}

function insertServiceUsage($token)
{
    $SERVICE_NAME = 'ECTS_Basic_1';
    $phoneNumber = getPhoneNumber($token);
    $cost = getCost($SERVICE_NAME);
    $currentDate = date('Y-m-d');
    $currentTime = date('H:i:s');
    $query = "INSERT INTO Service_Usage (service_name, user_phone, paid_cost, date, time) 
    VALUES ('$SERVICE_NAME','$phoneNumber','$cost','$currentDate','$currentTime')";
    return dbQuery($query);
}

$SERVICE_NAME = 'ECTS_Basic_1';
$token = getToken();

if (isValid($token)) {
    if (isActiveService($SERVICE_NAME)) {
        $balance = getBalance($token);
        $serviceCost = getServiceCost($SERVICE_NAME);
        if (hasEnoughBalance($balance, $serviceCost)) {
            $time = $_GET['time'];
            $unit = $_GET['unit'];
            $week = $_GET['week'];
            $ects = ($time * $unit * 16) / ($week * 30);
            $result1 = decreaseBalance($token, $balance - $serviceCost);
            $result2 = insertServiceUsage($token);

            if ($result1 == TRUE && $result2 == TRUE) {
                sendResponseCode();
                $result = [];
                $result['error'] = false;
                $result['data'] = $ects;
                $result['message'] = '';
                echo (json_encode($result));
            } else
                sendResponseCode(false);
        } else {
            $result = [];
            $result['error'] = true;
            $result['data'] = null;
            $result['message'] = 'موجودی ناکافی';
            echo (json_encode($result));
        }
    } else {
        $result = [];
        $result['error'] = true;
        $result['data'] = null;
        $result['message'] = 'سرویس غیر فعال یا ناموجود';
        echo (json_encode($result));
    }
} else
    sendResponseCode(false);

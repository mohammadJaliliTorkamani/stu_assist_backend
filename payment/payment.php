<?php

require("../config.php");
require_once('../utils/user_utils.php');

$price = $_POST['price'];
$numberOfRequests = $_POST['number_of_requests'];

$token = getToken();

function deleteExistingIncompleteChargeRecords($userID)
{
    return dbQuery("DELETE FROM Charge WHERE user = '$userID' AND created = '0'");
}

function insertChargeRecord($phoneNumber, $walletID)
{
    $userID = getUserIDFromPhone($phoneNumber);
    deleteExistingIncompleteChargeRecords($userID);
    global $price;
    $currentDate = date('Y-m-d');
    $currentTime = date('H:i:s');

    $query = "INSERT INTO Charge(user, wallet_id, price, record_creation_date, record_creation_time) 
        VALUES ('$userID','$walletID','$price','$currentDate','$currentTime')";
    $result = dbQuery($query);
    return $result == True ? dbInsertId() : -1;
}
function pay($orderID, $phoneNumber)
{
    global $price;
    $params = array(
        'order_id' => $orderID,
        'amount' => $price,
        'phone' => $phoneNumber,
        'callback' => 'https://stu-assist.ir/api/payment/handle_payment_result.php',
    );

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://api.idpay.ir/v1.1/payment');
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'X-API-KEY: 394a641a-c18b-49c8-a259-11225529ed9a',
        'X-SANDBOX: 0'
    ));

    $result = curl_exec($ch);
    curl_close($ch);
    return json_decode($result);
}

function isValidFinancialInfo()
{
    global $price, $numberOfRequests;
    $query = "SELECT id FROM ChargeValues WHERE price = '$price' AND number_of_requests = '$numberOfRequests' AND is_valid = '1'";
    $result = dbQuery($query);
    return dbNumRows($result) > 0;
}

function addLinkDetailToDB($orderID, $paymentID, $paymentLink)
{
    $query = "UPDATE Charge SET payment_id = '$paymentID', payment_link = '$paymentLink', created = '1' WHERE order_id = '$orderID'";
    return dbQuery($query);
}

if (isValid($token)) {
    $phoneNumber = getPhoneNumber($token);
    $walletID = getWalletID($token);
    if (isValidFinancialInfo()) {
        $orderID = insertChargeRecord($phoneNumber, $walletID);
        if ($orderID !== -1) {
            $link = pay($orderID, $phoneNumber);
            if (addLinkDetailToDB($orderID, $link->id, $link->link))
                cook($link->link);
            else
                cook(null, true, 'خطای سرور - ۲');
        } else
            cook(null, true, 'خطای سرور');
    } else
        cook(null, true, 'خطا در پردازش اطلاعات پرداخت');
} else
    cook(null, true, 'نشست نامعتبر');

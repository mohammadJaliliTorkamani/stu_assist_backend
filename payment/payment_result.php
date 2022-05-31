<?php

require("../config.php");
require_once('../user_utils.php');

$token = getToken();

function getStatusCodeMeaning($status)
{
    switch ($status) {
        case 1:
            return "پرداخت انجام نشده است";
        case 2:
            return "پرداخت ناموفق بوده است";
        case 3:
            return "خطا رخ داده است";
        case 4:
            return "بلوکه شده";
        case 5:
            return "برگشت به پرداخت کننده";
        case 6:
            return "برگشت خورده سیستمی";
        case 7:
            return "انصراف از پرداخت";
        case 8:
            return "به درگاه پرداخت منتقل شد";
        case 10:
            return "در انتظار تایید پرداخت";
        case 100:
            return "پرداخت تایید شده است";
        case 101:
            return "پرداخت قبلا تایید شده است";
        case 200:
            return "به دریافت کننده واریز شد";
    }
    return "خطا در شناسایی نتیحه پرداخت";
}


function getLastPayment($token)
{
    $phoneNumber = getPhoneNumber($token);
    $query = "SELECT order_id, user_phone, card_number, price, verification_status,payment_track_id, payment_date, payment_time 
    FROM Charge WHERE user_phone = '$phoneNumber' ORDER BY payment_date, payment_time DESC";
    $result = dbQuery($query);
    if (dbNumRows($result) > 0)
        return dbFetchAssoc($result);
    return null;
}

if (isValid($token)) {
    $lastRecord = getLastPayment($token);
    if ($lastRecord !== null) {
        $result['orderID'] = intval($lastRecord['order_id']);
        $result['userPhone'] = $lastRecord['user_phone'];
        $result['cardNumber'] = $lastRecord['card_number'];
        $result['price'] = intval($lastRecord['price']);
        $result['status'] = intval($lastRecord['verification_status']);
        $result['statusMeaning'] = getStatusCodeMeaning(intval($lastRecord['verification_status']));
        $result['trackID'] = $lastRecord['payment_track_id'];
        $result['date'] = $lastRecord['payment_date'];
        $result['time'] = $lastRecord['payment_time'];
        cook($result);
    } else
        cook(null, true, 'no transaction was found');
} else
    cook(null, true, 'invalid token');

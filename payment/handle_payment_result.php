<?php

require("../config.php");
require_once('../utils/user_utils.php');

$creationStatus = $_POST['status'];
$idPayTrackID = $_POST['track_id'];
$id = $_POST['id'];
$orderID = $_POST['order_id'];
$amount = $_POST['amount'];
$cardNo = $_POST['card_no'];
$hashedCardNo = $_POST['hashed_card_no'];
$paymentDateTime = date("Y/m/d H:i:s", intval($_POST['date']));
$date = explode(" ", $paymentDateTime)[0];
$time = explode(" ", $paymentDateTime)[1];

function isValidRecordInfo()
{
    global $amount, $id, $orderID;
    $query = "SELECT order_id FROM Charge 
    WHERE order_id = '$orderID' AND price = '$amount' AND payment_id = '$id' 
    AND created ='1' AND verified = '0' AND completed = '0'";

    $result = dbQuery($query);
    return dbNumRows($result) == 1;
}

function updateRecord($orderID)
{
    global $idPayTrackID, $orderID, $cardNo, $hashedCardNo, $creationStatus, $date, $time;
    $query = "UPDATE Charge 
    SET id_pay_track_id = '$idPayTrackID', card_number = '$cardNo', hashed_card_no = '$hashedCardNo', 
    creation_date = '$date', creation_time = '$time', creation_status = '$creationStatus'
    WHERE order_id = '$orderID'";

    return dbQuery($query);
}

function setRecordAsVerifiedAndCompleteWithDateTimeTrackID($orderID, $verificationDate, $verificationTime, $paymentDate, $paymentTime, $paymentTrackID)
{
    $query = "UPDATE Charge 
    SET verified = '1', completed = '1', verification_date = '$verificationDate', verification_time = '$verificationTime',
    payment_date = '$paymentDate', payment_time = '$paymentTime', payment_track_id = '$paymentTrackID' 
    WHERE order_id = '$orderID' AND created = '1' AND verified = '0'";
    return dbQuery($query);
}

function verifyPayment($paymentID, $orderID)
{
    $params = array(
        'id' => $paymentID,
        'order_id' => $orderID,
    );

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://api.idpay.ir/v1.1/payment/verify');
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'X-API-KEY: 394a641a-c18b-49c8-a259-11225529ed9a',
        'X-SANDBOX: 0',
    ));

    $result = curl_exec($ch);
    curl_close($ch);
    return json_decode($result);
}

function insertVerificationStatus($orderID, $verificationStatus)
{
    $query = "UPDATE Charge SET verification_status = '$verificationStatus' WHERE order_id = '$orderID'";
    return dbQuery($query);
}

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

function getAmount($orderID)
{
    $query = "SELECT price FROM Charge WHERE order_id = '$orderID'";
    $result = dbQuery($query);
    return intval(dbFetchAssoc($result)['price']);
}

function increaseBalance($orderID)
{
    $result = dbQuery("SELECT Token.value as token FROM Charge,Token, User WHERE 
    Charge.order_id = '$orderID' AND Token.user = User.id AND 
    Charge.user = User.id");
    $token = dbFetchAssoc($result)['token'];
    $price = getAmount($orderID);
    $walletID = getWalletID($token);
    return dbQuery("UPDATE Wallet SET balance = balance + '$price' WHERE id = '$walletID'");
}

$result = null;

if (isValidRecordInfo()) {
    if (updateRecord($orderID) == TRUE) {
        if ($creationStatus == 10) {
            $verifiyResult = verifyPayment($id, $orderID);
            insertVerificationStatus($orderID, $verifiyResult->status);
            if ($verifiyResult->status == 100) {
                $verificationDateTime = date("Y/m/d H:i:s", intval($verifiyResult->payment->date));
                $verificationDate = explode(" ", $verificationDateTime)[0];
                $verificationTime = explode(" ", $verificationDateTime)[1];
                $paymentDateTime = date("Y/m/d H:i:s", intval($verifiyResult->date));
                $paymentDate = explode(" ", $paymentDateTime)[0];
                $paymentTime = explode(" ", $paymentDateTime)[1];
                if (setRecordAsVerifiedAndCompleteWithDateTimeTrackID(
                    $orderID,
                    $verificationDate,
                    $verificationTime,
                    $paymentDate,
                    $paymentTime,
                    $verifiyResult->payment->track_id
                ) == TRUE) {
                    $data['card_number'] = $verifiyResult->payment->card_no;
                    $data['order_id'] = $verifiyResult->order_id;
                    $data['amount'] = $verifiyResult->amount;
                    $data['date'] = $verifiyResult->date;
                    $data['track_id'] = $verifiyResult->payment->track_id;
                    $data['status_meaning'] = getStatusCodeMeaning($verifiyResult->status);
                    if (increaseBalance($orderID) == TRUE)
                        $result = cook($data, false, null, false);
                    else
                        $result = cook(null, true, 'خطای داخلی سرور - کد ۴', false);
                } else
                    $result = cook(null, true, 'خطای داخلی سرور - کد ۳', false);
            } else
                $result = cook(null, true, 'خطای داخلی سرور - کد ۲', false);
        }
    } else
        $result = cook(null, true, 'خطای داخلی سرور', false);
} else
    $result = cook(null, true, 'نتیجه معتبر یافت نشد', false);

//result is not used for the current version!
$query =  "<script type='text/javascript' language='Javascript'>
    window.open('https://stu-assist.ir/payment-result');</script>";
header("location: https://stu-assist.ir/payment-result");

exit();

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

    $query = "SELECT * from Token WHERE value = '$token' AND is_valid = '1'";
    $result = dbQuery($query);

    return dbNumRows($result) > 0;
}

$token = getToken();
$data = [];
$transactions = [];
$result = [];

if (isValid($token)) {
    $infoQuery = "SELECT name, last_name, balance, wallet_id FROM Token, User, Wallet WHERE User.wallet_id = Wallet.id AND Token.value = '$token' AND User.phone = Token.user_phone";
    $query_result = dbQuery($infoQuery);
    $row = dbFetchAssoc($query_result);
    $fullName = $row['name'] . ' ' . $row['last_name'];
    $balance = intval($row['balance']);
    $walletID = $row['wallet_id'];
    $data['fullName'] = $fullName;
    $data['balance'] = $balance;

    $query = "SELECT issue_tracking_no, card_number, bank, date, time FROM Charge WHERE wallet_id = '$walletID'";
    $query_result = dbQuery($query);

    if ($query_result == TRUE) {
        $result['error'] = false;
        $result['message'] = '';
        $counter = 1;
        while ($row = dbFetchAssoc($query_result)) {
            $r['id'] = $counter;
            $r['issueTrackingNo'] = $row['issue_tracking_no'];
            $cadNumber = $row['card_number'];
            $r['cardNo'] = trim(substr($cadNumber, 0, 3)) . '* **** **** *' . trim(substr($cadNumber, 13));
            $r['bank'] = $row['bank'];
            $r['time'] = $row['time'];
            $r['date'] = $row['date'];
            array_push($transactions, $r);
            $counter++;
        }
        $data['transactions'] = $transactions;
    } else {
        $result['error'] = true;
        $result['message'] = 'خطای دریافت تراکنش';
    }
    $result['data'] = $data;
    sendResponseCode();

    echo (json_encode($result));
} else {
    sendResponseCode(false);
}

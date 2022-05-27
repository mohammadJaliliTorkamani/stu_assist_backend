<?php

require("../config.php");
require_once('../user_utils.php');

$token = getToken();

if (isValid($token)) {
    $data = [];
    $infoQuery = "SELECT name, last_name, balance, wallet_id 
    FROM Token, User, Wallet 
    WHERE User.wallet_id = Wallet.id AND Token.value = '$token' AND User.phone = Token.user_phone";

    $info_result = dbQuery($infoQuery);
    $infoRow = dbFetchAssoc($info_result);
    $data['fullName'] = $infoRow['name'] . ' ' . $infoRow['last_name'];
    $walletID = $infoRow['wallet_id'];
    $data['balance'] = intval($infoRow['balance']);

    $query = "SELECT issue_tracking_no AS ITN, card_number, bank, date, time FROM Charge WHERE wallet_id = '$walletID'";
    $query_result = dbQuery($query);

    if ($query_result == TRUE) {
        $counter = 1;
        $transactions = [];
        while ($row = dbFetchAssoc($query_result)) {
            $cadNumber = $row['card_number'];

            $transcation['id'] = $counter++;
            $transcation['issueTrackingNo'] = $row['ITN'];
            $transcation['cardNo'] = trim(substr($cadNumber, 0, 3)) . '* **** **** *' . trim(substr($cadNumber, 13));
            $transcation['bank'] = $row['bank'];
            $transcation['time'] = $row['time'];
            $transcation['date'] = $row['date'];

            array_push($transactions, $transcation);
        }
        $data['transactions'] = $transactions;
    } else
        cook(null, true, 'Something went wrong');
    cook($data);
} else
    cook(null, true, 'invalid token');

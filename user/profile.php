<?php

require("../config.php");
require_once('../utils/user_utils.php');
require_once('../utils/address_utils.php');
require_once('../utils/photo_utils.php');

$token = getToken();

if (isValid($token)) {
    $data = [];
    $infoQuery = "SELECT balance, wallet_id, name, last_name, profile_photo, address, biography 
    FROM Token, User, Wallet 
    WHERE User.wallet_id = Wallet.id AND Token.value = '$token' AND User.id = Token.user";

    $info_result = dbQuery($infoQuery);
    $infoRow = dbFetchAssoc($info_result);
    $walletID = $infoRow['wallet_id'];
    $data['name'] = $infoRow['name'];
    $data['lastName'] = $infoRow['last_name'];
    $data['biography'] = $infoRow['biography'];
    $data['balance'] = intval($infoRow['balance']);
    $data['address']=getAddress(intval($infoRow['address']));
    $data['photoPath']=getPhoto(intval($infoRow['profile_photo']));
    
    $query = "SELECT payment_track_id AS ITN, card_number, bank, price, payment_date, payment_time, order_id 
    FROM Charge 
    WHERE wallet_id = '$walletID' AND completed = '1'";

    $query_result = dbQuery($query);

    if ($query_result == TRUE) {
        $counter = 1;
        $transactions = [];
        while ($row = dbFetchAssoc($query_result)) {
            $cadNumber = $row['card_number'];
            $transcation = array(
                'id' => $counter++,
                'issueTrackingNo' => $row['ITN'],
                'cost' => (int)$row['price'],
                'orderID' => intval($row['order_id']),
                'time' => $row['payment_time'],
                'date' => $row['payment_date']
            );

            array_push($transactions, $transcation);
        }
        $data['transactions'] = $transactions;
        cook($data);
    } else
        cook(null, true, 'خطای داخلی سرور');
} else
    cook(null, true, 'نشست نامعتبر');

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

if (isValid($token)) {
    $query = "SELECT price, coupons FROM ChargeValues WHERE is_valid = '1' ";
    $queryResult = dbQuery($query);
    $result = [];
    $counter = 1;
    if ($queryResult == TRUE) {
        if (dbNumRows($queryResult) > 0) {
            $data = [];
            while ($row = dbFetchAssoc($queryResult)) {
                $r['id'] = $counter++;
                $r['price'] = $row['price'];
                $r['value'] = $row['coupons'];
                array_push($data, $r);
            }
            $result['error'] = false;
            $result['message'] = null;
            $result['data'] = $data;
        }
    } else {
        $result['error'] = true;
        $result['message'] = 'خطای سرور';
        $result['data'] = [];
    }

    echo (json_encode($result));
}else {
    sendResponseCode(false);
}
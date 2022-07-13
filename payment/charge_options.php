<?php

require("../config.php");
require_once("../utils/user_utils.php");

$token = getToken();

if (isValid($token)) {
    $queryResult = dbQuery("SELECT price, number_of_requests FROM ChargeValues WHERE is_valid = '1' ");
    $result = [];
    $counter = 1;
    if ($queryResult == TRUE) {
        if (dbNumRows($queryResult) > 0) {
            $chargeValues = [];
            while ($row = dbFetchAssoc($queryResult)) {
                $chargeValue['id'] = $counter++;
                $chargeValue['price'] = $row['price'];
                $chargeValue['numberOfRequests'] = $row['number_of_requests'];
                array_push($chargeValues, $chargeValue);
            }
        }
        cook($chargeValues);
    } else
        cook(null, true, 'Something went wrong');
} else
    cook(null, true, 'invalid token');

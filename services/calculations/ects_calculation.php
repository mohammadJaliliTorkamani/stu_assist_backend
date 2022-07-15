<?php

require_once("../config.php");
require_once("../utils/user_utils.php");
require_once("../utils/service_utils.php");

$SERVICE_NAME = 'ECTS_Basic_1';
$time = $_GET['time'];
$unit = $_GET['unit'];
$week = $_GET['week'];

function validECTSData($time, $unit, $week)
{
    if (($time <= 0) || ($time > 120) || ($unit <= 0) || ($unit > 20) || ($week < 10) || ($week > 25))
        return false;
    else if ($week == 0)
        return false;

    return true;
}

function calculateECTS($time, $unit, $week)
{
    return ($time * $unit * 16) / ($week * 30);
}

$token = getToken();
if (isValid($token)) {
    if (isActiveService($SERVICE_NAME)) {
        $balance = getBalance($token);
        $serviceCost = getServiceCost($SERVICE_NAME);
        if ($balance >= $serviceCost) {
            if (validECTSData($time, $unit, $week)) {
                $ects = calculateECTS($time, $unit, $week);
                $result1 = updateBalance($token, $balance - $serviceCost);
                $result2 = insertServiceUsage($token, $SERVICE_NAME);
                if ($result1 == TRUE && $result2 == TRUE)
                    cook($ects);
                else
                    cook(null, true, 'Something went wrong');
            } else
                cook(null, true, 'invalid ECTS data was passed');
        } else
            cook(null, true, 'Insufficient balance');
    } else
        cook(null, true, 'No active services were found');
} else
    cook(null, true, 'invalid token');

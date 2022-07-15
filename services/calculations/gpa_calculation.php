<?php

require("../config.php");
require_once('../utils/user_utils.php');
require_once('../utils/service_utils.php');

$SERVICE_NAME = 'GPA_Basic_1';
$min = $_GET['min'];
$max = $_GET['max'];
$grade = $_GET['grade'];

function validGPAData($min, $max, $grade)
{
    if (($min < 0) || ($min > 20) || ($max < 0) || ($max > 20) || ($grade < 0) || ($grade > 20))
        return false;
    else if (($grade < $min) || ($grade > $max))
        return false;
    else if (($max - $min) == 0)
        return false;

    return true;
}

function calculateGPA($max, $min, $grade)
{
    return 3 * (($max - $grade) / ($max - $min)) + 1;
}

$token = getToken();
if (isValid($token)) {
    if (isActiveService($SERVICE_NAME)) {
        $balance = getBalance($token);
        $serviceCost = getServiceCost($SERVICE_NAME);
        if ($balance >= $serviceCost) {
            if (validGPAData($min, $max, $grade)) {
                $gpa = calculateGPA($max, $min, $grade);
                $result1 = updateBalance($token, $balance - $serviceCost);
                $result2 = insertServiceUsage($token, $SERVICE_NAME);
                if ($result1 == TRUE && $result2 == TRUE)
                    cook($gpa);
                else
                    cook(null, true, 'Something went wrong');
            } else
                cook(null, true, 'invalid GPA data was passed');
        } else
            cook(null, true, 'Insufficient balance');
    } else
        cook(null, true, 'No active services were found');
} else
    cook(null, true, 'invalid token');

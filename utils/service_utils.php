<?php

function insertServiceUsage($token, $SERVICE_NAME)
{
    $currentDate = date('Y-m-d');
    $currentTime = date('H:i:s');
    $userID = getUserID($token);
    $cost = getServiceCost($SERVICE_NAME);
    $query = "INSERT INTO Service_Usage (service_name, user, paid_cost, date, time) 
    VALUES ('$SERVICE_NAME','$userID','$cost','$currentDate','$currentTime')";
    return dbQuery($query);
}

function getServiceCost($service_name)
{
    $result = dbQuery("SELECT price FROM Service WHERE name = '$service_name'");
    if (dbNumRows($result) == 1)
        return intval(dbFetchAssoc($result)['price']);
    return -1;
}

function isActiveService($serviceName)
{
    $result = dbQuery("SELECT name FROM Service WHERE name = '$serviceName' AND is_active = '1'");
    return dbNumRows($result) > 0;
}

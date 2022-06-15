<?php

require("../config.php");
require_once('../user_utils.php');

$token = getToken();

function getUniversityInfo($universityID)
{
    $query = "SELECT name, country, city FROM University WHERE id = '$universityID'";
    $result = dbQuery($query);
    return dbFetchAssoc($result);
}

function getUserInfo($phoneNumber)
{
    $query = "SELECT name, last_name FROM User WHERE phone = '$phoneNumber' AND type = '1'";
    $result = dbQuery($query);
    $row = dbFetchAssoc($result);

    if ($row['name'] == null)
        $row['name'] = '';
    if ($row['last_name'] == null)
        $row['last_name'] = '';

    return $row;
}

function getAdmissionStatus($phoneNumber, $universityID)
{
    $query = "SELECT admission_status as status FROM Application_Experience WHERE user_phone = '$phoneNumber' AND university_id = '$universityID'";
    $result = dbQuery($query);
    return dbFetchAssoc($result)['status'] == '1' ? true : false;
}

if (isValid($token)) {

    $finalResult = [];

    $phoneNumber = getPhoneNumber($token);
    $query = "SELECT id, creation_date, creation_time, comment, university_id
    FROM Experience 
    WHERE verified = '1'
    ORDER BY creation_date, creation_time DESC";
    
    $result = dbQuery($query);
    while ($row = dbFetchAssoc($result)) {
        $_id = intval($row['id']);
        $_creationDate = $row['creation_date'];
        $_creationTime = $row['creation_time'];
        $_comment = $row['comment'];
        $_universityID = intval($row['university_id']);

        $university = getUniversityInfo($_universityID);
        $user = getUserInfo($phoneNumber);
        $admissionStatus = getAdmissionStatus($phoneNumber, $_universityID);

        $record['fullName'] = $user['name'] . ' ' . $user['last_name'];
        $record['experienceDate'] = $_creationDate;
        $record['experienceTime'] = $_creationTime;
        $record['admissionStatus'] = $admissionStatus;
        $record['universityName'] = $university['name'];
        $record['universityCountry'] = $university['country'];
        $record['universityCity'] = $university['city'];

        array_push($finalResult, $record);
    }
    cook($finalResult);
} else
    cook(null, true, 'invalid token');

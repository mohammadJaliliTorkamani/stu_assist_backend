<?php

require("../config.php");
require_once('../utils/user_utils.php');

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

$finalResult = [];

$query = "SELECT id, creation_date, creation_time, comment, university_id, user_phone
    FROM Experience 
    WHERE verified = '1'
    ORDER BY creation_date, creation_time DESC";

$result = dbQuery($query);
$counter = 1;
while ($row = dbFetchAssoc($result)) {
    $_id = intval($row['id']);
    $_creationDate = $row['creation_date'];
    $_creationTime = $row['creation_time'];
    $_comment = $row['comment'];
    $_universityID = intval($row['university_id']);

    $phoneNumber = $row['user_phone'];
    $university = getUniversityInfo($_universityID);
    $user = getUserInfo($phoneNumber);
    $admissionStatus = getAdmissionStatus($phoneNumber, $_universityID);

    $record = array(
        'id' => $counter++,
        'fullName' => $user['name'] . ' ' . $user['last_name'],
        'experienceDate' => $_creationDate,
        'experienceTime' => $_creationTime,
        'admissionStatus' => $admissionStatus,
        'comment' => $_comment,
        'universityName' => $university['name'],
        'universityCountry' => $university['country'],
        'universityCity' => $university['city']
    );

    array_push($finalResult, $record);
}
cook($finalResult);

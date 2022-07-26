<?php

require("../config.php");
require_once('../utils/user_utils.php');
require_once('./university_utils.php');

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
        'userID' => (int)$user['id'],
        'fullName' => $user['name'] . ' ' . $user['last_name'],
        'photo' => $user['photo'],
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

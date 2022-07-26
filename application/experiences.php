<?php

require("../config.php");
require_once('../utils/user_utils.php');
require_once('../utils/university_utils.php');

$finalResult = [];

$query = "SELECT id, creation_date, creation_time, comment, university, user , admission_status
    FROM User_University_Relation_Admission 
    WHERE verified = '1'
    ORDER BY creation_date, creation_time DESC";

$result = dbQuery($query);
while ($row = dbFetchAssoc($result)) {
    $_id = intval($row['id']);
    $_creationDate = $row['creation_date'];
    $_creationTime = $row['creation_time'];
    $_comment = $row['comment'];
    $admissionStatus = ((int)$row['admission_status']) === 1 ? true : false;
    $university = getUniversityInfo(intval($row['university']));
    $user = getUserInfo($row['user']);

    $record = array(
        'id' => $_id,
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

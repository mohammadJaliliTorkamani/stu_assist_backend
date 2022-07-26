<?php

require('../config.php');

function getUniversityInfo($universityID)
{
    $query = "SELECT name, country, city FROM University WHERE id = '$universityID'";
    $result = dbQuery($query);
    return dbFetchAssoc($result);
}

function getAdmissionStatus($phoneNumber, $universityID)
{
    $query = "SELECT admission_status as status FROM Application_Experience WHERE user_phone = '$phoneNumber' AND university_id = '$universityID'";
    $result = dbQuery($query);
    return dbFetchAssoc($result)['status'] == '1' ? true : false;
}
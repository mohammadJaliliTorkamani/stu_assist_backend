<?php

require("../config.php");
require_once('../utils/user_utils.php');

$experience = json_decode($_POST['experience']);
$university = json_decode($_POST['university']);
$admissionStatus = $_POST['admission_status'];

$token = getToken();

function createUniversityIfNotExists()
{
    global $university;
    $name = $university->name;
    $city = $university->city;
    $country = $university->country;

    $query = "SELECT id FROM University WHERE name = '$name'";
    $result = dbQuery($query);
    if (dbNumRows($result) == 0) {
        $addressTitle = $name . " University";
        $query = "INSERT INTO Address (title, country, city) VALUES('$addressTitle', '$country', '$city')";
        $result = dbQuery($query);
        $addressID = dbInsertId();
        $query = "INSERT INTO University(name, address) VALUES ('$name', '$addressID')";
        $result = dbQuery($query);
        return dbInsertId();
    } else
        return dbFetchAssoc($result)['id'];
}

function createExperience($universityID, $userID)
{
    global $experience, $admissionStatus;
    $currentDate = date('Y-m-d');
    $currentTime = date('H:i:s');
    $comment = $experience->comment;

    $query = "INSERT INTO User_University_Relation_Admission(comment, creation_date, creation_time, user, university, admission_status) 
    VALUES ('$comment','$currentDate','$currentTime','$userID','$universityID', '$admissionStatus')";
    return dbQuery($query);
}

if (isValid($token)) {
    $userID = getUserID($token);
    $universityID = createUniversityIfNotExists();

    if (createExperience($universityID, $userID))
        cook(null, false, 'تجربه پذیرش ثبت شد');
    else
        cook(null, true, 'حطای داخلی سرور');
} else
    cook(null, true, 'نشست نامعتبر');

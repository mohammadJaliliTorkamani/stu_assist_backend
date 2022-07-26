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
    $country = $university->country;
    $city = $university->city;
    $name = $university->name;

    $query = "SELECT id FROM University WHERE name = '$name' AND country = '$country' and city = '$city'";
    $result = dbQuery($query);
    if (dbNumRows($result) == 0) {
        $query = "INSERT INTO University(name, country, city) VALUES ('$name', '$country', '$city')";
        $result = dbQuery($query);
        return dbInsertId();
    } else
        return dbFetchAssoc($result)['id'];
}

function createExperience($universityID, $phoneNumber)
{
    global $experience;
    $currentDate = date('Y-m-d');
    $currentTime = date('H:i:s');
    $comment = $experience->comment;

    $query = "INSERT INTO Experience(comment, creation_date, creation_time, user_phone, university_id) 
    VALUES ('$comment','$currentDate','$currentTime','$phoneNumber','$universityID')";
    dbQuery($query);
}

function addIntoApplicationExperience($universityID, $phoneNumber)
{
    global $admissionStatus;
    $query = "INSERT INTO Application_Experience (user_phone, university_id, admission_status) 
    VALUES ('$phoneNumber','$universityID','$admissionStatus')";
    return dbQuery($query);
}

if (isValid($token)) {
    $phoneNumber = getPhoneNumber($token);
    $universityID = createUniversityIfNotExists();
    createExperience($universityID, $phoneNumber);

    if (addIntoApplicationExperience($universityID, $phoneNumber))
        cook(null, false, 'تجربه پذیرش ثبت شد');
    else
        cook(null, true, 'حطای داخلی سرور');
} else
    cook(null, true, 'نشست نامعتبر');

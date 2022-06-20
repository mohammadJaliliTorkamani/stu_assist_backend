<?php

require("../config.php");
require_once('../user_utils.php');

function getLanguages($translationOfficeID)
{
    $query = "SELECT persian_equivalent 
    FROM Translation_Language, Translation_Office_Language 
    WHERE Translation_Office_Language.language_id  = Translation_Language.id AND Translation_Office_Language.translation_office_id = '$translationOfficeID'";
    $result = dbQuery($query);
    $languages = [];
    while ($row = dbFetchAssoc($result))
        array_push($languages, $row['persian_equivalent']);

    return $languages;
}
$query = "SELECT id, name, website, phone, postal_address, latitude, longitude FROM Translation_Office WHERE available = '1' ORDER BY name ASC";
$result = dbQuery($query);
$offices = [];
$counter = 1;
while ($row = dbFetchAssoc($result)) {
    $office['id'] = $counter++;
    $office['name'] = $row['name'];
    $office['website'] = $row['website'];
    $office['phoneNumber'] = $row['phone'];
    $office['address'] = ['name' => $row['postal_address'], 'latitude' => $row['latitude'], 'longitude' => $row['longitude']];
    $office['languages'] = getLanguages(intval($office['id']));
    array_push($offices, $office);
}
cook($offices);

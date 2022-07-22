<?php

require("../../config.php");
require_once('../../utils/user_utils.php');

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

$query = "SELECT Translation_Office. id as id, Translation_Office.name as name, 
Translation_Office.website as website, Translation_Office.phone as phone, Address.value as postal_address,
 Address.state as state, Address.city as city, Translation_Office.latitude as latitude, Translation_Office.longitude as longitude 
FROM Translation_Office, Address
WHERE available = '1'  AND Address.id = Translation_Office.postal_address 
ORDER BY name ASC";
$result = dbQuery($query);

$offices = [];
$counter = 1;
while ($row = dbFetchAssoc($result)) {
    $office = array(
        'id' => ($counter++),
        'name' => $row['name'],
        'website' => $row['website'],
        'phoneNumber' => $row['phone'],
        'address' => [
            'name' => $row['postal_address'], 'latitude' => $row['latitude'],
            'longitude' => $row['longitude'], 'state' => $row['state'], 'city' => $row['city']
        ],
        'languages' => getLanguages($counter - 1)
    );
    array_push($offices, $office);
}
cook($offices);

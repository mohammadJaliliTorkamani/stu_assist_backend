<?php

require('../../config.php');

$query = "SELECT Embassy.country as country, Embassy.website as website, 
Embassy.phone, Photo.path as photo, 
Address.state, Address.city, Address.value as postalAddress 
FROM Embassy, Address, Photo 
WHERE Embassy.flag_photo = Photo.id 
AND Address.id = Embassy.address 
AND Embassy.available = '1'";

$embassies = [];
$result = dbQuery($query);

while ($row = dbFetchAssoc($result))
    array_push($embassies, $row);

cook($embassies);

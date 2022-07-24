<?php

require('../../config.php');

$query = "SELECT Embassy.country as title, Embassy.website as path, Embassy.phone, Photo.path as photo, 
Address.state, Address.city, Address.value 
FROM Embassy, Address, Photo 
WHERE Embassy.flag_photo = Photo.id 
AND Address.id = Embassy.address 
AND Embassy.available = '1'";

$embassies = [];
$result = dbQuery($query);

while ($row = dbFetchAssoc($result)) {
    $row['title'] = "سفارت " . $row['title'];
    array_push($embassies, $row);
}

cook($embassies);

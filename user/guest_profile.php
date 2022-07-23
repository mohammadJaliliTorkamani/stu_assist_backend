<?php

require("../config.php");

$userID = (int)$_GET['id'];

$query = "SELECT name, last_name, address, profile_photo as photo
FROM User
WHERE id = '$userID' AND type = '1'";

$result = dbQuery($query);
if (dbNumRows($result) == 0)
    cook(null, true, 'No such user exists!');
else {
    $guestProfileRow = dbFetchAssoc($result);
    $profilePhotoID = (int)$guestProfileRow['photo'];
    $profileAddressID = (int)$guestProfileRow['address'];

    if ($profileAddressID !== -1) {
        $addressQuery = "SELECT country, state, city FROM Address where id = '$profileAddressID'";
        $addressResult = dbQuery($addressQuery);
        $address = dbFetchAssoc($addressResult);
        $guestProfileRow['address'] = $address;
    } else
        $guestProfileRow['address'] = null;

    if ($profilePhotoID !== -1) {
        $photoQuery = "SELECT path FROM Photo where id = '$profilePhotoID'";
        $photoResult = dbQuery($photoQuery);
        $photo = dbFetchAssoc($photoResult);
        $guestProfileRow['photo'] = $photo;
    } else
        $guestProfileRow['photo'] = null;

    cook($guestProfileRow);
}

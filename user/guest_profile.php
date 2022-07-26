<?php

require("../config.php");
require_once('../utils/address_utils.php');
require_once('../utils/photi_utils.php');

$userID = (int)$_GET['id'];

$query = "SELECT name, last_name, address, profile_photo as photo FROM User WHERE id = '$userID' AND type = '1'";
$result = dbQuery($query);

if (dbNumRows($result) == 0)
    cook(null, true, 'کاربر وجود ندارد');
else {
    $guestProfileRow = dbFetchAssoc($result);
    $profilePhotoID = (int)$guestProfileRow['photo'];
    $profileAddressID = (int)$guestProfileRow['address'];
    $guestProfileRow['address'] = getAddress($profileAddressID);
    $guestProfileRow['photo'] = getPhoto($profilePhotoID);
    cook($guestProfileRow);
}

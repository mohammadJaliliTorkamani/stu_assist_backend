<?php

require("./config.php");
require_once("./utils/user_utils.php");

$query = "SELECT user_phone FROM Service_Usage";
$result = dbQuery($query);
while ($row = dbFetchAssoc($result)) {
    $phone = $row['user_phone'];
    $q = "SELECT id FROM User where phone = '$phone'";
    $r0 = dbQuery($q);
    $r = dbFetchAssoc($r0);
    $userID = $r['id'];
    $q2 = "UPDATE Service_Usage SET user_phone = '$userID' WHERE user_phone = '$phone'";
    echo $q2."\n";
    dbQuery($q2);
}

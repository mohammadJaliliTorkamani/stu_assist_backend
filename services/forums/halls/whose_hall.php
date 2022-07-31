<?php

require("../../../config.php");
require_once("../../../utils/forums_utils.php");

$hallID = $_GET['hall'];

$query = "SELECT category FROM Hall WHERE id = '$hallID' AND available = '1'";
$result = dbQuery($query);
$numberOfRecords = dbNumRows($result);

if ($numberOfRecords > 0)
    cook(dbFetchAssoc($result)['category']);
else
    cook(null);

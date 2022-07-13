<?php

require("../../config.php");

$userID = $_GET['id'];

$query = "SELECT name, last_name FROM User WHERE id = '$userID'";

$result = dbQuery($query);
$row = dbFetchAssoc($result);
$user['fullName'] = $row['name'] . " " . $row['last_name'];

cook($user);

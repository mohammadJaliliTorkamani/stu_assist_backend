<?php

require("config.php");
header('Access-Control-Allow-Origin: *'); //is needed for local port communications

$sql = "SELECT * FROM Service";
$result = dbQuery($sql);

if (dbNumRows($result) > 0) {
    while ($row = dbFetchAssoc($result)) {
        echo $row['name'];
    }
} else {
    echo 'No result found';
}

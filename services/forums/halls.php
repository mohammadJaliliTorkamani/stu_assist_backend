<?php

require("../../config.php");

$category = $_GET['category'];
$query = "SELECT name, descriptor FROM Hall WHERE category = '$category' AND available = '1' ORDER BY placement_order,name ASC";
$result = dbQuery($query);
$halls = [];
while ($row = dbFetchAssoc($result)) {
    $name['name'] = $row['name'];
    $name['link'] = 'https://stu-assist.ir';
    $hall['name'] = $name;

    $hall['descriptor'] = $row['descriptor'];
    $hall['numberOfTopics'] = 0;

    $lastPost['name'] = 'خالی';
    $lastPost['link'] = 'httos://stu-assist.ir';
    $lastPost['lastPostDateEquivalent'] = '۱ ساعت پیش';
    $hall['lastPost'] = $lastPost;
    array_push($halls, $hall);
}

cook($halls);

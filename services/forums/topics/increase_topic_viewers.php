<?php

require("../../../config.php");

$topicIDs = $_POST['id'];

$query = "UPDATE Topic SET number_of_views = number_of_views + 1 WHERE id = '$topicIDs' AND available = '1'";
$result = dbQuery($query);

if ($result)
    cook(null);
else
    cook(null, true, 'Error while operating');

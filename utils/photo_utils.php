<?php

function getPhoto($photoID)
{
    if ($photoID === -1)
        return null;

    $photoQuery = "SELECT path FROM Photo where id = '$photoID'";
    $photoResult = dbQuery($photoQuery);
    return dbFetchAssoc($photoResult);
}

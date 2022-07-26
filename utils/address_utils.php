<?php

require('../config.php');

function getAddress($addressID)
{
    if ($addressID === -1)
        return null;

    $addressQuery = "SELECT country, state, city FROM Address where id = '$addressID'";
    $addressResult = dbQuery($addressQuery);
    return  dbFetchAssoc($addressResult);
}

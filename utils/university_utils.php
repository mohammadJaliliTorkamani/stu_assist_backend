<?php

function getUniversityInfo($universityID)
{
    $query = "SELECT University.name, Address.country, Address.city FROM University, Address 
    WHERE University.address=  Address.id AND University.id = '$universityID'";
    $result = dbQuery($query);
    return dbFetchAssoc($result);
}
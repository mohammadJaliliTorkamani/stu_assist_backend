<?php

require('../config.php');

$txt = file_get_contents("../assets/GEO_API.txt");
cook($txt);

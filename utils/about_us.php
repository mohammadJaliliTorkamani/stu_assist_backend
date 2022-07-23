<?php

require('../config.php');

$txt = file_get_contents("../assets/about_us_content.txt");
cook($txt);

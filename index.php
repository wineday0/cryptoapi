<?php

use Main\Main;

require_once 'api/Main.php';
require_once 'api/Authorization.php';
require_once 'api/Service.php';
require_once 'api/Response.php';

$main = new Main();
echo $main->run();
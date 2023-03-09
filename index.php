<?php

use Main\Main;

require_once 'api/Main.php';
require_once 'api/CheckToken.php';
require_once 'api/BtcExchangeRate.php';
require_once 'api/Response.php';

$main = new Main();
echo $main->run();
<?php

require_once("../libraries/autoload.php");

if (isset(App::instance()->config['timezone']))
    date_default_timezone_set(App::instance()->config['timezone']);

$error = new Error();

Router::instance()->route();

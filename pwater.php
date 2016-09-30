<?php

$_SERVER['argv'] = array(); // we'll process this later

ob_start();
require 'tests/CiTestCase.php';
require __DIR__ . '/../' . basename(__FILE__, '.php') . '/index.php';
ob_end_clean();
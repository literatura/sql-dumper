#!/usr/bin/env php

<?php
/**
 * About
 *
 */

$loader = require_once __DIR__ . '/vendor/autoload.php';

use SqlDumper\SqlDumper;


$app = new SqlDumper();
$app->run();
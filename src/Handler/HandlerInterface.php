<?php

namespace SqlDumper\Handler;


interface HandlerInterface
{
    public function __construct($wwwFolder);

    public static function checkIsEngine($wwwFolder);

    public function getEngineName();

    public function doBackup();

    function getDbCredentials();
}
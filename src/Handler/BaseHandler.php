<?php
namespace SqlDumper\Handler;

use Ifsnop\Mysqldump\Mysqldump;

/**
 * Base handler class.
 *
 * Class BaseHandler
 * @package SqlDumper\Handler
 */

class BaseHandler
{
    protected $wwwFolder;
    protected $excludedTables = [];
    protected $tableWithoutData = [];

    public function __construct($wwwFolder)
    {
        $this->wwwFolder = $wwwFolder;
    }

    public function doBackup()
    {
        $dbCredentials = $this->getDbCredentials();

        $pdoSettings = [];

        $dumpSettings = $this->getDumpSettings();

        try {
            $dsn = 'mysql:host=' . $dbCredentials['host'] . ';dbname=' . $dbCredentials['dbName'];
            $dump = new Mysqldump($dsn, $dbCredentials['user'], $dbCredentials['password'], $dumpSettings, $pdoSettings);
            $dump->start($this->getDumpFilename());
        } catch (\Exception $e) {
            echo 'mysqldump-php error: ' . $e->getMessage();
        }
    }

    protected function getDumpSettings()
    {
        return [
            'exclude-tables' => $this->excludedTables,
            'compress' => Mysqldump::GZIP,
            'no-data' => $this->tableWithoutData,
        ];
    }

    protected function getDumpFilename()
    {
        return 'dumps/' . date('Y-m-d-H-i-s-') . 'dump.gz';
    }

    public function getDbCredentials()
    {
        return [
            'dbName' => '',
            'user' => '',
            'password' => '',
            'host' => '',
            'charset' => '',
        ];
    }
}
<?php
namespace SqlDumper\Handler;

use SqlDumper\Exception\HandlerException;

/**
 * An example for the development of new handlers
 *
 * Class ExampleHandler
 * @package SqlDumper\Handler
 */

class ExampleHandler extends BaseHandler implements HandlerInterface
{
    const ENGINE_CONFIG_PATH = 'path/to/config.php'; // relative to the www folder
    const ENGINE_NAME = 'Example';

    /* These tables will not be included in the dump */
    protected $excludedTables = [
        'some_table_name'
    ];

    /* Only create commands without data will be included in the dump */
    protected $tableWithoutData = [
        'some_table_name2'
    ];

    /**
     * The function tries to determine that the folder contains this engine.
     * For example, by a specific file or folder
     *
     * @param string $wwwFolder
     * @return bool
     */
    public static function checkIsEngine($wwwFolder)
    {
        if (file_exists($wwwFolder . '/wp-includes')) {
            return true;
        }

        return false;
    }

    /**
     * Used for display to user
     * @return string
     */
    public function getEngineName()
    {
        return self::ENGINE_NAME;
    }

    /**
     * Must return array with credentials for connecting to the database
     *
     * @return array
     * @throws HandlerException
     */
    public function getDbCredentials()
    {
        // For example get credentials from engine config file
        $engineConfig = $this->getEngineConfig();

        if (empty($engineConfig['DB_NAME']) || empty($engineConfig['DB_USER']) || empty($engineConfig['DB_PASSWORD']) ||
            empty($engineConfig['DB_HOST']) || empty($engineConfig['DB_CHARSET'])
        ) {
            throw new HandlerException('Some fields with database credentials not found in engine config');
        }

        return [
            'dbName' => $engineConfig['DB_NAME'],
            'user' => $engineConfig['DB_USER'],
            'password' => $engineConfig['DB_PASSWORD'],
            'host' => $engineConfig['DB_HOST'],
            'charset' => $engineConfig['DB_CHARSET'],
        ];
    }

    private function getEngineConfig()
    {
        $engineConfigPath = $this->wwwFolder . '/' . self::ENGINE_CONFIG_PATH;

        if (!file_exists($engineConfigPath) || !is_readable($engineConfigPath)) {
            throw new HandlerException('Config file not found or not readable');
        }

        $config = file_get_contents($engineConfigPath);

        // parse config
        $result = [];
        // some code to parse config

        return $result;
    }

    /**
     * If necessary, you can override this function to some actions before start backups.
     *
     * For example add table_prefix to table names in $excludedTables and $tableWithoutData
     */
    protected function beforeBackup()
    {
        parent::beforeBackup();
    }

    /**
     * If necessary, you can override the dump settings.
     * More detailed https://github.com/ifsnop/mysqldump-php
     *
     * @return array
     */
    protected function getDumpSettings()
    {
        return parent::getDumpSettings();
    }
}
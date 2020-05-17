<?php
namespace SqlDumper\Handler;

use SqlDumper\Exception\HandlerException;

/**
 * Handler for Wordpress.
 *
 * WARNING! Not used table_prefix from engine config. Used default wp_
 *
 * Class WordpressHandler
 * @package SqlDumper\Handler
 */

class WordpressHandler extends BaseHandler implements HandlerInterface
{
    const ENGINE_CONFIG_PATH = 'wp-config.php'; // relative www root folder
    const ENGINE_NAME = 'Wordpress';

    protected $excludedTables = [
        'wp_commentmeta'
    ];

    protected $tableWithoutData = [
        'wp_users'
    ];

    /**
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

    public function getEngineName()
    {
        return self::ENGINE_NAME;
    }

    public function getDbCredentials()
    {
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
        $data = [];
        preg_match_all('/define\((.*?)\)/', $config, $data);

        foreach ($data[1] as $dataLine) {
            $dataLine = trim($dataLine);
            $dataLine = trim($dataLine, '\'');
            $configLineData = explode('\'', $dataLine);
            $result[$configLineData[0]] = $configLineData[count($configLineData) - 1];
        }

        return $result;
    }
}
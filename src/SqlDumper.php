<?php
namespace SqlDumper;

use SqlDumper\Exception\FolderNotFoundException;
use SqlDumper\Exception\HandlerException;
use SqlDumper\Handler\HandlerInterface;

class SqlDumper extends BaseCLI
{
    private $wwwFolder;
    private $config;
    private $handler = null;

    public function __construct()
    {
        parent::__construct();

        $this->parseArguments();
        $this->checkFolderExist();

        $this->config = require_once __DIR__ . '/config/config.php';
    }

    public function run()
    {
        if (!$this->handler = $this->searchHandler()) {
            $this->warning('Engine not defined');
            $this->endWarning();
        }

        /** @var HandlerInterface $this->handler */
        $this->info('Engine defined: ' . $this->handler->getEngineName());

        $this->message('Start backup...');
        $this->handler->doBackup();
        $this->info('Success');

        exit(0);
    }

    /**
     * @return ?HandlerInterface
     * @throws HandlerException
     */
    private function searchHandler()
    {
        $currentHandler = null;

        foreach ($this->config['handlers'] as $handlerClass) {
            /** @var HandlerInterface $handlerClass */
            if ($handlerClass::checkIsEngine($this->wwwFolder)) {
                if (!empty($currentHandler)) {
                    throw new HandlerException('Several options were found: ' .
                        $currentHandler::ENGINE_NAME . ' and ' .
                        $handlerClass::ENGINE_NAME
                    );
                }

                $currentHandler = $handlerClass;
            }
        }

        return $currentHandler ? new $handlerClass($this->wwwFolder) : null;
    }

    private function parseArguments()
    {
        if (!isset($GLOBALS['argc']) || !isset($GLOBALS['argv'])) {
            $this->warning('Can not read arguments. Used default www folder');
            $this->setDefaultFolder();
        } elseif ($GLOBALS['argc'] > 2) {
            $this->warning('More than 1 argument found. Used default www folder');
            $this->setDefaultFolder();
        } elseif ($GLOBALS['argc'] === 2) {
            $this->wwwFolder = rtrim($GLOBALS['argv'][1], "/");
        } else {
            $this->setDefaultFolder();
        }
    }

    private function setDefaultFolder()
    {
        $this->wwwFolder = '~/www';
    }

    private function checkFolderExist()
    {
        if (!file_exists($this->wwwFolder)) {
            throw new FolderNotFoundException();
        }
    }
}
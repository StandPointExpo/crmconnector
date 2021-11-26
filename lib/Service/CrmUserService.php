<?php

namespace OCA\CrmConnector\Service;

use OCP\IConfig;

class CrmUserService
{
    private $config;
    private $appName;

    public function __construct(IConfig $config, $appName){
        var_dump($config);
        die();
        $this->config = $config;
        $this->appName = $appName;
    }

    public function getUserValue($key, $userId) {
        return $this->config->getUserValue($userId, $this->appName, $key);
    }

    public function setUserValue($key, $userId, $value) {
        $this->config->setUserValue($userId, $this->appName, $key, $value);
    }
}
<?php

namespace OCA\CrmConnector\Service;

use OCP\IConfig;
use OCP\IRequest;

class CrmUserService
{
    private IConfig $config;
    private $appName;
    private IRequest $request;

    public function __construct(IConfig $config, $appName, IRequest $request)
    {
        $this->config = $config;
        $this->appName = $appName;
        $this->request = $request;
    }

    public function getCrmUser(): string
    {
        return $this->request->getHeader('Authorization');
    }

    public function getUserValue($key, $userId): string
    {
        return $this->config->getUserValue($userId, $this->appName, $key);
    }

    public function setUserValue($key, $userId, $value)
    {
        $this->config->setUserValue($userId, $this->appName, $key, $value);
    }
}
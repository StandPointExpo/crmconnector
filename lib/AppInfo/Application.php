<?php

namespace OCA\CrmConnector\AppInfo;

use OCA\CrmConnector\Service\CrmUserService;
use OCP\AppFramework\App;
use OCP\AppFramework\Bootstrap\IBootContext;
use OCP\AppFramework\Bootstrap\IBootstrap;
use OCP\AppFramework\Bootstrap\IRegistrationContext;
use OCP\AppFramework\IAppContainer;

class Application extends App implements IBootstrap
{

    public const APP_ID = 'crmconnector';
    private IAppContainer $container;

    /**
     * Define your dependencies in here
     */
    public function __construct(array $urlParams = array())
    {
        parent::__construct(self::APP_ID, $urlParams);
        $this->container = $this->getContainer();
    }

    public function register(IRegistrationContext $context): void
    {
        $crmUserService = $this->container->get(CrmUserService::class);
        $crmUserService->activeCrmUser();
    }

    public function boot(IBootContext $context): void
    {
        // TODO: Implement boot() method.
    }
}
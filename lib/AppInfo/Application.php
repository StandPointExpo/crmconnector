<?php

namespace OCA\CrmConnector\AppInfo;

use OCA\CrmConnector\Controller\CrmFileApiController;
use OCA\CrmConnector\Service\CrmUserService;
use OCP\AppFramework\App;
use OCP\AppFramework\Bootstrap\IRegistrationContext;
use OCP\AppFramework\IAppContainer;
use OCP\IL10N;
use OCP\IServerContainer;

class Application extends App
{
    public const APP_ID = 'crmconnector';

    /**
     * Define your dependencies in here
     */
    public function __construct(array $urlParams = array())
    {
        parent::__construct(self::APP_ID, $urlParams);

        $container = $this->getContainer();

        /**
         * Service
         */
        $container->registerService('CrmUserService', function ($c) {
            return new CrmUserService(
                $c->query('Config'),
                $c->query('AppName')
            );
        });

        /**
         * Controller
         */
        $container->registerService('CrmFileApiController', function (IAppContainer $appContainer) {

            $server = $appContainer->get(IServerContainer::class);

            return new CrmFileApiController(
                $appContainer->get('AppName'),
                $server->getRequest(),
                $appContainer->get('UserManager'),
                $server->getConfig(),
                $appContainer->get(IL10N::class),
                $appContainer->get('Session'),
                $server->getURLGenerator()
            );
        });

    }
//
//    public function register(IRegistrationContext $context): void
//    {
//
//        /**
//         * Middleware
//         */
//        $context->registerService('CrmUserService', function ($c) {
//            return new CrmUserService();
//        });
//    }
}
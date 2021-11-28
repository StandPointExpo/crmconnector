<?php

namespace OCA\CrmConnector\AppInfo;

use OCA\CrmConnector\Service\CrmUserService;
use OCP\AppFramework\App;
use OCP\AppFramework\Bootstrap\IRegistrationContext;
use OCP\IRequest;

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
         * Controllers
         */

        $crmUserService = $container->get(CrmUserService::class);

        var_dump($crmUserService->getCrmUser());
        die();


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
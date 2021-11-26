<?php
/**
 * Create your routes in here. The name is the lowercase name of the controller
 * without the controller part, the stuff after the hash is the method.
 * e.g. page#index -> OCA\CrmConnector\Controller\PageController->index()
 *
 * The controller class has to be registered in the application.php file since
 * it's instantiated in there
 */
return [
    'routes' => [
	   ['name' => 'CrmFileApi#index', 'url' => '/api/v1/crm-files', 'verb' => 'GET'],
	   ['name' => 'CrmFileApi#upload', 'url' => '/api/v1/crm-files', 'verb' => 'POST'],
	   ['name' => 'CrmFileApi#download', 'url' => '/api/v1/crm-files/{uuid}', 'verb' => 'GET'],
//	   ['name' => 'page#index', 'url' => '/', 'verb' => 'GET'],
//	   ['name' => 'page#do_echo', 'url' => '/echo', 'verb' => 'POST'],
    ]
];

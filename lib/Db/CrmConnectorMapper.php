<?php

namespace OCA\CrmConnector\Db;

use \OCP\AppFramework\Db\QBMapper;
use OCP\IDBConnection;
use OCP\IUser;

class CrmConnectorMapper extends QBMapper
{
    public function __construct(IDBConnection $db) {
        parent::__construct($db, 'myapp_authors');
    }
}
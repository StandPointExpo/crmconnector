<?php

namespace OCA\CrmConnector\Db;

use \OCP\AppFramework\Db\QBMapper;
use OCP\IDBConnection;
use OCP\IUser;

class CrmConnectorMapper extends QBMapper
{
    public const CRM_CONNECTOR_TABLE_TOKENS_NAME = 'crm_connector_tokens';
    public const CRM_CONNECTOR_TABLE_USERS_NAME = 'crm_connector_users';
    public const CRM_CONNECTOR_TABLE_FILES_NAME = 'crm_connector_files';
    public const CRM_CONNECTOR_TABLE_SHARE_NAME = 'crm_connector_share';
    public const OCA_TABLE_FILECACHE_NAME = 'filecache';

    public function __construct(IDBConnection $db) {
        parent::__construct($db, 'myapp_authors');
    }
}
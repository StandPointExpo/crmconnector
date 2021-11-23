<?php

namespace OCA\CrmConnector\Db;


use \OCP\AppFramework\Db\QBMapper;
use OCP\DB\Types;
use OCP\IDBConnection;
use OCP\IUser;

class CrmConnectorTypes
{
    public const CRM_CONNECTOR_TABLE_TOKENS_NAME = 'crm_tokens';
    public const CRM_CONNECTOR_TABLE_USERS_NAME = 'crm_users';
    public const CRM_CONNECTOR_TABLE_FILES_NAME = 'crm_files';
    public const CRM_CONNECTOR_TABLE_SHARE_NAME = 'crm_share';
    public const OCA_TABLE_FILECACHE_NAME = 'filecache';

    /**
     * @var string
     */
    public const BIGINT = 'bigint';

    /**
     * @var string
     */
    public const BINARY = 'binary';

    /**
     * @var string
     */
    public const BLOB = 'blob';

    /**
     * @var string
     */
    public const BOOLEAN = 'boolean';

    /**
     * @var string
     */
    public const DATE = 'date';

    /**
     * @var string
     */
    public const DATETIME = 'datetime';

    /**
     * @var string
     */
    public const DECIMAL = 'decimal';

    /**
     * @var string
     */
    public const FLOAT = 'float';

    /**
     * @var string
     * @since 21.0.0
     */
    public const INTEGER = 'integer';

    /**
     * @var string
     */
    public const SMALLINT = 'smallint';

    /**
     * @var string
     */
    public const STRING = 'string';

    /**
     * @var string
     */
    public const TEXT = 'text';

    /**
     * @var string
     */
    public const TIME = 'time';


}
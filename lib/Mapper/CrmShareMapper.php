<?php

namespace OCA\CrmConnector\Mapper;

use OCA\CrmConnector\Db\CrmConnectorTypes;
use OCA\CrmConnector\Db\CrmToken;
use OCP\AppFramework\Db\Entity;
use OCP\AppFramework\Db\QBMapper;
use OCP\DB\Exception;
use OCP\IDBConnection;

class CrmShareMapper extends QBMapper
{
    public function __construct(IDBConnection $db)
    {
        parent::__construct($db, CrmConnectorTypes::CRM_CONNECTOR_TABLE_SHARE_NAME, CrmToken::class);
    }

    /**
     * @param string $uuid
     * @return void $fetch
     * @throws Exception
     */
    public function getCrmShare(string $uuid, int $userId)
    {
        $db = $this->db->getQueryBuilder();
        $result = $db->select('*')
            ->from($this->getTableName())
            ->where(
                $db->expr()->eq('user_id', $db->createNamedParameter($userId, 'integer'))
            )
            ->andWhere(
                $db->expr()->eq('crm_file_uuid', $db->createNamedParameter($uuid, 'string'))
            )->execute();
        $fetch = $result->fetch();
        $result->closeCursor();
        return $fetch;
    }

    /**
     * @throws Exception
     */
    public function insertOrUpdate(Entity $entity): Entity
    {
        return parent::insertOrUpdate($entity); // TODO: Change the autogenerated stub
    }
}
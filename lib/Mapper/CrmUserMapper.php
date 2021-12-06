<?php

namespace OCA\CrmConnector\Mapper;

use OCA\CrmConnector\Db\CrmConnectorTypes;
use OCA\CrmConnector\Db\CrmUser;
use OCP\AppFramework\Db\Entity;
use \OCP\AppFramework\Db\QBMapper;
use OCP\DB\Exception;
use OCP\IDBConnection;
use OCP\IUser;

class CrmUserMapper extends QBMapper
{
    public function __construct(IDBConnection $db)
    {
        parent::__construct($db, CrmConnectorTypes::CRM_CONNECTOR_TABLE_USERS_NAME, CrmUser::class);
    }

    /**
     * @throws Exception
     */
    public function getUser(int $id) {
        $db = $this->db->getQueryBuilder();
        $result = $db->select('*')
            ->from($this->getTableName())
            ->where(
                $db->expr()->eq('id', $db->createNamedParameter($id, 'integer'))
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
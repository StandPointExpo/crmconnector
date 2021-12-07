<?php

namespace OCA\CrmConnector\Mapper;

use OCA\CrmConnector\Db\CrmConnectorTypes;
use OCA\CrmConnector\Db\CrmFile;
use OCA\CrmConnector\Exception\FileExtException;
use OCP\AppFramework\Db\Entity;
use \OCP\AppFramework\Db\QBMapper;
use OCP\DB\Exception;
use OCP\IDBConnection;
use OCP\IUser;

class CrmFileMapper extends QBMapper
{
    public function __construct(IDBConnection $db)
    {
        parent::__construct($db, CrmConnectorTypes::CRM_CONNECTOR_TABLE_FILES_NAME, CrmFile::class);
    }

    /**
     * @throws Exception
     */
    public function insertFile(Entity $entity): Entity
    {
        return parent::insert($entity);
    }

    /**
     * @throws Exception
     */
    public function getFile(int $id) {
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
    public function getUuidFile(string $uuid) {
        $db = $this->db->getQueryBuilder();
        $result = $db->select('*')
            ->from($this->getTableName())
            ->where(
                $db->expr()->eq('uuid', $db->createNamedParameter($uuid, 'string'))
            )->execute();
        $fetch = $result->fetch();
        $result->closeCursor();
        return $fetch;
    }

    public function createFileFromRow(array $row): CrmFile
    {
        return $this->mapRowToEntity([
            'id' => $row['id'],
            'user_id' => $row['user_id'],
            'uuid' => $row['uuid'],
            'publication' => $row['publication'],
            'file_original_name' => $row['file_original_name'],
            'file_type' => $row['file_type'],
            'file_source' => $row['file_source'],
            'extension' => $row['extension'],
            'deleted_at' => $row['deleted_at'],
            'created_at' => $row['created_at'],
            'updated_at' => $row['updated_at']
        ]);
    }
}
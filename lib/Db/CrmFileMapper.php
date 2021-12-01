<?php

namespace OCA\CrmConnector\Db;

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

    /**
     * @param $file
     * @return string
     */
    public function getType($file): ?string
    {
        $ext = mb_strtolower($file->getClientOriginalExtension());

        if (in_array($ext, CrmFile::IMAGE_EXT)) {
            return 'image';
        }

        if (in_array($ext, CrmFile::PROJECT_AUDIO_EXT)) {
            return 'audio';
        }

        if (in_array($ext, CrmFile::PROJECT_VIDEO_EXT)) {
            return 'video';
        }

        if (in_array($ext, CrmFile::PROJECT_DOCUMENT_EXT)) {
            return 'document';
        }

        if (in_array($ext, CrmFile::DOCUMENT_EXT)) {
            return 'file';
        }
        throw new FileExtException($file->getClientOriginalName());
    }
}
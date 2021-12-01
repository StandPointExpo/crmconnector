<?php

namespace OCA\CrmConnector\Service;

use OCA\CrmConnector\Db\CrmFile;
use OCA\CrmConnector\Db\CrmFileMapper;
use OCP\DB\Exception;

class CrmFileService
{
    /**
     * @var CrmFileMapper
     */
    private CrmFileMapper $crmFileMapper;

    public function __construct(CrmFileMapper $crmFileMapper)
    {
        $this->crmFileMapper = $crmFileMapper;
    }

    /**
     * @param array $dataFile
     * @throws Exception
     */
    public function create(array $dataFile) {
        $file = new CrmFile();
        $file->setId($dataFile['id']);
        $file->setUserId($dataFile['user_id']);
        $file->setUuid($dataFile['uuid']);
        $file->setPublication($dataFile['publication']);
        $file->setFileOriginalName($dataFile['file_original_name']);
        $file->setFileType($dataFile['file_type']);
        $file->setFileSource($dataFile['file_source']);
        $file->setFileShare($dataFile['file_share']);
        $file->setExtension($dataFile['extension']);
        $file->setDeletedAt($dataFile['deleted_at']);
        $file->setCreatedAt($dataFile['created_at']);
        $file->setUpdatedAt($dataFile['updated_at']);

        $this->crmFileMapper->insertFile($file);
    }

}
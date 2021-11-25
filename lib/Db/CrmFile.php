<?php

namespace OCA\CrmConnector\Db;

use OCP\AppFramework\Db\Entity;

/**
 * @method void setId(int $id)
 * @method int getId()
 * @method void setUserId(int $userID)
 * @method int getUserId()
 * @method void setUuid(string $uuid)
 * @method int getUuid()
 * @method void setPublication(bool $publication)
 * @method bool isPublication()
 * @method void setFileOriginalName(string $fileOriginalName)
 * @method string getFileOriginalName()
 * @method void setFileType(string $fileType)
 * @method string getFileType()
 * @method void setFileSource(string $fileSource)
 * @method string getFileSource()
 * @method void setFileShare(string $fileShare)
 * @method string getFileShare()
 * @method void setExtension(string $extension)
 * @method string getExtension()
 * @method void setDeletedAt(mixed $deletedAt)
 * @method mixed getDeletedAt()
 * @method void setCreatedAt(mixed $createdAt)
 * @method mixed getCreatedAt()
 * @method void setUpdatedAt(mixed $updatedAt)
 * @method mixed getUpdatedAt()
 */
class CrmFile extends Entity
{
    /** @var int */
    protected $userId;

    /** @var string */
    protected $uuid;

    /** @var bool */
    protected $publication;

    /** @var string */
    protected $fileOriginalName;

    /** @var string */
    protected $fileType;

    /** @var string */
    protected $fileSource;

    /** @var string */
    protected $fileShare;

    /** @var string */
    protected $extension;

    /** @var mixed */
    protected $deletedAt;

    /** @var mixed */
    protected $createdAt;

    /** @var mixed */
    protected $updatedAt;

    public function __construct()
    {
        $this->addType('userId', 'integer');
        $this->addType('uuid', 'string');
        $this->addType('publication', 'bool');
        $this->addType('fileOriginalName', 'string');
        $this->addType('fileType', 'string');
        $this->addType('fileSource', 'string');
        $this->addType('fileShare', 'string');
        $this->addType('extension', 'string');
        $this->addType('deletedAt', 'string');
        $this->addType('createdAt', 'string');
        $this->addType('updatedAt', 'string');
    }

    /**
     * @return array
     */
    public function asArray(): array
    {
        return [
            'id' => $this->getId(),
            'user_id' => $this->getUserId(),
            'uuid' => $this->getUuid(),
            'publication' => $this->isPublication(),
            'file_original_name' => $this->getFileOriginalName(),
            'file_type' => $this->getFileType(),
            'file_source' => $this->getFileSource(),
            'file_share' => $this->getFileShare(),
            'extension' => $this->getExtension(),
            'deleted_at' => $this->getDeletedAt(),
            'created_at' => $this->getCreatedAt(),
            'updated_at' => $this->getUpdatedAt(),
        ];
    }


}
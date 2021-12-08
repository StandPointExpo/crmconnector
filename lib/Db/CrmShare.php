<?php

namespace OCA\CrmConnector\Db;


use OCP\AppFramework\Db\Entity;

/**
 * @method void setId(int $id)
 * @method int getId()
 * @method void setUserId(int $userId)
 * @method string getUserId()
 * @method void setFileid(int $fileid)
 * @method string getFileid()
 * @method void setCrmFileUuid(string $crmFileUuid)
 * @method string getCrmFileUuid()
 * @method void setToken(string $shareToken)
 * @method string getToken()
 * @method void setCreatedAt(mixed $createdAt)
 * @method mixed getCreatedAt()
 * @method void setUpdatedAt(mixed $updatedAt)
 * @method mixed getUpdatedAt()
 */
class CrmShare extends Entity
{
    protected $userId;

    protected $fileid;

    protected $crmFileUuid;

    protected $token;

    protected $createdAt;

    protected $updatedAt;

    public function __construct()
    {
        $this->addType('user_id', 'integer');
        $this->addType('fileid', 'integer');
        $this->addType('crm_file_uuid', 'string');
        $this->addType('token', 'string');
        $this->addType('created_at', 'string');
        $this->addType('updated_at', 'string');
    }

    /**
     * @return array
     */
    public function asArray(): array
    {
        return [
            'id' => $this->getId(),
            'user_id' => $this->getUserId(),
            'fileid' => $this->getFileid(),
            'crm_file_uuid' => $this->getCrmFileUuid(),
            'token' => $this->getToken(),
            'created_at' => $this->getCreatedAt(),
            'updated_at' => $this->getUpdatedAt()
        ];
    }
}
<?php

namespace OCA\CrmConnector\Db;


use OCP\AppFramework\Db\Entity;

/**
 * @method void setId(int $id)
 * @method int getId()
 * @method void setUserId(int $userId)
 * @method string getUserId()
 * @method void setToken(string $token)
 * @method string getToken()
 * @method void setLastUsedAt(mixed $updatedAt)
 * @method mixed getLastUsedAt()
 */
class CrmToken extends Entity
{
    public const APP_TOKEN = 'secretToken';

    protected $userId;

    protected $token;

    protected $lastUsedAt;

    public function __construct()
    {
        $this->addType('user_id', 'integer');
        $this->addType('token', 'string');
        $this->addType('last_used_at', 'string');
    }

    /**
     * @return array
     */
    public function asArray(): array
    {
        return [
            'id' => $this->getId(),
            'user_id' => $this->getUserId(),
            'token' => $this->getToken(),
            'last_used_at' => $this->getLastUsedAt()
        ];
    }
}
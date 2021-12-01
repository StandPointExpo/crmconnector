<?php

namespace OCA\CrmConnector\Db;

use OCP\AppFramework\Db\Entity;

/**
 * @method void setId(int $id)
 * @method int getId()
 * @method void setName(string $name)
 * @method string getName()
 * @method void setEmail(string $email)
 * @method string getEmail()
 * @method void setCreatedAt(mixed $createdAt)
 * @method mixed getCreatedAt()
 * @method void setUpdatedAt(mixed $updatedAt)
 * @method mixed getUpdatedAt()
 */
class CrmUser extends Entity
{
    /** @var string */
    protected $name;

    /** @var string */
    protected $email;

    /** @var string */
    protected $createdAt;

    /** @var string */
    protected $updatedAt;

    public function __construct()
    {
        $this->addType('name', 'string');
        $this->addType('email', 'string');
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
            'name' => $this->getName(),
            'email' => $this->getEmail(),
            'created_at' => $this->getCreatedAt(),
            'updated_at' => $this->getUpdatedAt(),
        ];
    }
}
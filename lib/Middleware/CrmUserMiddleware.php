<?php

namespace OCA\CrmConnector\Middleware;

use OCA\CrmConnector\Db\CrmToken;
use OCA\CrmConnector\Db\CrmUser;
use OCA\CrmConnector\Exception\UserException;
use OCA\CrmConnector\Mapper\CrmTokenMapper;
use OCA\CrmConnector\Mapper\CrmUserMapper;
use OCP\AppFramework\Http\Response;
use \OCP\AppFramework\Middleware;
use OCP\IRequest;

class CrmUserMiddleware extends Middleware
{

    private CrmUserMapper $crmUserMapper;

    private CrmTokenMapper $crmTokenMapper;

    public function __construct(CrmUserMapper $crmUserMapper, CrmTokenMapper $crmTokenMapper)
    {
        $this->crmUserMapper = $crmUserMapper;
        $this->crmTokenMapper = $crmTokenMapper;
    }

    /**
     *
     * @throws \Exception
     */
    public function authUser(IRequest $request)
    {

        $userId = $this->crmTokenMapper->getCrmTokenUserId($request->getHeader('Authorization'));
        if (!$userId) {
            throw new UserException();
        }
        return $this->crmUserMapper->getUser($userId['user_id']);
    }
}
<?php

namespace OCA\CrmConnector\Service;

use OCA\CrmConnector\Db\CrmUser;
use OCA\CrmConnector\Mapper\CrmFileMapper;
use OCA\CrmConnector\Db\CrmToken;
use OCA\CrmConnector\Mapper\CrmTokenMapper;
use OCP\DB\Exception;
use OCP\IRequest;

class CrmTokenService
{
    /**
     * @var CrmTokenMapper
     */
    private CrmTokenMapper $crmTokenMapper;

    private IRequest $request;

    public function __construct(
        IRequest $request,
        CrmTokenMapper $crmTokenMapper
    )
    {
        $this->request = $request;
        $this->crmTokenMapper = $crmTokenMapper;
    }

    /**
     * @param array $user
     * @throws Exception
     */
    public function saveToken(array $user) {
        $tokenData = [];
        $tokenData['user_id'] = $user['id'];
        $tokenData['token'] = $this->request->getHeader('Authorization');
        $this->save($tokenData);
    }

    /**
     * @param array $dataToken
     * @throws Exception
     */
    public function save(array $dataToken) {
        $token = new CrmToken();
        $token->setUserId($dataToken['user_id']);
        $token->setToken($dataToken['token']);
        $token->setLastUsedAt(date('Y-m-d H:i:s', time()));

        $this->crmTokenMapper->insertOrUpdate($token);
    }
}
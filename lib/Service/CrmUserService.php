<?php

namespace OCA\CrmConnector\Service;

use OCA\CrmConnector\Db\CrmUser;
use OCA\CrmConnector\Db\CrmUserMapper;
use OCP\DB\Exception;
use OCP\IConfig;
use OCP\IRequest;

class CrmUserService
{
    private IConfig $config;
    private $appName;
    private IRequest $request;
    private CrmUserMapper $crmUserMapper;

    public function __construct(IConfig $config, $appName, IRequest $request, CrmUserMapper $crmUserMapper)
    {
        $this->config = $config;
        $this->appName = $appName;
        $this->request = $request;
        $this->crmUserMapper = $crmUserMapper;
    }

    /**
     * @throws \Exception
     */
    public function activeCrmUser(): array
    {
        $userData = $this->getCrmUser();
        $this->insertOrUpdateUser($userData);
    }

    /**
     * @throws \Exception
     */
    public function getCrmUser(): array
    {

        return $this->curlApiResource($this->request->getHeader('Authorization'));
    }

    /**
     * @throws \Exception
     */
    public function curlApiResource(string $token)
    {
        try {
            $computeApi = $this->config->getSystemValue('compute_api');
            if ($computeApi === '') {
                throw new \Exception('Please set compute_api url on config.php');
            }
            $cApiConnection = curl_init($computeApi . '/api/auth/user');

            curl_setopt($cApiConnection, CURLOPT_HTTPHEADER, [
                'Accept: application/json',
                "Authorization: $token"
            ]);
            curl_setopt($cApiConnection, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($cApiConnection, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($cApiConnection, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($cApiConnection, CURLOPT_CONNECTTIMEOUT, 5);
            curl_setopt($cApiConnection, CURLOPT_TIMEOUT, 3);

            $response = curl_exec($cApiConnection);
            $errno = curl_errno($cApiConnection);
            $error = curl_error($cApiConnection);
            curl_close($cApiConnection);
            if ($errno > 0) {
                throw new \Exception("CURL Error ($errno): $error");
            }

            $data = json_decode($response, true);
            return $data['data'];
        } catch (\Throwable $exception) {
            throw new \Exception($exception->getMessage());
        }

    }

    public function getUserValue($key, $userId): string
    {
        return $this->config->getUserValue($userId, $this->appName, $key);
    }

    public function setUserValue($key, $userId, $value)
    {
        $this->config->setUserValue($userId, $this->appName, $key, $value);
    }

    /**
     * @throws Exception
     */
    public function insertOrUpdateUser(array $userData)
    {
        $user = new CrmUser();
        $user->setId($userData['id']);
        $user->setName($userData['name']);
        $user->setEmail($userData['email']);
        $user->setCreatedAt(date('Y-m-d H:i:s', time()));
        $user->setUpdatedAt(date('Y-m-d H:i:s', time()));

        $this->crmUserMapper->insertUser($user);
    }
}
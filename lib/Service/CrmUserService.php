<?php

namespace OCA\CrmConnector\Service;

use OCP\IConfig;
use OCP\IRequest;

class CrmUserService
{
    private IConfig $config;
    private $appName;
    private IRequest $request;

    public function __construct(IConfig $config, $appName, IRequest $request)
    {
        $this->config = $config;
        $this->appName = $appName;
        $this->request = $request;
    }

    /**
     * @throws \Exception
     */
    public function activeCrmUser(): array
    {

        $user = $this->getCrmUser();
        //отріматі актівного корістувача и записати дани в базу для подальшої обробки
        // написати модель для запису користувача
        var_dump($user);
        die();
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
            if($errno > 0) {
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
}
<?php

namespace OCA\CrmConnector\Service;

use OCA\CrmConnector\Db\CrmFile;
use OCA\CrmConnector\Exception\UserException;
use OCA\CrmConnector\Mapper\CrmFileMapper;
use OCP\AppFramework\Db\Entity;
use OCP\DB\Exception;
use OCP\IConfig;
use OCP\IRequest;

class CrmFileService
{
    /**
     * @var CrmFileMapper
     */
    private CrmFileMapper $crmFileMapper;

    private IConfig $config;

    public function __construct(IConfig $config,CrmFileMapper $crmFileMapper)
    {
        $this->crmFileMapper = $crmFileMapper;
        $this->config = $config;
    }

    /**
     * @param array $dataFile
     * @throws Exception
     */
    public function create(array $dataFile): Entity
    {
        $file = new CrmFile();
        $file->setUserId($dataFile['user_id']);
        $file->setUuid($dataFile['uuid']);
        $file->setPublication($dataFile['publication']);
        $file->setFileOriginalName($dataFile['file_original_name']);
        $file->setFileType($dataFile['file_type']);
        $file->setFileSource($dataFile['file_source']);
        $file->setFileShare($dataFile['file_share']);
        $file->setExtension($dataFile['extension']);
        $file->setCreatedAt($dataFile['created_at']);
        $file->setUpdatedAt($dataFile['updated_at']);

        return $this->crmFileMapper->insertFile($file);
    }

    /**
     * @throws \Exception
     */
    public function curlApiFileResource(string $token, $file, IRequest $request)
    {
        try {
            $computeApi = $this->config->getSystemValue('compute_api');
            if ($computeApi === '') {
                /**
                 * Set compute_api url to backend server api, where get active user data
                 * */
                throw new \Exception('Please set compute_api url on config.php');
            }
            $cApiConnection = curl_init($computeApi . '/api/v1/file-manager/files');

            $foldersThree = json_decode($request->getParam('folders_tree'), true);
            $fields = [];
            $fields['dir_name'] = array_pop($foldersThree);
            $fields['file_data'] = $file;
            $fields['project_id'] = $request->getParam('project_id');
            $fields['fileable_id'] = $request->getParam('fileable_id');
            $fields['fileable_type'] = $request->getParam('fileable_type');
            $fields['file_position'] = $request->getParam('file_position');

            $fields = http_build_query($fields);

            curl_setopt($cApiConnection, CURLOPT_HTTPHEADER, [
                "Authorization: $token",
                "Content-Length: " . strlen($fields)
            ]);

//            curl_setopt($cApiConnection, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($cApiConnection, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($cApiConnection, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($cApiConnection, CURLOPT_CONNECTTIMEOUT, 5);
            curl_setopt($cApiConnection, CURLOPT_TIMEOUT, 3);

            curl_setopt($cApiConnection, CURLOPT_POSTFIELDS, $fields);

            $response = curl_exec($cApiConnection);

            $errno = curl_errno($cApiConnection);
            $error = curl_error($cApiConnection);
            curl_close($cApiConnection);
            if ($errno > 0) {
                throw new \Exception("CURL Error ($errno): $error");
            }

            $data = json_decode($response, true);
//            var_dump($data);

            if (!isset($data['status']) && !isset($data['data']['id'])) {
                throw new UserException();
            }
            return $data['data'];
        } catch (\Throwable $exception) {
            throw new \Exception($exception->getMessage());
        }

    }

}
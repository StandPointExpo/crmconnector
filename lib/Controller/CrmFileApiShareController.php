<?php

namespace OCA\CrmConnector\Controller;

use OC\User\NoUserException;
use OCA\CrmConnector\Db\CrmFile;
use OCA\CrmConnector\Db\CrmShare;
use OCA\CrmConnector\Db\CrmToken;
use OCA\CrmConnector\Mapper\CrmFileMapper;
use OCA\CrmConnector\Mapper\CrmShareMapper;
use OCA\CrmConnector\Middleware\CrmUserMiddleware;
use OCA\CrmConnector\Traits\CrmConnectionResponse;
use OCA\CrmConnector\Traits\CrmFileTrait;
use OCA\Files_Sharing\Controller\ShareAPIController;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Http\JSONResponse;
use OCP\AppFramework\PublicShareController;
use OCP\Files\IRootFolder;
use OCP\IConfig;
use OCP\IRequest;
use OCP\Share\IManager;

class CrmFileApiShareController extends PublicShareController
{
    use CrmFileTrait;
    use CrmConnectionResponse;

    private $user;
    private $crmFileMapper;
    private $storage;
    private $config;
    private IManager $shareManager;
    private ShareAPIController $shareAPIController;
    private CrmShareMapper $crmShareMapper;
    private $middleware;

    /**
     * @throws \Exception
     */
    public function __construct(
        IConfig            $config,
        IRequest           $request,
        IRootFolder        $storage,
        IManager           $shareManager,
        CrmFileMapper      $crmFileMapper,
        CrmShareMapper     $crmShareMapper,
        CrmUserMiddleware  $crmUserMiddleware,
        ShareAPIController $shareAPIController
    )
    {
        $this->request = $request;
        $this->middleware = $crmUserMiddleware;
        $this->user = $this->middleware->authUser($this->request);
        $this->shareManager = $shareManager;
        $this->config = $config;
        $this->storage = $storage;
        $this->crmFileMapper = $crmFileMapper;
        $this->crmShareMapper = $crmShareMapper;
        $this->shareAPIController = $shareAPIController;
    }

    /**
     * Return the hash of the password for this share.
     * This function is of course only called when isPasswordProtected is true
     */
    protected function getPasswordHash(): string
    {
        return md5('secretpassword');
    }

    /**
     * Validate the token of this share. If the token is invalid this controller
     * will return a 404.
     */
    public function isValidToken(): bool
    {
        return $this->getToken() === CrmToken::APP_TOKEN;
    }

    /**
     * Allows you to specify if this share is password protected
     */
    protected function isPasswordProtected(): bool
    {
        return false;
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     * @PublicPage
     * @param string $uuid
     * @throws \OCP\DB\Exception
     */
    public function share(string $uuid): JSONResponse
    {
        try {

            $share = $this->createShare($uuid);
            return $this->success(
                $this->config->getSystemValue('storage_url') .
                '/index.php/s/' .
                $share['token']);

        } catch (\Exception $e) {
            return $this->fail($e);
        }
    }

    /**
     * @param string $uuid
     * @throws \OCP\DB\Exception
     * @throws \OCP\Files\NotFoundException
     */
    public function createShare(string $uuid)
    {
        $file = $this->crmFileMapper->getUuidFile($uuid);
        $userFolder = $this->storage->getUserFolder(CrmFile::CRM_USER);
        $activeFile = $this->getActiveFolder($userFolder, $file['file_source']);
        if ($activeFile->getPath()) {
            $share = $this->shareManager->newShare();
            $share->setNode($activeFile);
            $share->setShareType(3);
            $share->setSharedBy(CrmFile::CRM_USER);
            $share->setPermissions(17);
            $share = $this->shareManager->createShare($share);
            $this->saveCrmShare($share, $uuid);
            return [
                'token' => $share->getToken()
            ];
        }
    }

    /**
     * @return mixed
     * @throws \OCP\DB\Exception
     */
    public function saveCrmShare($share, string $uuid)
    {

        $crmShare = new CrmShare();
        $crmShare->setUserId($this->user['id']);
        $crmShare->setFileid($share->getNodeId());
        $crmShare->setCrmFileUuid($uuid);
        $crmShare->setToken($share->getToken());
        $crmShare->setCreatedAt(date('Y-m-d H:i:s', time()));
        $crmShare->setUpdatedAt(date('Y-m-d H:i:s', time()));
        $this->crmShareMapper->insertOrUpdate($crmShare);
    }

}

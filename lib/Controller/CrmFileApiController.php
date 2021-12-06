<?php

namespace OCA\CrmConnector\Controller;

use OCA\Crmconnector\Db\CrmFile;
use OC\IntegrityCheck\Exceptions\InvalidSignatureException;
use OCA\CrmConnector\Mapper\CrmFileMapper;
use OCA\CrmConnector\Middleware\CrmUserMiddleware;
use OCA\CrmConnector\Migration\SeedsStep;
use OCA\CrmConnector\Requests\CrmFileRequest;
use OCA\CrmConnector\Service\CrmFileService;
use OCP\AppFramework\PublicShareController;
use OCP\Files\IRootFolder;
use OCP\IConfig;
use OCP\IRequest;
use OCP\ISession;
use OCP\Files\IAppData;
use OCA\CrmConnector\Traits\CrmConnectionResponse;
use OCP\IUser;

/**
 * This is the implementation of the server side part of
 * Resumable.js client script, which sends/uploads files
 * to a server in several chunks.
 *
 * The script receives the files in a standard way as if
 * the files were uploaded using standard HTML form (multipart).
 *
 * This PHP script stores all the chunks of a file in a temporary
 * directory (`temp`) with the extension `_part<#ChunkN>`. Once all
 * the parts have been uploaded, a final destination file is
 * being created from all the stored parts (appending one by one).
 *
 * @author Gregory Chris (http://online-php.com)
 * @email www.online.php@gmail.com
 *
 * @editor Bivek Joshi (http://www.bivekjoshi.com.np)
 * @email meetbivek@gmail.com
 */
class CrmFileApiController extends PublicShareController
{

    use CrmConnectionResponse;

    /**
     * @var string
     */
    private $uploadsDir;

    /**
     * @var string
     */
    private $projectsDir;

    /** @var IAppData */
    private $appData;

    private SeedsStep $seedsStep;

    private mixed $files;

    private CrmFileRequest $crmFileRequest;
    private IConfig $config;

    private CrmUserMiddleware $middleware;

    private $user;

    private IRootFolder $storage;

    private CrmFileService $crmFileService;

    private CrmFileMapper $crmFileMapper;

    public function __construct(
        string            $appName,
        IRequest          $request,
        ISession          $session,
        IAppData          $appData,
        IConfig           $config,
        IRootFolder       $storage,
        CrmFileRequest    $crmFileRequest,
        CrmUserMiddleware $crmUserMiddleware,
        CrmFileService    $crmFileService,
        CrmFileMapper     $crmFileMapper
    )
    {
        $this->request = $request;
        parent::__construct($appName, $request, $session);
        $this->middleware = $crmUserMiddleware;
        $this->user = $this->middleware->authUser($this->request);
        $this->appName = $appName;
        $this->crmFileRequest = $crmFileRequest;
        $this->config = $config;
        $this->storage = $storage;
        $this->crmFileService = $crmFileService;
        $this->crmFileMapper = $crmFileMapper;
        $this->appData = $appData; //https://docs.nextcloud.com/server/latest/developer_manual/basics/storage/appdata.html
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
        return $this->getToken() === 'secretToken';
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
     */
    public function upload()
    {
        try {
            $request = $this->crmFileRequest->validate();
            $reciever = new FileReceive(
                $request,
                $this->config,
                $this->storage
            );
            if ($reciever->isUploaded()) {
                $file = $reciever->uploadedFileMove();
                $file['user_id'] = $this->user['id'];
            };
            $result = $this->crmFileService->create($file);
            return $this->success($result->asArray());
        } catch (\Throwable $exception) {
            return $this->fail($exception);
        }
    }


    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     * @PublicPage
     * @param string $uuid
     */
    public function download(string $uuid): string
    {
        Доробитит скачування файла, а також шарінг
    перевірити додавання файла в базу nextcloud після завантаження
        var_dump($uuid);
        die();
        // Work your magic
        return $uuid;
    }
}
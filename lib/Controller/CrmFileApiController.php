<?php

namespace OCA\CrmConnector\Controller;

use OC\User\NoUserException;
use OCA\Crmconnector\Db\CrmFile;
use OCA\CrmConnector\Db\CrmToken;
use OCA\CrmConnector\Mapper\CrmFileMapper;
use OCA\CrmConnector\Middleware\CrmUserMiddleware;
use OCA\CrmConnector\Migration\SeedsStep;
use OCA\CrmConnector\Requests\CrmFileRequest;
use OCA\CrmConnector\Service\CrmFileService;
use OCA\CrmConnector\Traits\CrmFileTrait;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\FileDisplayResponse;
use OCP\AppFramework\PublicShareController;
use OCP\Files\IRootFolder;
use OCP\Files\NotFoundException;
use OCP\Files\NotPermittedException;
use OCP\IConfig;
use OCP\IRequest;
use OCP\ISession;
use OCP\Files\IAppData;
use OCA\CrmConnector\Traits\CrmConnectionResponse;
use OCP\AppFramework\Http\StreamResponse;
use Psr\Log\LoggerInterface;

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
    use CrmFileTrait;

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
    private LoggerInterface $logger;

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
        CrmFileMapper     $crmFileMapper,
        LoggerInterface   $logger
    )
    {
        $this->request = $request;
        $this->middleware = $crmUserMiddleware;
        $this->user = $this->middleware->authUser($this->request);
        $this->appName = $appName;
        $this->crmFileRequest = $crmFileRequest;
        $this->config = $config;
        $this->storage = $storage;
        $this->logger = $logger;
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
            $fileChunksData = $reciever->isUploaded();

            if ($fileChunksData['resumableChunkNumber'] == $fileChunksData['resumableTotalChunks']) {
                if ($reciever->createFileFromChunks($fileChunksData)) {
                    $file = $reciever->uploadedFileMove();
                    $file['user_id'] = $this->user['id'];
                    $result = $this->crmFileService->create($file);
                    return $this->success($result->asArray());
                }
            }

        } catch (\Throwable $exception) {
            return $this->fail($exception);
        }
    }

    /**
     * @throws \Exception
     */
    public function saveFileData(array $file): array
    {
        $token = $this->request->getHeader('Authorization');
        return $this->crmFileService->curlApiFileResource(
            $token,
            $file,
            $this->request);
    }


    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     * @PublicPage
     * @param string $uuid
     * @throws \OCP\DB\Exception
     */
    public function getFile(string $uuid)
    {
        try {
            $file = $this->crmFileMapper->getUuidFile($uuid);
            $userFolder = $this->storage->getUserFolder(CrmFile::CRM_USER);
            $image = $this->getActiveFolder($userFolder, $file['file_source']);
            if ($image->getPath()) {
                $response = new FileDisplayResponse($image, Http::STATUS_OK, ['Content-Type' => $image->getMimeType()]);
                $response->cacheFor(3600 * 24);
                return $response;
            }
        } catch (NotPermittedException $e) {
            return $this->fail($e);
        } catch (NoUserException $e) {
            return $this->fail($e);
        } catch (NotFoundException $e) {
            return $this->fail($e);
        }
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     * @PublicPage
     * @param string $uuid
     * @throws \OCP\DB\Exception
     */
    public function download(string $uuid)
    {
        try {
            $file = $this->crmFileMapper->getUuidFile($uuid);
            $userFolder = $this->storage->getUserFolder(CrmFile::CRM_USER);
            $activeFile = $this->getActiveFolder($userFolder, $file['file_source']);
            if ($activeFile->getPath()) {
                $uploadsDir = $this->config->getSystemValue('datadirectory', \OC::$SERVERROOT . '/data');
                return new StreamResponse($uploadsDir . $activeFile->getPath());
            }
        } catch (NotPermittedException $e) {
            return $this->fail($e);
        } catch (NoUserException $e) {
            return $this->fail($e);
        } catch (NotFoundException $e) {
            return $this->fail($e);
        }
    }
}
<?php

namespace OCA\CrmConnector\Requests;

use OCA\CrmConnector\Db\CrmFile;
use OCA\CrmConnector\Exception\FileException;
use OCA\CrmConnector\Exception\FileExtException;
use OCA\CrmConnector\Middleware\CrmUserMiddleware;
use OCP\IRequest;
use SplFileInfo;

class CrmFileRequest
{
    private $request;
    private CrmFile $crmFile;
    private CrmUserMiddleware $middleware;

    /**
     * @throws \Exception
     */
    public function __construct(IRequest $request, CrmFile $crmFile)
    {
        $this->request = $request;
        $this->crmFile = $crmFile;
    }


    /**
     * @throws \Exception
     */
    public function validate(): IRequest
    {
        $file = $this->request->getUploadedFile('file');

        if (is_null($file)) {
            throw new FileException();
        };
        $fileInfo = new SplFileInfo($file['name']);
        $extensions = $this->crmFile->validExtensions();
        $ext = strtolower($fileInfo->getExtension());
        if (in_array($ext, $extensions)) {
            return $this->request;
        }
        throw new FileExtException($file['name']);
    }
}
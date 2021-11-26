<?php

namespace OCA\CrmConnector\Requests;

use OC\IntegrityCheck\Exceptions\InvalidSignatureException;
use OCA\CrmConnector\Db\CrmFile;
use OCA\CrmConnector\Exception\FileException;
use OCA\CrmConnector\Exception\FileExtException;
use OCP\IRequest;
use SplFileInfo;

class CrmFileRequest
{
    private $request;
    private CrmFile $crmFile;

    public function __construct(IRequest $request, CrmFile $crmFile)
    {
        $this->request = $request;
        $this->crmFile = $crmFile;
    }


    /**
     */
    public function validate (): bool
    {
        $file = $this->request->getUploadedFile('file');

        if(is_null($file)) {
            throw new FileException();
        };
        $info = new SplFileInfo($file['name']);

        if(!in_array( $info->getExtension(), $this->crmFile->validExtensions())) {
            throw new FileExtException( $file['name'] );
        }

        return true;
    }
}
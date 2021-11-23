<?php

namespace OCA\CrmConnector\Controller;

use OCP\AppFramework\PublicShareController;
use OCP\IConfig;
use OCP\IRequest;
use OCP\ISession;

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
class CrmFileController extends PublicShareController
{

    /**
     * @var string
     */
    private $uploadsDir;

    /**
     * @var string
     */
    private $projectsDir;

    /** @var IRequest */
    public $request;

    public function __construct(string $appName, IRequest $request, ISession $session, IConfig $config)
    {
        parent::__construct($appName, $request, $session);
        $this->uploadsDir = $config->getSystemValue('datadirectory', \OC::$SERVERROOT . '/data');
        $this->projectsDir = '';
        $this->appName = $appName;
        $this->request = $request;
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
//        return $this->getToken() === 'secretToken';
    }

    /**
     * Allows you to specify if this share is password protected
     */
    protected function isPasswordProtected(): bool
    {
        return false;
    }

    /**
     * Move uploaded file to projects dir and remove $temp_dir
     * @param string $temp_dir
     * @param string $fileName
     * @return string $uploadedFilePath
     * */
    public function moveFile(string $temp_dir, string $fileName, $projectName, $foldersTree)
    {
        $uploadedFilePath = '';
        mkdir($this->uploadsDir);
//        $this->makeFileDir();
        if (copy("$temp_dir/$fileName", "$this->uploadsDir/$fileName")) {
            $this->_log('copy file');
        } else {
            $this->_log('not copy');
        };
        // rename the temporary directory (to avoid access from other
        // concurrent chunks uploads) and that delete it
        if (rename($temp_dir, $temp_dir . '_UNUSED')) {
            $this->rrmdir($temp_dir . '_UNUSED');
        } else {
            $this->rrmdir($temp_dir);
        }
        return $uploadedFilePath;
    }

    public function makeFileDir($projectName, $folderTree)
    {
        $foldersThree = json_decode($folderTree);
    }

    /**
     * Create valid folders three for uploaded file
     * @param array $folderThree
     * @return string
     */
    public function foldersThreeString(array $folderThree): string
    {
        return implode('/', $folderThree);
    }

//    /**
//     * Create unique filename for uploaded file
//     * @param UploadedFile $file
//     * @param $filePath
//     * @return string
//     */
//    protected function createFilename(UploadedFile $file, $filePath): string
//    {
//        return $this->checkExistFileName($file, $file->getClientOriginalName(), $filePath);
//    }
//
//    public function checkExistFileName(UploadedFile $file, $fileName, $filePath)
//    {
//        $extension = $file->getClientOriginalExtension();
//        if (Storage::disk('nextcloud')->exists("{$filePath}/$fileName")) {
//            $clearFilename = trim(str_replace("." . $extension, "", $fileName)); // Filename without extension
//            $newFileName = "{$clearFilename}-copy." . $extension;
//            $fileName = $this->checkExistFileName($file, $newFileName, $filePath);
//        }
//        return $fileName;
//    }

////////////////////////////////////////////////////////////////////
// THE SCRIPT
////////////////////////////////////////////////////////////////////

//check if request is GET and the requested chunk exists or not. this makes testChunks work
    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     * @PublicPage
     *
     */
    public function upload(): FileReceive
    {
        $reciever = new FileReceive($this->request);
        if($reciever->isUploaded());
        var_dump($reciever->getUploadedFile());
        die();
        return $reciever;
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     * @PublicPage
     *
     * @param string $uuid
     */
    public function download(string $uuid): string
    {
        // Work your magic
        return $uuid;
    }
}
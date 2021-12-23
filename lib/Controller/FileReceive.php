<?php

namespace OCA\CrmConnector\Controller;

use OCA\CrmConnector\Db\CrmFile;
use OCA\CrmConnector\Traits\CrmFileTrait;
use OCP\Files\IRootFolder;
use OCP\Files\NotPermittedException;
use OCP\IConfig;
use OCP\IRequest;

class FileReceive
{
    use CrmFileTrait;

    /**
     * @var string
     */
    private $temp_dir;
    /**
     * @var string
     */
    private $chunk_file;
    /**
     * @var mixed
     */
    private $resumableIdentifier;
    /**
     * @var mixed
     */
    private $resumableChunkNumber;
    /**
     * @var null
     */
    private $uploadedFile;

    /**
     * @var mixed
     */
    private $uploadsDir;


    /**
     * @var string
     */
    private string $resumableFilename;

    private $resumableChunkSize;

    private $resumableTotalSize;

    private $resumableTotalChunks;

    private $userFolder;
    /**
     * @var mixed
     */
    private $projectFolder;
    /**
     * @var mixed
     */
    private $foldersThree;
    /**
     * @var IRootFolder
     */
    private $storage;
    /**
     * @var mixed
     */
    private $uuid;

    private $file;

    private $request;

    public function __construct(IRequest    $request,
                                IConfig     $config,
                                IRootFolder $storage)
    {
        try {

            $this->uploadsDir = $config->getSystemValue('datadirectory', \OC::$SERVERROOT . '/data');

            $this->storage = $storage;
            $this->userFolder = $this->storage->getUserFolder(CrmFile::CRM_USER);
            $this->request = $request;

            $this->uuid = $this->request->getParam('uuid');
            $this->projectFolder = $this->request->getParam('project_name');
            $this->foldersThree = json_decode($this->request->getParam('folders_tree'), true);

            $this->resumableIdentifier = '';
            $this->resumableFilename = '';
            $this->resumableChunkNumber = '';
            $this->resumableChunkSize = '';
            $this->resumableTotalSize = '';
            $this->resumableTotalChunks = '';

            if ($request->getMethod() === 'GET') {

                $this->temp_dir = $this->uploadsDir . '/temp/' . $this->resumableIdentifier;
                $this->chunk_file = $this->temp_dir . '/' . $this->resumableFilename . '.part' . $this->resumableChunkNumber;

                if (file_exists($this->chunk_file)) {
                    header("HTTP/1.0 200 Ok");
                } else {
                    header("HTTP/1.0 404 Not Found");
                }
            }
        } catch (\Throwable $exception) {
            throw new \Exception($exception->getMessage());
        }
    }


    public function isUploaded()
    {
        if ($this->request->getMethod() === 'POST') {
            $this->resumableChunkSize = $this->request->getParam('resumableChunkSize');
            $this->resumableTotalSize = $this->request->getParam('resumableTotalSize');
            $this->resumableTotalChunks = $this->request->getParam('resumableTotalChunks');

            $this->resumableIdentifier = $this->request->getParam('resumableIdentifier');
            $this->resumableFilename = trim($this->request->getParam('resumableFilename'));
            $this->resumableChunkNumber = $this->request->getParam('resumableChunkNumber');
        }
// loop through files and move the chunks to a temporarily created directory
        if (!empty($_FILES)) foreach ($_FILES as $file) {
            // check the error status
            if ($file['error'] != 0) {
                continue;
            }
            // init the destination file (format <filename.ext>.part<#chunk>
            // the file is stored in a temporary directory

            if ($this->resumableIdentifier && trim($this->resumableIdentifier) != '') {
                $this->temp_dir = $this->uploadsDir . '/temp/' . $this->resumableIdentifier;
            }
            $dest_file = $this->temp_dir . '/' . $this->resumableFilename . '.part' . $this->resumableChunkNumber;

            // create the temporary directory
            if (!is_dir($this->temp_dir)) {
                mkdir($this->temp_dir, 0777, true);
            }
            // move the temporary file
            if (!move_uploaded_file($file['tmp_name'], $dest_file)) {
                throw new \Exception('Error saving (move_uploaded_file) chunk ' . $this->resumableChunkNumber . ' for file ' . $this->resumableFilename);
            } else {
                return [
                    'temp_dir' => $this->temp_dir,
                    'resumableFilename' => $this->resumableFilename,
                    'resumableChunkSize' => $this->resumableChunkSize,
                    'resumableTotalSize' => $this->resumableTotalSize,
                    'resumableTotalChunks' => $this->resumableTotalChunks,
                    'resumableChunkNumber' => $this->resumableChunkNumber
                ];
            }
        }
    }

    /**
     * @return array $file moved data
     * @throws \Exception
     */
    public function uploadedFileMove(): array
    {
        try {
            $uploadedFilePath = "{$this->projectFolder}/{$this->foldersThreeString($this->foldersThree)}";
            $activeFolder = $this->checkFoldersThree($uploadedFilePath);
            if ($activeFolder) {
                return $this->moveFile($activeFolder, $uploadedFilePath);
            } else {
                throw new \Exception('File in uploadedFileMove not upload');
            }

        } catch (\Throwable $exception) {
            throw new \Exception($exception->getMessage());
        }
    }

    /**
     * @param string $uploadedFilePath
     * @return $activeFolder - OC\\Files\\Node\\Folder
     * @throws \Exception
     */
    public function checkFoldersThree($uploadedFilePath)
    {
        $sourceDir = "{$this->uploadsDir}{$this->userFolder->getPath()}/" . CrmFile::CRM_STORAGE . "/{$uploadedFilePath}/";
        if (!is_dir($sourceDir)) {
            $activeFolder = $this->foldersCreate($uploadedFilePath);
        } else {
            $activeFolder = $this->getActiveFolder($this->userFolder, $uploadedFilePath);
        }
        return $activeFolder;
    }

    /**
     * Check and create projetcs folder and start recursive new folders three
     * @param string $uploadedFilePath
     * @return $folder - OC\\Files\\Node\\Folder
     * @throws NotPermittedException
     */

    public function foldersCreate(string $uploadedFilePath)
    {
        $projects = null;
        try {
            $projects = $this->userFolder->get(CrmFile::CRM_STORAGE);
        } catch (\Throwable $exception) {
            $projects = $this->userFolder->newFolder(CrmFile::CRM_STORAGE);//create folder
        } catch (NotPermittedException $e) {
            return false;
        }
        $foldersArr = explode('/', $uploadedFilePath);
        return $this->folderCreateRecursive($projects, $foldersArr);

    }

    /**
     * @param $parentFolder - is IRootFolder after get()
     * @param array $foldersArr
     * @return mixed $folder - IRootFolder
     */

    public function folderCreateRecursive($parentFolder, array $foldersArr)
    {
        $newFolder = array_shift($foldersArr);
        try {
            $folder = $parentFolder->get($newFolder);
        } catch (\Throwable $exception) {
            $folder = $parentFolder->newFolder($newFolder);
        }

        if (count($foldersArr) > 0) {
            $folder = $this->folderCreateRecursive($folder, $foldersArr);
        }

        return $folder;
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

    /**
     *
     * Delete a directory RECURSIVELY
     * @param string $dir - directory path
     * @link http://php.net/manual/en/function.rmdir.php
     */
    function rrmdir($dir)
    {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (filetype($dir . "/" . $object) == "dir") {
                        $this->rrmdir($dir . "/" . $object);
                    } else {
                        unlink($dir . "/" . $object);
                    }
                }
            }
            reset($objects);
            rmdir($dir);
        }
    }

    /**
     *
     * Check if all the parts exist, and
     * gather all the parts of the file together
     * @param array $fileChunksData
     * @return bool|void
     * @throws \Exception
     */
    function createFileFromChunks(array $fileChunksData)
    {
        try {
            $temp_dir = $fileChunksData['temp_dir'];
            $fileName = $fileChunksData['resumableFilename'];
            $chunkSize = $fileChunksData['resumableChunkSize'];
            $totalSize = $fileChunksData['resumableTotalSize'];
            $total_files = $fileChunksData['resumableTotalChunks'];

            // count all the parts of this file
            $total_files_on_server_size = 0;
            $temp_total = 0;
            foreach (scandir($temp_dir) as $file) {
                $temp_total = $total_files_on_server_size;
                $tempfilesize = filesize($temp_dir . '/' . $file);
                $total_files_on_server_size = $temp_total + $tempfilesize;
            }
            // check that all the parts are present
            // If the Size of all the chunks on the server is equal to the size of the file uploaded.
            if ($total_files_on_server_size >= $totalSize) {
                // create the final destination file
                if (($fp = fopen($temp_dir . '/' . $fileName, 'w')) !== false) {
                    for ($i = 1; $i <= $total_files; $i++) {
                        fwrite($fp, file_get_contents($temp_dir . '/' . $fileName . '.part' . $i));
                    }
                    fclose($fp);
                    return true;
                } else {
                    throw new \Exception('cannot create the destination file');
                }
            }
        } catch (\Throwable $exception) {
            throw new \Exception($exception->getMessage());
        }
    }


    /**
     * Move uploaded file to projects dir and remove $temp_dir
     * @param $activeFolder
     * @param $uploadedFilePath
     * @return array $file $uploadedFilePath
     * @throws \Exception
     */
    public function moveFile($activeFolder, $uploadedFilePath): array
    {
        try {
            $sourceDir = "{$this->uploadsDir}{$this->userFolder->getPath()}/" . CrmFile::CRM_STORAGE . "/{$uploadedFilePath}/";
            $fileName = $this->createFilename($this->resumableFilename, $sourceDir);
            if (!copy("$this->temp_dir/$this->resumableFilename", "{$sourceDir}/$fileName")) {
                throw new \Exception('Uploaded file not move');
            }
            $this->resumableFilename = $fileName;
            // rename the temporary directory (to avoid access from other
            // concurrent chunks uploads) and that delete it
            if (rename($this->temp_dir, $this->temp_dir . '_UNUSED')) {
                $this->rrmdir($this->temp_dir . '_UNUSED');
            } else {
                $this->rrmdir($this->temp_dir);
            }

            $activeFolder->get($fileName);
            $file = [];
            $file['uuid'] = $this->uuid;
            $file['publication'] = true;
            $file['file_type'] = (new \OCA\CrmConnector\Db\CrmFile)->getType($this->resumableFilename);
            $file['file_share'] = 0;
            $file['extension'] = (new \OCA\CrmConnector\Db\CrmFile)->getExt($this->resumableFilename);
            $file['file_source'] = "{$uploadedFilePath}/{$this->resumableFilename}";
            $file['created_at'] = date('Y-m-d H:i:s', time());
            $file['updated_at'] = date('Y-m-d H:i:s', time());
            $file['file_original_name'] = $this->resumableFilename;
            return $file;
        } catch (\Throwable $exception) {
            throw new \Exception($exception->getMessage());
        }
    }

    /**
     * Create unique filename for uploaded file
     * @param $file
     * @param $filePath
     * @return string
     */
    protected function createFilename($fileName, $filePath): string
    {
        return $this->checkExistFileName($fileName, $filePath);
    }

    public function checkExistFileName($fileName, $sourceDir)
    {
        $extension = (new \OCA\CrmConnector\Db\CrmFile)->getExt($fileName);
        if (is_file("{$sourceDir}/$fileName")) {
            $clearFilename = trim(str_replace("." . $extension, "", $fileName)); // Filename without extension
            $newFileName = "{$clearFilename}-copy." . $extension;
            $fileName = $this->checkExistFileName($newFileName, $sourceDir);
        }
        return $fileName;
    }
}
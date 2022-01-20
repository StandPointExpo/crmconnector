<?php

namespace OCA\CrmConnector\Controller;

use OC\Files\Node\Folder;
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
    private $projectName;
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
            $this->projectName = $this->request->getParam('project_name');
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
            $uploadedFilePath = "{$this->projectName}/{$this->foldersThreeString($this->foldersThree)}";
            $activeFolder = $this->checkOrCreateFoldersThree($uploadedFilePath);
            return $this->moveFile($activeFolder, $uploadedFilePath);

        } catch (\Throwable $exception) {
            throw new \Exception($exception->getMessage());
        }
    }

    /**
     * @param string $uploadedFilePath
     * @return bool|null $activeFolder - OC\\Files\\Node\\Folder
     * @throws NotPermittedException
     * @throws \OCP\Files\NotFoundException
     */
    public function checkOrCreateFoldersThree(string $uploadedFilePath)
    {
        $sourceDir = $this->sourceFilePath($uploadedFilePath);
        if (!file_exists($sourceDir)) {
            $this->foldersTreeCreate($uploadedFilePath);
        }
        var_dump($sourceDir);
        die();
        return $this->getActiveFolder($this->userFolder, $uploadedFilePath);
    }

    /**
     * Check and create projetcs folder and start recursive new folders three
     * @param string $uploadedFilePath
     * @return mixed $folder - OC\\Files\\Node\\Folder
     * @throws NotPermittedException
     * @throws \Exception
     */

    public function foldersTreeCreate(string $uploadedFilePath)
    {
        $projectsPath = $this->checkOrCreateProjectsStorage();
        if (!$projectsPath) {
            throw new \Exception('User project folder not created');
        }

        $foldersArr = explode('/', $uploadedFilePath);
        $parentFolder = $this->userFolder->get(CrmFile::CRM_STORAGE);
        var_dump($parentFolder);
        die();
        return $this->folderCheckOrCreateRecursive($parentFolder, $foldersArr);

    }

    /**
     * @throws NotPermittedException
     */
    public function checkOrCreateProjectsStorage(): bool
    {
        if (!$this->userFolder->nodeExists(CrmFile::CRM_STORAGE)) {
            $this->userFolder->newFolder(CrmFile::CRM_STORAGE);
        }
        return $this->userFolder->nodeExists(CrmFile::CRM_STORAGE);
    }

    /**
     * @param string $parentFolder
     * @param array $foldersArr
     * @return mixed $folder - IRootFolder
     * @throws NotPermittedException
     */

    public function folderCheckOrCreateRecursive($parentFolder, array $foldersArr)
    {
        $newFolder = array_shift($foldersArr);

        var_dump($parentFolder->isSubNode($newFolder));
        die();
        $folder = ($parentFolder->isSubNode($newFolder))
            ? $this->userFolder->getFullPath($parentFolder . '/' . $newFolder)
            : $this->createNewFolder($parentFolder . '/' . $newFolder);

        if (count($foldersArr) > 0) {
            $folder = $this->folderCheckOrCreateRecursive($folder, $foldersArr);
        }

        return $folder;
    }

    /**
     * @throws NotPermittedException
     */
    public function createNewFolder($path): string
    {
        $this->userFolder->newFolder($path);
        return $this->userFolder->getFullPath($path);
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
            $sourceDir = $this->sourceFilePath($uploadedFilePath);
            $fileName = $this->setValidConv($this->createFilename($this->resumableFilename, $sourceDir));
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

    /**
     * Converting a string to the valid encoding
     * @param $string
     * @return string
     */
    public function setValidConv($string): string
    {
        return $string;
    }

    /**
     * Converting a string to the valid encoding
     * @param $uploadedFilePath
     * @return string
     */
    public function sourceFilePath($uploadedFilePath): string
    {
        return "{$this->uploadsDir}{$this->userFolder->getPath()}/" . CrmFile::CRM_STORAGE . "/{$uploadedFilePath}/";
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
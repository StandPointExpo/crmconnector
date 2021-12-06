<?php

namespace OCA\CrmConnector\Controller;

use OCA\CrmConnector\Db\CrmFile;
use OCA\CrmConnector\Mapper\CrmFileMapper;
use OCP\Files\IRootFolder;
use OCP\IConfig;
use OCP\IRequest;
use function Amp\Iterator\discard;

class FileReceive
{

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

    private string $projectsDir;

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

    private $resumableType;
    private $file;

    public function __construct(IRequest    $request,
                                IConfig     $config,
                                IRootFolder $storage)
    {
        $this->uploadedFile = null;
        $this->projectsDir = 'projects';
        $this->uploadsDir = $config->getSystemValue('datadirectory', \OC::$SERVERROOT . '/data');

        $this->storage = $storage;
        $this->userFolder = $this->storage->getUserFolder(CrmFile::USERNAME_STORAGE);

        $this->resumableFilename = trim($request->getParam('resumableFilename'));
        $this->resumableIdentifier = $request->getParam('resumableIdentifier');
        $this->resumableChunkSize = $request->getParam('resumableChunkSize');
        $this->resumableTotalSize = $request->getParam('resumableTotalSize');
        $this->resumableTotalChunks = $request->getParam('resumableTotalChunks');
        $this->resumableChunkNumber = $request->getParam('resumableChunkNumber');
        $this->resumableType = $request->getParam('resumableType');
        $this->uuid = $request->getParam('uuid');
        $this->file = $request->getUploadedFile('file');
        $this->projectFolder = $request->getParam('project_name');
        $this->foldersThree = json_decode($request->getParam('folders_tree'), true);

        $this->temp_dir = 'temp/' . $request->getParam('resumableIdentifier');

        $this->chunk_file = $this->temp_dir . '/' . $this->resumableFilename . '.part' . $this->resumableChunkNumber;
        if ($request->getMethod() === 'GET') {
            if (file_exists($this->chunk_file)) {
                header("HTTP/1.0 200 Ok");
            } else {
                header("HTTP/1.0 404 Not Found");
            }
        }

    }


    public function isUploaded()
    {

// loop through files and move the chunks to a temporarily created directory
        if (!empty($_FILES)) foreach ($_FILES as $file) {

            // check the error status
            if ($file['error'] != 0) {
                $this->_log('error ' . $file['error'] . ' in file ' . $this->resumableFilename);
                continue;
            }
            // init the destination file (format <filename.ext>.part<#chunk>
            // the file is stored in a temporary directory
            if (isset($this->resumableIdentifier) && trim($this->resumableIdentifier) != '') {
                $this->temp_dir = 'temp/' . $this->resumableIdentifier;
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
                // check if all the parts present, and create the final destination file
                return $this->createFileFromChunks(
                    $this->resumableFilename,
                    $this->resumableChunkSize,
                    $this->resumableTotalSize,
                    $this->resumableTotalChunks);
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
            $file = [];
            $uploadedFilePath = "{$this->projectFolder}/{$this->foldersThreeString($this->foldersThree)}";
            $sourceDir = "{$this->uploadsDir}/{$this->userFolder->getPath()}/projects/{$uploadedFilePath}/";
            $this->foldersThreeCreate($sourceDir);
            if ($this->moveFile($sourceDir)) {
                $file['uuid'] = $this->uuid;
                $file['publication'] = true;
                $file['file_type'] = (new \OCA\CrmConnector\Db\CrmFile)->getType($this->resumableFilename);
                $file['file_share'] = 0;
                $file['extension'] = (new \OCA\CrmConnector\Db\CrmFile)->getExt($this->resumableFilename);
                $file['file_source'] = "{$uploadedFilePath}/{$this->resumableFilename}";
                $file['created_at'] = date('Y-m-d H:i:s', time());
                $file['updated_at'] = date('Y-m-d H:i:s', time());
                $file['file_original_name'] = $this->resumableFilename;
            }
            return $file;

        } catch (\Throwable $exception) {
            throw new \Exception($exception->getMessage());
        }
    }

    /**
     * @throws \Exception
     */
    public function foldersThreeCreate($sourceDir)
    {
        if (!is_dir($sourceDir)) {
            @mkdir($sourceDir, 0777, true);
        }
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
     * Logging operation - to a file (upload_log.txt) and to the stdout
     * @param string $str - the logging string
     */
    function _log($str)
    {

        // log to the output
        $log_str = date('d.m.Y') . ": {$str}\r\n";

        // log to file
        if (($fp = fopen('upload_log.txt', 'a+')) !== false) {
            fputs($fp, $log_str);
            fclose($fp);
        }
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
     * @param string $temp_dir - the temporary directory holding all the parts of the file
     * @param string $fileName - the original file name
     * @param string $chunkSize - each chunk size (in bytes)
     * @param string $totalSize - original file size (in bytes)
     * @param string $total_files - original file size (in bytes)
     */
    function createFileFromChunks(
        $fileName,
        $chunkSize,
        $totalSize,
        $total_files
    )
    {

        // count all the parts of this file
        $total_files_on_server_size = 0;
        $temp_total = 0;
        foreach (scandir($this->temp_dir) as $file) {
            $temp_total = $total_files_on_server_size;
            $tempfilesize = filesize($this->temp_dir . '/' . $file);
            $total_files_on_server_size = $temp_total + $tempfilesize;
        }
        // check that all the parts are present
        // If the Size of all the chunks on the server is equal to the size of the file uploaded.
        if ($total_files_on_server_size >= $totalSize) {
            // create the final destination file
            if (($fp = fopen($this->temp_dir . '/' . $fileName, 'w')) !== false) {
                for ($i = 1; $i <= $total_files; $i++) {
                    fwrite($fp, file_get_contents($this->temp_dir . '/' . $fileName . '.part' . $i));
                }
                fclose($fp);
                $this->uploadedFile = $fp;
                return true;
            } else {
                throw new \Exception('cannot create the destination file');
            }
        }
    }


    /**
     * Move uploaded file to projects dir and remove $temp_dir
     * @return string $uploadedFilePath
     * @throws \Exception
     */
    public function moveFile($sourceDir): string
    {
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
        return true;
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
        $extension = (new \OCA\CrmConnector\Db\CrmFile)->getExt($this->file['name']);
        if (is_file("{$sourceDir}/$fileName")) {
            $clearFilename = trim(str_replace("." . $extension, "", $fileName)); // Filename without extension
            $newFileName = "{$clearFilename}-copy." . $extension;
            $fileName = $this->checkExistFileName($newFileName, $sourceDir);
        }
        return $fileName;
    }
}
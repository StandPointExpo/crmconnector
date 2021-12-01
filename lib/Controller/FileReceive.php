<?php

namespace OCA\CrmConnector\Controller;

use OCP\Files\IRootFolder;
use OCP\IConfig;
use OCP\IRequest;

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
     * @var string
     */
    private $fileName;
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

    private IRootFolder $storage;
    /**
     * @var mixed
     */
    private $uploadsDir;

    private string $projectsDir;

    public function __construct(IRequest $request,
                                IConfig $config,
                                IRootFolder $storage)
    {
        $this->uploadedFile = null;
        $this->storage = $storage;
        $this->projectsDir = '';
        //        $userFolder = $this->storage->getUserFolder('myUser');
        $this->uploadsDir = $config->getSystemValue('datadirectory', \OC::$SERVERROOT . '/data');
        if ($request->getMethod() === 'GET') {

            $this->fileName = trim($request->getParam('resumableFilename'));
            $this->resumableIdentifier = $request->getParam('resumableIdentifier');
            $this->resumableChunkNumber = $request->getParam('resumableChunkNumber');
            $this->temp_dir = 'temp/' . $request->getParam('resumableIdentifier');

            $this->chunk_file = $this->temp_dir . '/' . $this->fileName . '.part' . $this->resumableChunkNumber;

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
                $this->_log('error ' . $file['error'] . ' in file ' . $_POST['resumableFilename']);
                continue;
            }

            // init the destination file (format <filename.ext>.part<#chunk>
            // the file is stored in a temporary directory
            if (isset($_POST['resumableIdentifier']) && trim($_POST['resumableIdentifier']) != '') {
                $this->temp_dir = 'temp/' . $_POST['resumableIdentifier'];
            }
            $dest_file = $this->temp_dir . '/' . $_POST['resumableFilename'] . '.part' . $_POST['resumableChunkNumber'];

            // create the temporary directory
            if (!is_dir($this->temp_dir)) {
                mkdir($this->temp_dir, 0777, true);
            }

            // move the temporary file
            if (!move_uploaded_file($file['tmp_name'], $dest_file)) {
                $this->_log('Error saving (move_uploaded_file) chunk ' . $_POST['resumableChunkNumber'] . ' for file ' . $_POST['resumableFilename']);
                return false;
            } else {
                // check if all the parts present, and create the final destination file
                return $this->createFileFromChunks(
                    $_POST['resumableFilename'],
                    $_POST['resumableChunkSize'],
                    $_POST['resumableTotalSize'],
                    $_POST['resumableTotalChunks']);
            }
        }
    }

    //TODO доробити перенесення файлу після скачування
    public function uploadedFileMove($sourceDir)
    {

    }

    ////////////////////////////////////////////////////////////////////
// THE FUNCTIONS
////////////////////////////////////////////////////////////////////

    /**
     *
     * Logging operation - to a file (upload_log.txt) and to the stdout
     * @param string $str - the logging string
     */
    function _log($str)
    {

        // log to the output
        $log_str = date('d.m.Y') . ": {$str}\r\n";
        echo $log_str;

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
                    $this->_log('writing chunk ' . $i);
                }
                $this->_log('upload done');
                fclose($fp);
                $this->uploadedFile = $fp;
                return true;
            } else {
                $this->_log('cannot create the destination file');
                return false;
            }
        }
    }


    /**
     * Move uploaded file to projects dir and remove $temp_dir
     * @param string $temp_dir
     * @param string $fileName
     * @param $projectName
     * @param $foldersTree
     * @return string $uploadedFilePath
     */
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
}
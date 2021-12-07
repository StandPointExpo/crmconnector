<?php

namespace OCA\CrmConnector\Traits;

use OCA\CrmConnector\Db\CrmFile;

trait CrmFileTrait
{
    /**
     * Check and create projetcs folder and start recursive new folders three
     * @param string $uploadedFilePath
     * @return bool $folder - IRootFolder
     * @throws \OCP\Files\NotFoundException
     */

    public function getActiveFolder($userFolder, string $uploadedFilePath)
    {

        $projects = $userFolder->get(CrmFile::CRM_STORAGE);
        $foldersArr = explode('/', $uploadedFilePath);
        return $this->folderGetRecursive($projects, $foldersArr);

    }

    /**
     * @param $parentFolder - is IRootFolder after get()
     * @param array $foldersArr
     * @return mixed $folder - IRootFolder
     */

    public function folderGetRecursive($parentFolder, array $foldersArr)
    {
        $newFolder = array_shift($foldersArr);
        $folder = $parentFolder->get($newFolder);

        if (count($foldersArr) > 0) {
            $folder = $this->folderGetRecursive($folder, $foldersArr);
        }

        return $folder;
    }
}
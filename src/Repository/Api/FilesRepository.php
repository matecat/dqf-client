<?php

namespace Matecat\Dqf\Repository\Api;

use Matecat\Dqf\Model\Entity\File;
use Matecat\Dqf\Model\Repository\FilesRepositoryInterface;

class FilesRepository extends AbstractApiRepository implements FilesRepositoryInterface
{
    /**
     * @param int    $childProjectId
     * @param string $childProjectUuid
     *
     * @return File[]
     */
    public function getByChildProject($childProjectId, $childProjectUuid)
    {
        return $this->getFilesArray(
            $this->client->getChildProjectFiles([
                'sessionId'  => $this->sessionId,
                'projectKey' => $childProjectUuid,
                'projectId' => $childProjectId,
            ])
        );
    }

    /**
     * @param int    $masterProjectId
     * @param string $masterProjectUuid
     *
     * @return File[]
     */
    public function getByMasterProject($masterProjectId, $masterProjectUuid)
    {
        return $this->getFilesArray(
            $this->client->getMasterProjectFiles([
                'sessionId'  => $this->sessionId,
                'projectKey' => $masterProjectUuid,
                'projectId' => $masterProjectId,
            ])
        );
    }

    /**
     * @param \stdClass $response
     *
     * @return File[]
     */
    private function getFilesArray($response)
    {
        $files = [];

        foreach ($response->modelList as $file) {
            $newFile = new File($file->name, $file->segmentSize);
            $newFile->setDqfId($file->id);
            $newFile->setTmsFileId($file->tmsFile);
            $newFile->setClientId($file->integratorFileMap->clientValue);

            $files[] = $newFile;
        }

        return $files;
    }

    /**
     * @param int    $childProjectId
     * @param string $childProjectUuid
     * @param int    $fileId
     *
     * @return File
     */
    public function getByIdAndChildProject($childProjectId, $childProjectUuid, $fileId)
    {
        return $this->getFile(
            $this->client->getChildProjectFile([
                'sessionId'  => $this->sessionId,
                'projectKey' => $childProjectUuid,
                'projectId' => $childProjectId,
                'fileId' => $fileId,
            ])
        );
    }

    /**
     * @param int    $masterProjectId
     * @param string $masterProjectUuid
     * @param int    $fileId
     *
     * @return File
     */
    public function getByIdAndMasterProject($masterProjectId, $masterProjectUuid, $fileId)
    {
        return $this->getFile(
            $this->client->getMasterProjectFile([
                'sessionId'  => $this->sessionId,
                'projectKey' => $masterProjectUuid,
                'projectId' => $masterProjectId,
                'fileId' => $fileId,
            ])
        );
    }

    /**
     * @param \stdClass $response
     *
     * @return File
     */
    private function getFile($response)
    {
        $model = $response->model;

        $newFile = new File($model->name, $model->segmentSize);
        $newFile->setDqfId($model->id);
        $newFile->setTmsFileId($model->tmsFile);
        $newFile->setClientId($model->integratorFileMap->clientValue);

        return $newFile;
    }
}

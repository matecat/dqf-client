<?php

namespace Matecat\Dqf\Model\Repository;

use Matecat\Dqf\Model\Entity\File;

interface FilesRepositoryInterface
{
    /**
     * @param int $childProjectId
     * @param string $childProjectUuid
     *
     * @return File[]
     */
    public function getByChildProject($childProjectId, $childProjectUuid);

    /**
     * @param int $masterProjectId
     * @param string $masterProjectUuid
     *
     * @return File[]
     */
    public function getByMasterProject($masterProjectId, $masterProjectUuid);

    /**
     * @param int $childProjectId
     * @param string $childProjectUuid
     * @param int $fileId
     *
     * @return File
     */
    public function getByIdAndChildProject($childProjectId, $childProjectUuid, $fileId);

    /**
     * @param int $masterProjectId
     * @param string $masterProjectUuid
     * @param int $fileId
     *
     * @return File
     */
    public function getByIdAndMasterProject($masterProjectId, $masterProjectUuid, $fileId);
}

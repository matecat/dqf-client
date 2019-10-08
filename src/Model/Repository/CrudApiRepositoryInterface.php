<?php

namespace Matecat\Dqf\Model\Repository;

use Matecat\Dqf\Model\Entity\BaseApiEntity;

interface CrudApiRepositoryInterface
{
    /**
     * Delete a record
     *
     * @param int $dqfId
     * @param null $dqfUuid
     *
     * @return int
     */
    public function delete($dqfId, $dqfUuid = null);

    /**
     * Retrieve a record
     *
     * @param int $dqfId
     * @param null $dqfUuid
     *
     * @return mixed
     */
    public function get($dqfId, $dqfUuid = null);

    /**
     * Save a record
     *
     * @param BaseApiEntity $baseEntity
     *
     * @return BaseApiEntity
     */
    public function save(BaseApiEntity $baseEntity);

    /**
     * Update a record
     *
     * @param BaseApiEntity $baseEntity
     *
     * @return mixed
     */
    public function update(BaseApiEntity $baseEntity);
}

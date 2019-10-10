<?php

namespace Matecat\Dqf\Repository\Api;

use Matecat\Dqf\Model\Entity\TranslatedSegment;
use Matecat\Dqf\Model\Repository\TranslationRepositoryInterface;
use Matecat\Dqf\Model\ValueObject\TranslationBatch;

class TranslationBatchRepository extends AbstractApiRepository implements TranslationRepositoryInterface {

//    /**
//     * Delete a record
//     *
//     * @param int  $dqfId
//     * @param null $dqfUuid
//     *
//     * @return int
//     */
//    public function delete($dqfId, $dqfUuid = null)
//    {
//
//    }
//
//    /**
//     * Retrieve a record
//     *
//     * @param int  $dqfId
//     * @param null $dqfUuid
//     *
//     * @return mixed
//     */
//    public function get($dqfId, $dqfUuid = null)
//    {
//
//    }
//
//    /**
//     * Save a record
//     *
//     * @param BaseApiEntity $baseEntity
//     *
//     * @return BaseApiEntity
//     */
//    public function save(BaseApiEntity $baseEntity)
//    {
//        /** @var $baseEntity TranslationBatch */
//        if (false === $baseEntity instanceof TranslationBatch) {
//            throw new InvalidTypeException('Entity provided is not an instance of TranslationBatch');
//        }
//
//        $segments = [];
//
//        foreach ( $baseEntity->getSegments() as $segment ) {
//            $segments[] =             [
//                    "sourceSegmentId"   => $segment->getSourceSegment()->getDqfId(),
//                    "clientId"          => $segment->getClientId(),
//                    "targetSegment"     => $segment->getTargetSegment(),
//                    "editedSegment"     => $segment->getEditedSegment(),
//                    "time"              => $segment->getTime(),
//                    "segmentOriginId"   => $segment->getSegmentOriginId(),
//                    "mtEngineId"        => $segment->getMtEngineId(),
//                    "mtEngineOtherName" => $segment->getMtEngineOtherName(),
//                    "matchRate"         => $segment->getMatchRate()
//            ];
//        }
//
//        var_dump($segments);
//
////        'segmentPairs' => [
////            [
////                    "sourceSegmentId"   => 1,
////                    "clientId"          => Uuid::uuid4()->toString(),
////                    "targetSegment"     => "",
////                    "editedSegment"     => "The frog in Spain",
////                    "time"              => 6582,
////                    "segmentOriginId"   => 1,
////                    "mtEngineId"        => 22,
////                    "mtEngineOtherName" => null,
////                    "matchRate"         => 0
////            ],
//    }
//
//    /**
//     * Update a record
//     *
//     * @param BaseApiEntity $baseEntity
//     *
//     * @return mixed
//     */
//    public function update(BaseApiEntity $baseEntity)
//    {
//
//    }
    /**
     * @param \Matecat\Dqf\Model\ValueObject\TranslationBatch $batch
     *
     * @return mixed
     */
    public function save( TranslationBatch $batch ) {
        // TODO: Implement save() method.
    }

    /**
     * @param TranslatedSegment $segment
     *
     * @return mixed
     */
    public function update( TranslatedSegment $segment ) {
        // TODO: Implement update() method.
    }
}

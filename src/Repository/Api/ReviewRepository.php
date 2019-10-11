<?php

namespace Matecat\Dqf\Repository\Api;

use Matecat\Dqf\Model\Entity\BaseApiEntity;
use Matecat\Dqf\Model\Entity\ChildProject;
use Matecat\Dqf\Model\Entity\File;
use Matecat\Dqf\Model\Entity\FileTargetLang;
use Matecat\Dqf\Model\Entity\AbstractProject;
use Matecat\Dqf\Model\Repository\ReviewRepositoryInterface;
use Matecat\Dqf\Model\ValueObject\ReviewBatch;
use Ramsey\Uuid\Uuid;

class ReviewRepository extends AbstractApiRepository implements ReviewRepositoryInterface
{
    /**
     * @param ReviewBatch $batch
     *
     * @return mixed|void
     * @throws \Exception
     */
    public function save(ReviewBatch $batch)
    {
        $reviewedSegments = $batch->getReviewedSegments();
        $corrections = [];
        $errors = [];

        if (false === empty($reviewedSegments)) {
            foreach ($reviewedSegments as $reviewedSegment) {
                // errors
                foreach ($reviewedSegment->getErrors() as $error) {
                    $errors[] = [
                            "errorCategoryId" => $error->getErrorCategoryId(),
                            "severityId"      => $error->getSeverityId(),
                            "charPosStart"    => $error->getCharPosStart(),
                            "charPosEnd"      => $error->getCharPosEnd(),
                            "isRepeated"      => $error->isRepeated()
                    ];
                }

                // detailList
                $detailList = [];
                foreach ($reviewedSegment->getCorrection()->getDetailList() as $correctionItem) {
                    $detailList[] = [
                            "subContent" => $correctionItem->getSubContent(),
                            "type"       => $correctionItem->getType()
                    ];
                }

                $corrections[] = [
                        "clientId" => (false === empty($reviewedSegment->getClientId())) ? $reviewedSegment->getClientId() : Uuid::uuid4()->toString(),
                        "comment"  => $reviewedSegment->getComment(),
                        "errors"   => $errors,
                        "correction" => [
                                "content"    => $reviewedSegment->getCorrection()->getContent(),
                                "time"       => $reviewedSegment->getCorrection()->getTime(),
                                "detailList" => $detailList
                        ]
                ];
            }
        }

        $updateReviewInBatch = $this->client->updateReviewInBatch([
                'sessionId'      => $this->sessionId,
                'projectKey'     => $batch->getChildProject()->getDqfUuid(),
                'projectId'      => $batch->getChildProject()->getDqfId(),
                'fileId'         => $batch->getFile()->getDqfId(),
                'targetLangCode' => $batch->getTargetLanguage()->getLocaleCode(),
                'translationId'  => $batch->getTranslation()->getDqfId(),
                'batchId'        => $batch->getBatchId(),
                'overwrite'      => $batch->isOverwrite(),
                'body'           => $corrections,
        ]);

        if (false === empty($reviewedSegments)) {
            foreach ($reviewedSegments as $key => $reviewedSegment) {
                $reviewedSegment->setClientId($updateReviewInBatch->createdReviewIds[$key]->clientId);
            }
        }

        return $batch;
    }
}

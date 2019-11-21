<?php

namespace Matecat\Dqf\Repository\Api;

use Matecat\Dqf\Model\Entity\ChildProject;
use Matecat\Dqf\Model\Entity\File;
use Matecat\Dqf\Model\Entity\Language;
use Matecat\Dqf\Model\Entity\SourceSegment;
use Matecat\Dqf\Model\Entity\TranslatedSegment;
use Matecat\Dqf\Model\Repository\TranslationRepositoryInterface;
use Matecat\Dqf\Model\ValueObject\TranslationBatch;

class TranslationRepository extends AbstractApiRepository implements TranslationRepositoryInterface
{
    /**
     * @param int     $childProjectId
     * @param string  $childProjectUuid
     * @param int     $fileId
     * @param string  $targetLanguage
     * @param int     $sourceSegmentDqfId
     * @param int     $segmentTranslationDqfId
     *
     * @return TranslatedSegment
     */
    public function getTranslatedSegment($childProjectId, $childProjectUuid, $fileId, $targetLanguage, $sourceSegmentDqfId, $segmentTranslationDqfId)
    {
        $translationForASegment = $this->client->getTranslationForASegment([
                'generic_email'       => $this->genericEmail,
                'sessionId'           => $this->sessionId,
                'projectKey'          => $childProjectUuid,
                'projectId'           => $childProjectId,
                'fileId'              => $fileId,
                'targetLangCode'      => $targetLanguage,
                'sourceSegmentId'     => $sourceSegmentDqfId,
                'translationId'       => $segmentTranslationDqfId,
        ]);

        if (false === isset($translationForASegment->model)) {
            return null;
        }

        $model = $translationForASegment->model;

        $file = new File($model->sourceSegment->file->name, $model->sourceSegment->file->segmentSize);
        $file->setDqfId($model->sourceSegment->file->id);
        $file->setTmsFileId($model->sourceSegment->file->tmsFile);
        $file->setClientId($model->sourceSegment->file->integratorFileMap->clientValue);

        $sourceSegment = new SourceSegment($file, $model->sourceSegment->indexNo, $model->sourceSegment->content);
        $sourceSegment->setDqfId($model->sourceSegment->id);
        $sourceSegment->setClientId($model->sourceSegment->integratorSegmentMap->clientValue);

        $translatedSegment = new TranslatedSegment(
            $model->targetSegment->mtEngine->id,
            $model->targetSegment->segmentOrigin->id,
            $targetLanguage,
            $sourceSegment->getDqfId(),
            $model->sourceSegment->content,
            $model->targetSegment->content,
            $model->sourceSegment->indexNo
        );
        $translatedSegment->setDqfId($model->id);
        $translatedSegment->setMtEngineOtherName($model->targetSegment->mtEngineOther);
        $translatedSegment->setMtEngineVersion($model->targetSegment->mtEngineVersion);
        $translatedSegment->setMatchRate($model->targetSegment->matchRate);

        if (isset($model->integratorTranslationMap->clientValue)) {
            $translatedSegment->setClientId($model->integratorTranslationMap->clientValue);
        }

        $translatedSegment->setTime($model->time);

        $language = new Language($targetLanguage);
        $this->hydrateLanguage($language);
        $translatedSegment->setTargetLanguage($language);

        return $translatedSegment;
    }

    /**
     * @param TranslationBatch $batch
     *
     * @return mixed|void
     * @throws \Exception
     */
    public function save(TranslationBatch $batch)
    {
        // get the source segments for the child project
        $sourceSegmentIds = $this->client->getSourceSegmentIdsForAFile([
            'generic_email'  => $this->genericEmail,
            'sessionId'      => $this->sessionId,
            'projectKey'     => $batch->getChildProject()->getDqfUuid(),
            'projectId'      => $batch->getChildProject()->getDqfId(),
            'fileId'         => $batch->getFile()->getDqfId(),
            'targetLangCode' => $batch->getChildProject()->getTargetLanguages()[ 0 ]->getLocaleCode(),
        ]);

        // build $segmentPairs
        $segmentPairs = [];

        if (false === empty($batch->getSegments())) {
            foreach ($batch->getSegments() as $segment) {
                foreach ($sourceSegmentIds->sourceSegmentList as $index => $item) {
                    if ($item->index === $segment->getIndexNo()) {
                        $segmentPairs[] = [
                            'sourceSegmentId'   => $item->dqfId,
                            'clientId'          => $segment->getClientId(),
                            'targetSegment'     => $segment->getTargetSegment(),
                            'editedSegment'     => $segment->getEditedSegment(),
                            'time'              => $segment->getTime(),
                            'segmentOriginId'   => $segment->getSegmentOriginId(),
                            'mtEngineId'        => $segment->getMtEngineId(),
                            'mtEngineOtherName' => $segment->getMtEngineOtherName(),
                            'matchRate'         => $segment->getMatchRate(),
                            'indexNo'           => $segment->getIndexNo()
                        ];
                    }
                }
            }
        }

        $translationsBatch = $this->client->addTranslationsForSourceSegmentsInBatch([
            'generic_email'  => $this->genericEmail,
            'sessionId'      => $this->sessionId,
            'projectKey'     => $batch->getChildProject()->getDqfUuid(),
            'projectId'      => $batch->getChildProject()->getDqfId(),
            'fileId'         => $batch->getFile()->getDqfId(),
            'targetLangCode' => $batch->getTargetLanguage()->getLocaleCode(),
            'body'           => $segmentPairs,
        ]);

        foreach ($translationsBatch->translations as $i => $translation) {
            $batch->getSegments()[$i]->setDqfId($translation->dqfId);
            $this->hydrateLanguage($batch->getSegments()[$i]->getTargetLanguage());
        }

        return $batch;
    }

    /**
     * @param ChildProject      $childProject
     * @param File              $file
     * @param TranslatedSegment $translatedSegment
     *
     * @return bool
     */
    public function update(ChildProject $childProject, File $file, TranslatedSegment $translatedSegment)
    {
        if ($this->exists($childProject, $file, $translatedSegment)) {
            $updateSingleSegmentTranslation = $this->client->updateTranslationForASegment([
                'generic_email'       => $this->genericEmail,
                'sessionId'           => $this->sessionId,
                'projectKey'          => $childProject->getDqfUuid(),
                'projectId'           => $childProject->getDqfId(),
                'fileId'              => $file->getDqfId(),
                'targetLangCode'      => $translatedSegment->getTargetLanguage()->getLocaleCode(),
                'sourceSegmentId'     => $translatedSegment->getSourceSegmentId(),
                'translationId'       => $translatedSegment->getDqfId(),
                'segmentOriginId'     => $translatedSegment->getSegmentOriginId(),
                'targetSegment'       => $translatedSegment->getTargetSegment(),
                'editedSegment'       => $translatedSegment->getEditedSegment(),
                'time'                => $translatedSegment->getTime(),
                'matchRate'           => $translatedSegment->getMatchRate(),
                'mtEngineId'          => $translatedSegment->getMtEngineId(),
                'mtEngineOtherName'   => $translatedSegment->getMtEngineOtherName(),
                'mtEngineVersion'     => $translatedSegment->getMtEngineVersion(),
                'segmentOriginDetail' => $translatedSegment->getSegmentOriginDetail(),
                'clientId'            => $translatedSegment->getClientId(),
            ]);

            return $updateSingleSegmentTranslation->status === 'OK';
        }
    }

    /**
     * @param ChildProject      $childProject
     * @param File              $file
     * @param TranslatedSegment $translatedSegment
     *
     * @return bool
     */
    private function exists(ChildProject $childProject, File $file, TranslatedSegment $translatedSegment)
    {
        $translationForASegment = $this->client->getTranslationForASegment([
            'generic_email'       => $this->genericEmail,
            'sessionId'           => $this->sessionId,
            'projectKey'          => $childProject->getDqfUuid(),
            'projectId'           => $childProject->getDqfId(),
            'fileId'              => $file->getDqfId(),
            'targetLangCode'      => $translatedSegment->getTargetLanguage()->getLocaleCode(),
            'sourceSegmentId'     => $translatedSegment->getSourceSegmentId(),
            'translationId'       => $translatedSegment->getDqfId(),
        ]);

        return false === empty($translationForASegment->model);
    }
}

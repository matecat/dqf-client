<?php

namespace Matecat\Dqf\Repository\Api;

use Matecat\Dqf\Constants;
use Matecat\Dqf\Model\Entity\AbstractProject;
use Matecat\Dqf\Model\Entity\BaseApiEntity;
use Matecat\Dqf\Model\Entity\ChildProject;
use Matecat\Dqf\Model\Entity\File;
use Matecat\Dqf\Model\Entity\FileTargetLang;
use Matecat\Dqf\Model\Entity\ReviewSettings;
use Matecat\Dqf\Model\Repository\CrudApiRepositoryInterface;
use Symfony\Component\Config\Definition\Exception\InvalidTypeException;

class ChildProjectRepository extends AbstractApiRepository implements CrudApiRepositoryInterface
{
    /**
     * Delete a record
     *
     * @param BaseApiEntity $baseEntity
     *
     * @return int
     */
    public function delete(BaseApiEntity $baseEntity)
    {
        /** @var $baseEntity AbstractProject */
        if (false === $baseEntity instanceof AbstractProject) {
            throw new InvalidTypeException('Entity provided is not an instance of MasterProject');
        }

        if (empty($baseEntity->getDqfId())) {
            throw new \DomainException('MasterProject have not a DQF id and cannot be deleted');
        }

        $childProject = $this->client->deleteChildProject([
                'generic_email' => $this->genericEmail,
                'sessionId'     => $this->sessionId,
                'projectKey'    => $baseEntity->getDqfUuid(),
                'projectId'     => $baseEntity->getDqfId(),
        ]);

        return ($childProject->status === 'OK') ? 1 : 0;
    }

    /**
     * Retrieve a record
     *
     * @param int  $dqfId
     * @param null $dqfUuid
     *
     * @return mixed
     */
    public function get($dqfId, $dqfUuid = null)
    {
        // get child project
        $childProject = $this->client->getChildProject([
                'generic_email' => $this->genericEmail,
                'sessionId'     => $this->sessionId,
                'projectKey'    => $dqfUuid,
                'projectId'     => $dqfId,
        ]);

        $model = $childProject->model;

        $childProject = new ChildProject($model->type);
        $childProject->setName($model->name);
        $childProject->setDqfId($dqfId);
        $childProject->setDqfUuid($dqfUuid);

        // file(s)
        if (false === empty($model->files)) {
            foreach ($model->files as $f) {
                $file = new File($f->name, $f->segmentSize);
                $file->setDqfId($f->id);
                $file->setTmsFileId($f->tmsFile);
                $childProject->addFile($file);
            }
        }

        // assoc targetLang to file(s)
        if (false === empty($model->fileProjectTargetLangs)) {
            foreach ($model->fileProjectTargetLangs as $assoc) {
                $childProject->assocTargetLanguageToFile($assoc->projectTargetLang->language->localeCode, $childProject->getFile($assoc->file->name), $assoc->id);
            }
        }

        // review settings
        $reviewSettings = $this->getReviewSettings($dqfId, $dqfUuid);
        $childProject->setReviewSettings($reviewSettings);
        $childProject->setReviewSettingsId($reviewSettings->getDqfId());

        return $childProject;
    }

    /**
     * @param $dqfId
     * @param $dqfUuid
     *
     * @return ReviewSettings|mixed
     */
    private function getReviewSettings($dqfId, $dqfUuid)
    {
        $reviewSettings = $this->client->getProjectReviewSettings([
                'generic_email' => $this->genericEmail,
                'sessionId'     => $this->sessionId,
                'projectKey'    => $dqfUuid,
                'projectId'     => $dqfId,
        ]);

        $model = $reviewSettings->model;

        $reviewSettings = new ReviewSettings($model->type);
        $reviewSettings->setDqfId($model->id);
        $reviewSettings->setPassFailThreshold($model->threshold);

        $errorSeveritySetting      = '[';
        $errorSeveritySettingArray = [];

        if (false === empty($model->errorSeveritySetting)) {
            foreach ($model->errorSeveritySetting as $setting) {
                $errorSeveritySettingArray[] = '{"severityId":' . $setting->value . ',"weight":' . $setting->errorSeverity->id . '}';
            }
        }

        $errorSeveritySetting .= implode(',', $errorSeveritySettingArray);
        $errorSeveritySetting .= ']';

        $reviewSettings->setSeverityWeights($errorSeveritySetting);
        if (false === empty($model->errorTypologySetting[ 0 ]->errorCategory->id)) {
            $reviewSettings->setErrorCategoryIds0($model->errorTypologySetting[ 0 ]->errorCategory->id);
        }

        if (false === empty($model->errorTypologySetting[ 1 ]->errorCategory->id)) {
            $reviewSettings->setErrorCategoryIds1($model->errorTypologySetting[ 1 ]->errorCategory->id);
        }

        if (false === empty($model->errorTypologySetting[ 2 ]->errorCategory->id)) {
            $reviewSettings->setErrorCategoryIds2($model->errorTypologySetting[ 2 ]->errorCategory->id);
        }

        return $reviewSettings;
    }

    /**
     * Save a record
     *
     * @param BaseApiEntity $baseEntity
     *
     * @return BaseApiEntity
     */
    public function save(BaseApiEntity $baseEntity)
    {
        /** @var $baseEntity ChildProject */
        if (false === $baseEntity instanceof ChildProject) {
            throw new \InvalidArgumentException('Entity provided is not an instance of ChildProject');
        }

        if (empty($baseEntity->getParentProject())) {
            throw new \DomainException('MasterProject MUST be set during creation of a ChildProject');
        }

        if ($baseEntity->getType() === Constants::PROJECT_TYPE_REVIEW and empty($baseEntity->getReviewSettings())) {
            throw new \DomainException('A \'review\' ChildProject MUST have set review settings');
        }

        // create child project
        $this->createProject($baseEntity);

        // assoc targetLang to file(s)
        $this->saveTargetLanguageAssoc($baseEntity);

        // review settings
        $this->saveReviewSettings($baseEntity);

        return $baseEntity;
    }

    /**
     * @param ChildProject $baseEntity
     */
    private function createProject(ChildProject $baseEntity)
    {
        $childProject = $this->client->createChildProject([
                'generic_email'   => $this->genericEmail,
                'sessionId'       => $this->sessionId,
                'parentKey'       => $baseEntity->getParentProject()->getDqfUuid(),
                'type'            => $baseEntity->getType(),
                'name'            => $baseEntity->getName(),
                'clientId'        => $baseEntity->getClientId(),
                'assignee'        => $baseEntity->getAssignee(),
                'assigner'        => $baseEntity->getAssigner(),
                'reviewSettingId' => $baseEntity->getReviewSettingsId(),
                'isDummy'         => $baseEntity->isDummy(),
        ]);

        $baseEntity->setDqfId($childProject->dqfId);
        $baseEntity->setDqfUuid($childProject->dqfUUID);
    }

    /**
     * @param ChildProject $baseEntity
     */
    private function saveTargetLanguageAssoc(ChildProject $baseEntity)
    {
        if (false === empty($baseEntity->getTargetLanguageAssoc())) {
            foreach ($baseEntity->getTargetLanguageAssoc() as $targetLanguageCode => $fileTargetLangs) {

                /** @var FileTargetLang $fileTargetLang */
                foreach ($fileTargetLangs as $fileTargetLang) {
                    if (false === empty($fileTargetLang->getFile()->getDqfId())) {
                        $projectTargetLanguage = $this->client->addChildProjectTargetLanguage([
                                'generic_email'      => $this->genericEmail,
                                'sessionId'          => $this->sessionId,
                                'projectKey'         => $baseEntity->getDqfUuid(),
                                'projectId'          => $baseEntity->getDqfId(),
                                'fileId'             => $fileTargetLang->getFile()->getDqfId(),
                                'targetLanguageCode' => $targetLanguageCode,
                        ]);

                        $fileTargetLang->setDqfId($projectTargetLanguage->dqfId);
                    }
                }
            }
        }
    }

    /**
     * @param AbstractProject $baseEntity
     */
    private function saveReviewSettings(AbstractProject $baseEntity)
    {
        if (false === empty($baseEntity->getReviewSettings())) {
            $projectReviewSettings = $this->client->addProjectReviewSettings([
                    'generic_email'       => $this->genericEmail,
                    'sessionId'           => $this->sessionId,
                    'projectKey'          => $baseEntity->getDqfUuid(),
                    'projectId'           => $baseEntity->getDqfId(),
                    'reviewType'          => $baseEntity->getReviewSettings()->getReviewType(),
                    'severityWeights'     => $baseEntity->getReviewSettings()->getSeverityWeights(),
                    'errorCategoryIds[0]' => $baseEntity->getReviewSettings()->getErrorCategoryIds0(),
                    'errorCategoryIds[1]' => $baseEntity->getReviewSettings()->getErrorCategoryIds1(),
                    'errorCategoryIds[2]' => $baseEntity->getReviewSettings()->getErrorCategoryIds2(),
                    'passFailThreshold'   => $baseEntity->getReviewSettings()->getPassFailThreshold(),
            ]);

            $baseEntity->getReviewSettings()->setDqfId($projectReviewSettings->dqfId);
        }
    }

    /**
     * Update a record
     *
     * @param BaseApiEntity $baseEntity
     *
     * @return mixed
     */
    public function update(BaseApiEntity $baseEntity)
    {
        // create child project
        $this->updateProject($baseEntity);

        // assoc targetLang to file(s)
        $this->updateTargetLanguageAssoc($baseEntity);

        // review settings
        $this->updateReviewSettings($baseEntity);
    }

    /**
     * @param BaseApiEntity $baseEntity
     *
     * @return mixed
     */
    private function updateProject(BaseApiEntity $baseEntity)
    {
        if (empty($baseEntity->getParentProject())) {
            throw new \DomainException('MasterProject MUST be set during the update of a ChildProject');
        }

        return $this->client->updateChildProject([
                'generic_email'   => $this->genericEmail,
                'sessionId'       => $this->sessionId,
                'projectKey'      => $baseEntity->getDqfUuid(),
                'projectId'       => $baseEntity->getDqfId(),
                'parentKey'       => $baseEntity->getParentProject()->getDqfUuid(),
                'type'            => $baseEntity->getType(),
                'name'            => $baseEntity->getName(),
                'clientId'        => $baseEntity->getClientId(),
                'assignee'        => $baseEntity->getAssignee(),
                'assigner'        => $baseEntity->getAssigner(),
                'reviewSettingId' => $baseEntity->getReviewSettingsId(),
                'isDummy'         => $baseEntity->isDummy(),
        ]);
    }

    private function updateTargetLanguageAssoc(BaseApiEntity $baseEntity)
    {
        if (false === empty($baseEntity->getTargetLanguageAssoc())) {

            // delete ALL target lang assoc
            foreach ($baseEntity->getFiles() as $file) {
                $childProjectTargetLanguages = $this->client->getChildProjectTargetLanguages([
                        'generic_email' => $this->genericEmail,
                        'sessionId'     => $this->sessionId,
                        'projectKey'    => $baseEntity->getDqfUuid(),
                        'projectId'     => $baseEntity->getDqfId(),
                        'fileId'        => $file->getDqfId(),
                ]);

                foreach ($childProjectTargetLanguages->modelList as $childProjectTargetLanguage) {
                    $this->client->deleteChildProjectTargetLanguage([
                            'generic_email'  => $this->genericEmail,
                            'sessionId'      => $this->sessionId,
                            'projectKey'     => $baseEntity->getDqfUuid(),
                            'projectId'      => $baseEntity->getDqfId(),
                            'fileId'         => $file->getDqfId(),
                            'targetLangCode' => $childProjectTargetLanguage->localeCode,
                    ]);
                }
            }

            // And then reset values
            foreach ($baseEntity->getTargetLanguageAssoc() as $targetLanguageCode => $fileTargetLangs) {
                /** @var FileTargetLang $fileTargetLang */
                foreach ($fileTargetLangs as $fileTargetLang) {
                    $projectTargetLanguage = $this->client->addChildProjectTargetLanguage([
                            'generic_email'      => $this->genericEmail,
                            'sessionId'          => $this->sessionId,
                            'projectKey'         => $baseEntity->getDqfUuid(),
                            'projectId'          => $baseEntity->getDqfId(),
                            'fileId'             => $fileTargetLang->getFile()->getDqfId(),
                            'targetLanguageCode' => $targetLanguageCode,
                    ]);

                    $fileTargetLang->setDqfId($projectTargetLanguage->dqfId);
                }
            }
        }
    }

    /**
     * @param AbstractProject $baseEntity
     */
    private function updateReviewSettings(BaseApiEntity $baseEntity)
    {
        if (false === empty($baseEntity->getReviewSettings())) {
            if (false === empty($baseEntity->getReviewSettings()->getDqfId())) {
                $this->client->updateProjectReviewSettings([
                        'generic_email'       => $this->genericEmail,
                        'sessionId'           => $this->sessionId,
                        'projectKey'          => $baseEntity->getDqfUuid(),
                        'projectId'           => $baseEntity->getDqfId(),
                        'reviewType'          => $baseEntity->getReviewSettings()->getReviewType(),
                        'severityWeights'     => $baseEntity->getReviewSettings()->getSeverityWeights(),
                        'errorCategoryIds[0]' => $baseEntity->getReviewSettings()->getErrorCategoryIds0(),
                        'errorCategoryIds[1]' => $baseEntity->getReviewSettings()->getErrorCategoryIds1(),
                        'errorCategoryIds[2]' => $baseEntity->getReviewSettings()->getErrorCategoryIds2(),
                        'passFailThreshold'   => $baseEntity->getReviewSettings()->getPassFailThreshold(),
                ]);
            } else {
                $projectReviewSettings = $this->client->addProjectReviewSettings([
                        'generic_email'       => $this->genericEmail,
                        'sessionId'           => $this->sessionId,
                        'projectKey'          => $baseEntity->getDqfUuid(),
                        'projectId'           => $baseEntity->getDqfId(),
                        'reviewType'          => $baseEntity->getReviewSettings()->getReviewType(),
                        'severityWeights'     => $baseEntity->getReviewSettings()->getSeverityWeights(),
                        'errorCategoryIds[0]' => $baseEntity->getReviewSettings()->getErrorCategoryIds0(),
                        'errorCategoryIds[1]' => $baseEntity->getReviewSettings()->getErrorCategoryIds1(),
                        'errorCategoryIds[2]' => $baseEntity->getReviewSettings()->getErrorCategoryIds2(),
                        'passFailThreshold'   => $baseEntity->getReviewSettings()->getPassFailThreshold(),
                ]);

                $baseEntity->getReviewSettings()->setDqfId($projectReviewSettings->dqfId);
            }
        }
    }
}

<?php

namespace Matecat\Dqf\Repository\Api;

use Matecat\Dqf\Model\Entity\BaseApiEntity;
use Matecat\Dqf\Model\Entity\File;
use Matecat\Dqf\Model\Entity\FileTargetLang;
use Matecat\Dqf\Model\Entity\MasterProject;
use Matecat\Dqf\Model\Entity\ReviewSettings;
use Matecat\Dqf\Model\Entity\SourceSegment;
use Symfony\Component\Config\Definition\Exception\InvalidTypeException;

class MasterProjectRepository extends AbstractApiRepository {

    /**
     * Delete a record
     *
     * @param int  $dqfId
     * @param null $dqfUuid
     *
     * @return int
     */
    public function delete( $dqfId, $dqfUuid = null ) {
        $masterProject = $this->client->deleteMasterProject( [
                'generic_email' => $this->genericEmail,
                'sessionId'     => $this->sessionId,
                'projectKey'    => $dqfUuid,
                'projectId'     => $dqfId,
        ] );

        return ( $masterProject->status === 'OK' ) ? 1 : 0;
    }

    /**
     * Retrieve a record
     *
     * @param int  $dqfId
     * @param null $dqfUuid
     *
     * @return mixed
     */
    public function get( $dqfId, $dqfUuid = null ) {

        // get master project
        $masterProject = $this->client->getMasterProject( [
                'generic_email' => $this->genericEmail,
                'sessionId'     => $this->sessionId,
                'projectKey'    => $dqfUuid,
                'projectId'     => $dqfId,
        ] );

        $model = $masterProject->model;

        $masterProject = new MasterProject(
                $model->name,
                $model->language->localeCode,
                $model->projectSettings->contentType->id,
                $model->projectSettings->industry->id,
                $model->projectSettings->process->id,
                $model->projectSettings->quality->id
        );

        $masterProject->setDqfId( $dqfId );
        $masterProject->setDqfUuid( $dqfUuid );

        // file(s)
        if ( false === empty( $model->files ) ) {
            foreach ( $model->files as $f ) {
                $file = new File( $f->name, $f->segmentSize );
                $file->setDqfId( $f->id );
                $file->setTmsFileId( $f->tmsFile );
                $masterProject->addFile( $file );
            }
        }



        // assoc targetLang to file(s)
        if ( false === empty( $model->fileProjectTargetLangs ) ) {
            foreach ( $model->fileProjectTargetLangs as $assoc ) {
                $masterProject->assocTargetLanguageToFile($assoc->projectTargetLang->language->localeCode, $masterProject->getFile($assoc->file->name), $assoc->id );
            }
        }

        // review settings
        $reviewSettings = $this->client->getProjectReviewSettings( [
                'generic_email' => $this->genericEmail,
                'sessionId'     => $this->sessionId,
                'projectKey'    => $dqfUuid,
                'projectId'     => $dqfId,
        ] );

        $model = $reviewSettings->model;

        $reviewSettings = new ReviewSettings( $model->type );
        $reviewSettings->setDqfId( $model->id );
        $reviewSettings->setPassFailThreshold( $model->threshold );

        $errorSeveritySetting      = '[';
        $errorSeveritySettingArray = [];
        if ( false === empty( $model->errorSeveritySetting ) ) {
            foreach ( $model->errorSeveritySetting as $setting ) {
                $errorSeveritySettingArray[] = '{"severityId":' . $setting->value . ',"weight":' . $setting->errorSeverity->id . '}';
            }
        }
        $errorSeveritySetting .= implode( ',', $errorSeveritySettingArray );
        $errorSeveritySetting .= ']';

        $reviewSettings->setSeverityWeights( $errorSeveritySetting );
        if ( false === empty( $model->errorTypologySetting[ 0 ]->errorCategory->id ) ) {
            $reviewSettings->setErrorCategoryIds0( $model->errorTypologySetting[ 0 ]->errorCategory->id );
        }
        if ( false === empty( $model->errorTypologySetting[ 1 ]->errorCategory->id ) ) {
            $reviewSettings->setErrorCategoryIds1( $model->errorTypologySetting[ 1 ]->errorCategory->id );
        }
        if ( false === empty( $model->errorTypologySetting[ 2 ]->errorCategory->id ) ) {
            $reviewSettings->setErrorCategoryIds2( $model->errorTypologySetting[ 2 ]->errorCategory->id );
        }

        $masterProject->setReviewSettings( $reviewSettings );

        // source segments

        return $masterProject;
    }

    /**
     * @param BaseApiEntity $baseEntity
     *
     * @return BaseApiEntity
     * @throws \Exception
     */
    public function save( BaseApiEntity $baseEntity ) {
        /** @var $baseEntity MasterProject */
        if ( false === $baseEntity instanceof MasterProject ) {
            throw new InvalidTypeException( 'Entity provided is not an instance of MasterProject' );
        }

        // create master project
        $masterProject = $this->client->createMasterProject( [
                'generic_email'      => $this->genericEmail,
                'sessionId'          => $this->sessionId,
                'name'               => $baseEntity->getName(),
                'sourceLanguageCode' => $baseEntity->getSourceLanguage()->getLocaleCode(),
                'contentTypeId'      => $baseEntity->getContentTypeId(),
                'industryId'         => $baseEntity->getIndustryId(),
                'processId'          => $baseEntity->getProcessId(),
                'qualityLevelId'     => $baseEntity->getQualityLevelId(),
                'clientId'           => $baseEntity->getClientId(),
                'templateName'       => $baseEntity->getTemplateName(),
                'tmsProjectKey'      => $baseEntity->getTmsProjectKey(),
        ] );

        $baseEntity->setDqfId($masterProject->dqfId);
        $baseEntity->setDqfUuid($masterProject->dqfUUID);

        // file(s)
        if ( false === empty( $baseEntity->getFiles() ) ) {
            foreach ( $baseEntity->getFiles() as $file ) {
                $masterProjectFile = $this->client->addMasterProjectFile( [
                        'generic_email'    => $this->genericEmail,
                        'sessionId'        => $this->sessionId,
                        'projectKey'       => $baseEntity->getDqfUuid(),
                        'projectId'        => $baseEntity->getDqfId(),
                        'name'             => $file->getName(),
                        'numberOfSegments' => $file->getNumberOfSegments(),
                        'clientId'         => $file->getClientId(),
                ] );

                $file->setDqfId( $masterProjectFile->dqfId );
            }
        }

        // assoc targetLang to file(s)
        if ( false === empty( $baseEntity->getTargetLanguageAssoc() ) ) {
            foreach ( $baseEntity->getTargetLanguageAssoc() as $targetLanguageCode => $fileTargetLangs ) {

                /** @var FileTargetLang $fileTargetLang */
                foreach ( $fileTargetLangs as $fileTargetLang ) {
                    $projectTargetLanguage = $this->client->addMasterProjectTargetLanguage( [
                            'generic_email'      => $this->genericEmail,
                            'sessionId'          => $this->sessionId,
                            'projectKey'         => $baseEntity->getDqfUuid(),
                            'projectId'          => $baseEntity->getDqfId(),
                            'fileId'             => $fileTargetLang->getFile()->getDqfId(),
                            'targetLanguageCode' => $targetLanguageCode,
                    ] );

                    $fileTargetLang->setDqfId($projectTargetLanguage->dqfId);
                }
            }
        }

        // review settings
        if ( false === empty( $baseEntity->getReviewSettings() ) ) {
            $projectReviewSettings = $this->client->addProjectReviewSettings( [
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
            ] );

            $baseEntity->getReviewSettings()->setDqfId( $projectReviewSettings->dqfId );
        }

        // source segments
        if ( false === empty( $baseEntity->getSourceSegments() ) ) {
            $bodies = [];
            foreach ( $baseEntity->getSourceSegments() as $filename => $sourceSegments ) {
                /** @var SourceSegment $sourceSegment */
                foreach ( $sourceSegments as $sourceSegment ) {
                    $bodies[ $sourceSegment->getFile()->getDqfId() ][] = [
                            'index'         => $sourceSegment->getIndex(),
                            'sourceSegment' => $sourceSegment->getSegment(),
                            'clientId'      => $sourceSegment->getClientId(),
                    ];
                }
            }

            foreach ( $bodies as $fileId => $body ) {
                $updatedSourceSegments = $this->client->addSourceSegmentsInBatchToMasterProject( [
                        'generic_email' => $this->genericEmail,
                        'sessionId'     => $this->sessionId,
                        'projectKey'    => $baseEntity->getDqfUuid(),
                        'projectId'     => $baseEntity->getDqfId(),
                        'fileId'        => $fileId,
                        'body'          => $body
                ] );

                $segmentList = $updatedSourceSegments->segmentList;

                foreach ( $baseEntity->getSourceSegments() as $filename => $sourceSegments ) {
                    $i = 0;
                    /** @var SourceSegment $sourceSegment */
                    foreach ( $sourceSegments as $sourceSegment ) {
                        if ( $sourceSegment->getFile()->getDqfId() === $fileId ) {
                            $sourceSegment->setDqfId( $segmentList[ $i ]->dqfId );
                        }

                        $i++;
                    }
                }
            }
        }

        return $baseEntity;
    }

    /**
     * Update a record
     *
     * @param BaseApiEntity $baseEntity
     *
     * @return mixed
     */
    public function update( BaseApiEntity $baseEntity ) {

        /** @var $baseEntity MasterProject */
        if ( false === $baseEntity instanceof MasterProject ) {
            throw new InvalidTypeException( 'Entity provided is not an instance of MasterProject' );
        }

        // update project
        $masterProject = $this->client->updateMasterProject( [
                'generic_email'      => $this->genericEmail,
                'sessionId'          => $this->sessionId,
                'projectKey'         => $baseEntity->getDqfUuid(),
                'projectId'          => $baseEntity->getDqfId(),
                'name'               => $baseEntity->getName(),
                'sourceLanguageCode' => $baseEntity->getSourceLanguage()->getLocaleCode(),
                'contentTypeId'      => $baseEntity->getContentTypeId(),
                'industryId'         => $baseEntity->getIndustryId(),
                'processId'          => $baseEntity->getProcessId(),
                'qualityLevelId'     => $baseEntity->getQualityLevelId(),
                'clientId'           => $baseEntity->getClientId(),
                'templateName'       => $baseEntity->getTemplateName(),
                'tmsProjectKey'      => $baseEntity->getTmsProjectKey(),
        ] );

        // file(s)
        if ( false === empty( $baseEntity->getFiles() ) ) {
            foreach ( $baseEntity->getFiles() as $file ) {
                if(false === empty($file->getDqfId())){
                    $this->client->updateMasterProjectFile([
                            'generic_email'    => $this->genericEmail,
                            'sessionId'        => $this->sessionId,
                            'projectKey'       => $baseEntity->getDqfUuid(),
                            'projectId'        => $baseEntity->getDqfId(),
                            'name'             => $file->getName(),
                            'numberOfSegments' => $file->getNumberOfSegments(),
                            'clientId'         => $file->getClientId(),
                            'fileId'           => $file->getDqfId(),
                    ]);
                } else {
                    $masterProjectFile = $this->client->addMasterProjectFile( [
                            'generic_email'    => $this->genericEmail,
                            'sessionId'        => $this->sessionId,
                            'projectKey'       => $baseEntity->getDqfUuid(),
                            'projectId'        => $baseEntity->getDqfId(),
                            'name'             => $file->getName(),
                            'numberOfSegments' => $file->getNumberOfSegments(),
                            'clientId'         => $file->getClientId(),
                    ] );

                    $file->setDqfId( $masterProjectFile->dqfId );
                }
            }
        }

        // assoc targetLang to file(s)
        if ( false === empty( $baseEntity->getTargetLanguageAssoc() ) ) {

            // delete ALL target lang assoc
            foreach ( $baseEntity->getFiles() as $file ) {
                $masterProjectTargetLanguages = $this->client->getMasterProjectTargetLanguages( [
                        'generic_email'      => $this->genericEmail,
                        'sessionId'          => $this->sessionId,
                        'projectKey'         => $baseEntity->getDqfUuid(),
                        'projectId'          => $baseEntity->getDqfId(),
                        'fileId'             => $file->getDqfId(),
                ] );

                foreach ($masterProjectTargetLanguages->modelList as $masterProjectTargetLanguage) {
                    $this->client->deleteMasterProjectTargetLanguage( [
                            'generic_email'      => $this->genericEmail,
                            'sessionId'          => $this->sessionId,
                            'projectKey'         => $baseEntity->getDqfUuid(),
                            'projectId'          => $baseEntity->getDqfId(),
                            'fileId'             => $file->getDqfId(),
                            'targetLangCode'     => $masterProjectTargetLanguage->localeCode,
                    ] );
                }
            }

            // And then reset values
            foreach ( $baseEntity->getTargetLanguageAssoc() as $targetLanguageCode => $fileTargetLangs ) {
                /** @var FileTargetLang $fileTargetLang */
                foreach ( $fileTargetLangs as $fileTargetLang ) {
                    $projectTargetLanguage = $this->client->addMasterProjectTargetLanguage( [
                            'generic_email'      => $this->genericEmail,
                            'sessionId'          => $this->sessionId,
                            'projectKey'         => $baseEntity->getDqfUuid(),
                            'projectId'          => $baseEntity->getDqfId(),
                            'fileId'             => $fileTargetLang->getFile()->getDqfId(),
                            'targetLanguageCode' => $targetLanguageCode,
                    ] );

                    $fileTargetLang->setDqfId($projectTargetLanguage->dqfId);
                }
            }
        }

        // review settings
        if ( false === empty( $baseEntity->getReviewSettings() ) ) {
            if(false === empty($baseEntity->getReviewSettings()->getDqfId())){
                $this->client->updateProjectReviewSettings( [
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
                ] );
            } else {
                $projectReviewSettings = $this->client->addProjectReviewSettings( [
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
                ] );

                $baseEntity->getReviewSettings()->setDqfId( $projectReviewSettings->dqfId );
            }
        }

        // source segments

        return $masterProject;
    }
}

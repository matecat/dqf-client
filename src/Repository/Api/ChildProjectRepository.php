<?php

namespace Matecat\Dqf\Repository\Api;

use Matecat\Dqf\Model\Entity\BaseApiEntity;
use Matecat\Dqf\Model\Entity\ChildProject;
use Matecat\Dqf\Model\Entity\File;
use Matecat\Dqf\Model\Entity\FileTargetLang;
use Matecat\Dqf\Model\Repository\CrudApiRepositoryInterface;

class ChildProjectRepository extends AbstractApiRepository implements CrudApiRepositoryInterface {
    /**
     * Delete a record
     *
     * @param int  $dqfId
     * @param null $dqfUuid
     *
     * @return int
     */
    public function delete( $dqfId, $dqfUuid = null ) {
        $childProject = $this->client->deleteChildProject( [
                'generic_email' => $this->genericEmail,
                'sessionId'     => $this->sessionId,
                'projectKey'    => $dqfUuid,
                'projectId'     => $dqfId,
        ] );

        return ( $childProject->status === 'OK' ) ? 1 : 0;
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
        // get child project
        $childProject = $this->client->getChildProject( [
                'generic_email' => $this->genericEmail,
                'sessionId'     => $this->sessionId,
                'projectKey'    => $dqfUuid,
                'projectId'     => $dqfId,
        ] );

        $model = $childProject->model;

        $childProject = new ChildProject( $model->type );
        $childProject->setName( $model->name );
        $childProject->setDqfId( $dqfId );
        $childProject->setDqfUuid( $dqfUuid );

        // file(s)
        if ( false === empty( $model->files ) ) {
            foreach ( $model->files as $f ) {
                $file = new File( $f->name, $f->segmentSize );
                $file->setDqfId( $f->id );
                $file->setTmsFileId( $f->tmsFile );
                $childProject->addFile( $file );
            }
        }

        // assoc targetLang to file(s)
        if ( false === empty( $model->fileProjectTargetLangs ) ) {
            foreach ( $model->fileProjectTargetLangs as $assoc ) {
                $childProject->assocTargetLanguageToFile( $assoc->projectTargetLang->language->localeCode, $childProject->getFile( $assoc->file->name ), $assoc->id );
            }
        }

        return $childProject;
    }

    /**
     * Save a record
     *
     * @param BaseApiEntity $baseEntity
     *
     * @return BaseApiEntity
     */
    public function save( BaseApiEntity $baseEntity ) {
        /** @var $baseEntity ChildProject */
        if ( false === $baseEntity instanceof ChildProject ) {
            throw new \InvalidArgumentException( 'Entity provided is not an instance of ChildProject' );
        }

        if ( empty( $baseEntity->getMasterProject() ) ) {
            throw new \DomainException( 'MasterProject MUST be set during creation of a ChildProject' );
        }

        // create child project
        $this->createProject( $baseEntity );

        // assoc targetLang to file(s)
        $this->saveTargetLanguageAssoc( $baseEntity );

        return $baseEntity;
    }

    /**
     * @param ChildProject $baseEntity
     */
    private function createProject( ChildProject $baseEntity ) {
        $childProject = $this->client->createChildProject( [
                'generic_email'   => $this->genericEmail,
                'sessionId'       => $this->sessionId,
                'parentKey'       => $baseEntity->getMasterProject()->getDqfUuid(),
                'type'            => $baseEntity->getType(),
                'name'            => $baseEntity->getName(),
                'clientId'        => $baseEntity->getClientId(),
                'assignee'        => $baseEntity->getAssignee(),
                'assigner'        => $baseEntity->getAssigner(),
                'reviewSettingId' => $baseEntity->getReviewSettingsId(),
                'isDummy'         => $baseEntity->isDummy(),
        ] );

        $baseEntity->setDqfId( $childProject->dqfId );
        $baseEntity->setDqfUuid( $childProject->dqfUUID );
    }

    /**
     * @param ChildProject $baseEntity
     */
    private function saveTargetLanguageAssoc( ChildProject $baseEntity ) {
        if ( false === empty( $baseEntity->getTargetLanguageAssoc() ) ) {
            foreach ( $baseEntity->getTargetLanguageAssoc() as $targetLanguageCode => $fileTargetLangs ) {

                /** @var FileTargetLang $fileTargetLang */
                foreach ( $fileTargetLangs as $fileTargetLang ) {
                    if ( false === empty( $fileTargetLang->getFile()->getDqfId() ) ) {
                        $projectTargetLanguage = $this->client->addChildProjectTargetLanguage( [
                                'generic_email'      => $this->genericEmail,
                                'sessionId'          => $this->sessionId,
                                'projectKey'         => $baseEntity->getDqfUuid(),
                                'projectId'          => $baseEntity->getDqfId(),
                                'fileId'             => $fileTargetLang->getFile()->getDqfId(),
                                'targetLanguageCode' => $targetLanguageCode,
                        ] );

                        $fileTargetLang->setDqfId( $projectTargetLanguage->dqfId );
                    }
                }
            }
        }
    }

    /**
     * Update a record
     *
     * @param BaseApiEntity $baseEntity
     *
     * @return mixed
     */
    public function update( BaseApiEntity $baseEntity ) {
        // create child project
        $this->updateProject( $baseEntity );

        // assoc targetLang to file(s)
        $this->updateTargetLanguageAssoc( $baseEntity );
    }

    /**
     * @param BaseApiEntity $baseEntity
     *
     * @return mixed
     */
    private function updateProject( BaseApiEntity $baseEntity ) {
        if ( empty( $baseEntity->getMasterProject() ) ) {
            throw new \DomainException( 'MasterProject MUST be set during the update of a ChildProject' );
        }

        return $this->client->updateChildProject( [
                'generic_email'   => $this->genericEmail,
                'sessionId'       => $this->sessionId,
                'projectKey'      => $baseEntity->getDqfUuid(),
                'projectId'       => $baseEntity->getDqfId(),
                'parentKey'       => $baseEntity->getMasterProject()->getDqfUuid(),
                'type'            => $baseEntity->getType(),
                'name'            => $baseEntity->getName(),
                'clientId'        => $baseEntity->getClientId(),
                'assignee'        => $baseEntity->getAssignee(),
                'assigner'        => $baseEntity->getAssigner(),
                'reviewSettingId' => $baseEntity->getReviewSettingsId(),
                'isDummy'         => $baseEntity->isDummy(),
        ] );
    }

    private function updateTargetLanguageAssoc( BaseApiEntity $baseEntity ) {
        if ( false === empty( $baseEntity->getTargetLanguageAssoc() ) ) {

            // delete ALL target lang assoc
            foreach ( $baseEntity->getFiles() as $file ) {
                $childProjectTargetLanguages = $this->client->getChildProjectTargetLanguages( [
                        'generic_email' => $this->genericEmail,
                        'sessionId'     => $this->sessionId,
                        'projectKey'    => $baseEntity->getDqfUuid(),
                        'projectId'     => $baseEntity->getDqfId(),
                        'fileId'        => $file->getDqfId(),
                ] );

                foreach ( $childProjectTargetLanguages->modelList as $childProjectTargetLanguage ) {
                    $this->client->deleteChildProjectTargetLanguage( [
                            'generic_email'  => $this->genericEmail,
                            'sessionId'      => $this->sessionId,
                            'projectKey'     => $baseEntity->getDqfUuid(),
                            'projectId'      => $baseEntity->getDqfId(),
                            'fileId'         => $file->getDqfId(),
                            'targetLangCode' => $childProjectTargetLanguage->localeCode,
                    ] );
                }
            }

            // And then reset values
            foreach ( $baseEntity->getTargetLanguageAssoc() as $targetLanguageCode => $fileTargetLangs ) {
                /** @var FileTargetLang $fileTargetLang */
                foreach ( $fileTargetLangs as $fileTargetLang ) {
                    $projectTargetLanguage = $this->client->addChildProjectTargetLanguage( [
                            'generic_email'      => $this->genericEmail,
                            'sessionId'          => $this->sessionId,
                            'projectKey'         => $baseEntity->getDqfUuid(),
                            'projectId'          => $baseEntity->getDqfId(),
                            'fileId'             => $fileTargetLang->getFile()->getDqfId(),
                            'targetLanguageCode' => $targetLanguageCode,
                    ] );

                    $fileTargetLang->setDqfId( $projectTargetLanguage->dqfId );
                }
            }
        }
    }
}

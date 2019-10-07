<?php

namespace Matecat\Dqf\Repository\Api;

use Matecat\Dqf\Model\Entity\BaseApiEntity;
use Matecat\Dqf\Model\Entity\MasterProject;
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

        return ($masterProject->status === 'OK') ? 1 : 0;
    }

    /**
     * Retrive a record
     *
     * @param int  $dqfId
     * @param null $dqfUuid
     *
     * @return mixed
     */
    public function get( $dqfId, $dqfUuid = null ) {
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

        $masterProject->setDqfId($dqfId);
        $masterProject->setDqfUuid($dqfUuid);

        return $masterProject;





//        ["message"]=>
//  string(28) "Project successfully fetched"
//        ["class"]=>
//  string(29) "net.taus.dqf.model.v3.Project"
//        ["model"]=>
//  object(stdClass)#241 (32) {
//  ["id"]=>
//    int(66945)
//    ["completionTimestamp"]=>
//    NULL
//    ["creationTimestamp"]=>
//    int(1570457076000)
//    ["name"]=>
//    string(19) "master-project-test"
//        ["ownerUser"]=>
//    object(stdClass)#247 (3) {
//    ["id"]=>
//      int(10943)
//      ["email"]=>
//      string(32) "luca.defranceschi@translated.net"
//        ["tausId"]=>
//      int(150)
//    }
//["organization"]=>
//object(stdClass)#248 (3) {
//["id"]=>
//int(8)
//["name"]=>
//string(14) "Translated.net"
//["tausId"]=>
//int(367)
//}
//["status"]=>
//    string(11) "initialized"
//["type"]=>
//    string(11) "translation"
//["updateTimestamp"]=>
//    int(1570457076000)
//    ["user"]=>
//    object(stdClass)#245 (3) {
//    ["id"]=>
//      int(10943)
//      ["email"]=>
//      string(32) "luca.defranceschi@translated.net"
//["tausId"]=>
//      int(150)
//    }
//    ["uuid"]=>
//    string(36) "d927a7a3-2551-4a99-a038-0767d5b026ed"
//["level"]=>
//    int(0)
//    ["isReturn"]=>
//    bool(false)
//    ["files"]=>
//    array(0) {
//}
//    ["integrator"]=>
//    object(stdClass)#234 (9) {
//    ["id"]=>
//      int(5)
//      ["admin"]=>
//      bool(false)
//      ["description"]=>
//      string(7) "MateCat"
//["catTool"]=>
//      object(stdClass)#235 (2) {
//      ["id"]=>
//        int(12)
//        ["name"]=>
//        string(7) "MateCat"
//      }
//      ["url"]=>
//      NULL
//      ["version"]=>
//      NULL
//      ["enableApi"]=>
//      bool(true)
//      ["enableWaiter"]=>
//      bool(false)
//      ["enableCreditsCheck"]=>
//      bool(true)
//    }
//    ["integratorProjectMap"]=>
//    NULL
//    ["language"]=>
//    object(stdClass)#236 (3) {
//    ["id"]=>
//      int(125)
//      ["localeCode"]=>
//      string(5) "it-IT"
//["name"]=>
//      string(14) "Italian(Italy)"
//    }
//    ["projectSettings"]=>
//    object(stdClass)#246 (5) {
//    ["id"]=>
//      int(32459)
//      ["industry"]=>
//      object(stdClass)#232 (2) {
//      ["id"]=>
//        int(2)
//        ["name"]=>
//        string(10) "Automotive"
//      }
//      ["process"]=>
//      object(stdClass)#259 (2) {
//      ["id"]=>
//        int(3)
//        ["name"]=>
//        string(11) "MT+PE+TM+HT"
//      }
//      ["contentType"]=>
//      object(stdClass)#240 (2) {
//      ["id"]=>
//        int(1)
//        ["name"]=>
//        string(19) "User Interface Text"
//      }
//      ["quality"]=>
//      object(stdClass)#230 (2) {
//      ["id"]=>
//        int(1)
//        ["name"]=>
//        string(11) "Good Enough"
//      }
//    }
//    ["projectReviewSetting"]=>
//    NULL
//    ["projectTargetLangs"]=>
//    array(0) {
//}
//    ["fileProjectTargetLangs"]=>
//    array(0) {
//}
//    ["left"]=>
//    int(1)
//    ["right"]=>
//    int(2)
//    ["year"]=>
//    int(2019)
//    ["yearWeek"]=>
//    int(201940)
//    ["yearMonth"]=>
//    int(201910)
//    ["yearMonthDay"]=>
//    int(20191007)
//    ["userRoot"]=>
//    bool(true)
//    ["organizationRoot"]=>
//    bool(true)
//    ["meta"]=>
//    bool(false)
//    ["active"]=>
//    bool(true)
//    ["dummy"]=>
//    bool(false)
//  }

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
        ] );

        $baseEntity->setDqfId( $masterProject->dqfId );
        $baseEntity->setDqfUuid( $masterProject->dqfUUID );

        // template

        // file(s)

        // review settings

        // source segments

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

        $masterProject = $this->get($baseEntity->getDqfId(), $baseEntity->getDqfUuid());

        // update project

        // template

        // file(s)

        // review settings

        // source segments

        return $masterProject;
    }
}
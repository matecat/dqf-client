# Submitting data to DQF: a complete workflow

Here is a typical, complete and detailed workflow. These are the principal steps:
 
* creation of a master project;
* applying settings for master project;
* creation of a translation child;
* submitting of a translation in batch, and then editing a single segment translation;
* creation of a revision child;
* submitting a revision for a segment translation;
* deleting of projects.

## Standard approach (Client approach)

```php
// ...

use Ramsey\Uuid\Uuid;

$sourceFile = $this->getSourceFile();       // a fake array used as source file
$targetFile = $this->getTranslationFile();  // a fake array used as target file

/**
****************************************************************************
* create a project
****************************************************************************
*/

// checking if the sourceLanguageCode is valid first
$languageCode = $client->checkLanguageCode([
    'languageCode' => $sourceFile[ 'lang' ],
]);

$masterProjectClientId = Uuid::uuid4()->toString();
$masterProject         = $client->createMasterProject([
    'sessionId'          => $sessionId,
    'name'               => 'master-workflow-test',
    'sourceLanguageCode' => $sourceFile[ 'lang' ],
    'contentTypeId'      => 1,
    'industryId'         => 2,
    'processId'          => 1,
    'qualityLevelId'     => 1,
    'clientId'           => $masterProjectClientId,
]);

/**
****************************************************************************
* add a template, retrieve and delete it
****************************************************************************
*/

$client->addTemplate([
    'sessionId'      => $sessionId,
    'name'           => 'test-template-' . Uuid::uuid4()->toString(),
    'contentTypeId'  => rand(1, 15),
    'industryId'     => rand(1, 24),
    'processId'      => rand(1, 4),
    'qualityLevelId' => rand(1, 2),
    'isPublic'       => true,
]);

$templates = $client->getTemplates([
    'sessionId' => $sessionId,
]);

$projectTemplateId = $templates->modelList[ 0 ]->id;

$getTemplate = $client->getTemplate([
    'projectTemplateId' => $projectTemplateId,
    'sessionId'         => $sessionId,
]);

$deleteTemplate = $client->deleteTemplate([
    'projectTemplateId' => $projectTemplateId,
    'sessionId'         => $sessionId,
]);

/**
****************************************************************************
* set a file for the project
****************************************************************************
*/

$masterProjectFile = $client->addMasterProjectFile([
    'sessionId'        => $sessionId,
    'projectKey'       => $masterProject->dqfUUID,
    'projectId'        => $masterProject->dqfId,
    'name'             => $sourceFile[ 'name' ],
    'numberOfSegments' => count($sourceFile[ 'segments' ]),
    'clientId'         => $sourceFile[ 'uuid' ],
]);

/**
****************************************************************************
* set target language for the file (checking if the lang is valid first)
****************************************************************************
*/

$languageCode = $client->checkLanguageCode([
    'languageCode' => $targetFile[ 'lang' ],
]);

$masterProjectTargetLang = $client->addMasterProjectTargetLanguage([
    'sessionId'          => $sessionId,
    'projectKey'         => $masterProject->dqfUUID,
    'projectId'          => $masterProject->dqfId,
    'fileId'             => $masterProjectFile->dqfId,
    'targetLanguageCode' => $targetFile[ 'lang' ],
]);

/**
****************************************************************************
* set review settings for the project
****************************************************************************
*/

$projectReviewSettings = $client->addProjectReviewSettings([
    'sessionId'           => $sessionId,
    'projectKey'          => $masterProject->dqfUUID,
    'projectId'           => $masterProject->dqfId,
    'reviewType'          => 'combined',
    'severityWeights'     => '[{"severityId":"1","weight":1}, {"severityId":"2","weight":2}, {"severityId":"3","weight":3}, {"severityId":"4","weight":4}]',
    'errorCategoryIds'    => [1, 2, 3],
    'passFailThreshold'   => 1.00,
]);

/**
****************************************************************************
* update source segments in batch
****************************************************************************
*/

$updatedSourceSegments = $client->addSourceSegmentsInBatchToMasterProject([
    'sessionId'  => $sessionId,
    'projectKey' => $masterProject->dqfUUID,
    'projectId'  => $masterProject->dqfId,
    'fileId'     => $masterProjectFile->dqfId,
    'body'       => $sourceFile[ 'segments' ]
]);

/**
****************************************************************************
* create a 'translation' child node
****************************************************************************
*/

$childTranslation = $client->createChildProject([
    'sessionId' => $sessionId,
    'parentKey' => $masterProject->dqfUUID,
    'type'      => 'translation',
    'name'      => 'child-workflow-test',
    'isDummy'   => true,
]);

/**
****************************************************************************
* set target language for the child node
****************************************************************************
*/

$childTranslationTargetLang = $client->addChildProjectTargetLanguage([
    'sessionId'          => $sessionId,
    'projectKey'         => $childTranslation->dqfUUID,
    'projectId'          => $childTranslation->dqfId,
    'fileId'             => $masterProjectFile->dqfId,
    'targetLanguageCode' => $targetFile[ 'lang' ],
]);

/**
****************************************************************************
* update translations in batch
****************************************************************************
*/

$segmentPairs = $targetFile[ 'segmentPairs' ];
foreach ($segmentPairs as $key => $segmentPair) {
    $segmentPairs[ $key ][ 'sourceSegmentId' ] = $client->getSegmentId([
        'sessionId' => $sessionId,
        'clientId'  => $sourceFile[ 'segments' ][ $key ][ 'clientId' ],
    ])->dqfId;
}

$translationsBatch = $client->addTranslationsForSourceSegmentsInBatch([
    'sessionId'      => $sessionId,
    'projectKey'     => $childTranslation->dqfUUID,
    'projectId'      => $childTranslation->dqfId,
    'fileId'         => $masterProjectFile->dqfId,
    'targetLangCode' => $targetFile[ 'lang' ],
    'body'           => $segmentPairs,
]);

/**
****************************************************************************
* update a single segment translation
****************************************************************************
*/

$firstSegmentId = $client->getSegmentId([
    'sessionId' => $sessionId,
    'clientId'  => $sourceFile[ 'segments' ][ 0 ][ 'clientId' ],
]);

$firstTranslationId = $client->getTranslationId([
    'sessionId' => $sessionId,
    'clientId'  => $targetFile[ 'segmentPairs' ][ 0 ][ 'clientId' ],
]);

$updateSingleSegmentTranslation = $client->updateTranslationForASegment([
    'sessionId'       => $sessionId,
    'projectKey'      => $childTranslation->dqfUUID,
    'projectId'       => $childTranslation->dqfId,
    'fileId'          => $masterProjectFile->dqfId,
    'targetLangCode'  => $targetFile[ 'lang' ],
    'sourceSegmentId' => $firstSegmentId->dqfId,
    'translationId'   => $firstTranslationId->dqfId,
    'segmentOriginId' => 4,
    'targetSegment'   => "The frog in Spain",
    'editedSegment'   => "The frog in Spain (from Barcelona)",
    'time'            => 5435435,
]);

/**
****************************************************************************
* get the complete translation object previously edited
****************************************************************************
*/

$translationForASegment = $client->getTranslationForASegment([
    'sessionId'       => $sessionId,
    'projectKey'      => $childTranslation->dqfUUID,
    'projectId'       => $childTranslation->dqfId,
    'fileId'          => $masterProjectFile->dqfId,
    'targetLangCode'  => $targetFile[ 'lang' ],
    'sourceSegmentId' => $firstSegmentId->dqfId,
    'translationId'   => $firstTranslationId->dqfId,
]);

/**
****************************************************************************
* check the status of child node
****************************************************************************
*/

$childNodeStatus = $client->getChildProjectStatus([
    'sessionId'  => $sessionId,
    'projectKey' => $childTranslation->dqfUUID,
    'projectId'  => $childTranslation->dqfId,
]);

/**
****************************************************************************
* create a 'review' child node
****************************************************************************
*/

$childReview = $client->createChildProject([
    'sessionId' => $sessionId,
    'parentKey' => $childTranslation->dqfUUID,
    'type'      => 'review',
    'name'      => 'child-revision-workflow-test',
    'isDummy'   => false, // for type = 'revise' isDummy = false is not allowed
]);

/**
****************************************************************************
* set target language for the child node
****************************************************************************
*/

$childReviewTargetLang = $client->addChildProjectTargetLanguage([
    'sessionId'          => $sessionId,
    'projectKey'         => $childReview->dqfUUID,
    'projectId'          => $childReview->dqfId,
    'fileId'             => $masterProjectFile->dqfId,
    'targetLanguageCode' => $targetFile[ 'lang' ],
]);

/**
****************************************************************************
* set review settings for the child node
* (this is mandatory for a revision child node, but can be inherited by master project settings if already declared)
****************************************************************************
*/

$childNodeReviewSettings = $client->addProjectReviewSettings([
    'sessionId'           => $sessionId,
    'projectKey'          => $childReview->dqfUUID,
    'projectId'           => $childReview->dqfId,
    'reviewType'          => 'combined',
    'severityWeights'     => '[{"severityId":"1","weight":1}, {"severityId":"2","weight":2}, {"severityId":"3","weight":3}, {"severityId":"4","weight":4}]',
    'errorCategoryIds'    => [1, 2, 3],
    'passFailThreshold'   => 1.00,
]);

/**
****************************************************************************
* add a review template, retrieve and delete it
****************************************************************************
*/

$client->addReviewTemplate([
    'sessionId'           => $sessionId,
    'projectKey'          => $childReview->dqfUUID,
    'templateName'        => 'test-review-template-' . Uuid::uuid4()->toString(),
    'reviewType'          => 'combined',
    'severityWeights'     => '[{"severityId":"1","weight":1}, {"severityId":"2","weight":2}, {"severityId":"3","weight":3}, {"severityId":"4","weight":4}]',
    'errorCategoryIds'    => [1, 2, 3],
    'passFailThreshold'   => 1.00,
    'isPublic'            => true,
]);

$templates = $client->getReviewTemplates([
    'sessionId' => $sessionId,
]);

$projectTemplateId = $templates->modelList[ 0 ]->id;

$getTemplate = $client->getReviewTemplate([
    'reviewTemplateId' => $projectTemplateId,
    'sessionId'        => $sessionId,
]);

$deleteTemplate = $client->deleteReviewTemplate([
    'reviewTemplateId' => $projectTemplateId,
    'sessionId'        => $sessionId,
]);

/**
****************************************************************************
* check for segmentsId for the file
****************************************************************************
*/

$sourceSegmentIds = $client->getSourceSegmentIdsForAFile([
    'sessionId'      => $sessionId,
    'projectKey'     => $childReview->dqfUUID,
    'projectId'      => $childReview->dqfId,
    'fileId'         => $masterProjectFile->dqfId,
    'targetLangCode' => $targetFile[ 'lang' ],
]);

/**
****************************************************************************
* submitting revisions(corrections) in batch
****************************************************************************
*/

$corrections = [];

// adding a first correction (2 errors)
$corrections[] = [
    "clientId" => Uuid::uuid4()->toString(),
    "comment"  => "Sample review comment",
    "errors"   => [
        [
            "errorCategoryId" => 11,
            "severityId"      => 2,
            "charPosStart"    => null,
            "charPosEnd"      => null,
            "isRepeated"      => false
        ],
        [
            "errorCategoryId" => 9,
            "severityId"      => 1,
            "charPosStart"    => 1,
            "charPosEnd"      => 5,
            "isRepeated"      => false
        ],
    ],
];

// adding a second correction (correction)
$corrections[] = [
    "clientId"   => Uuid::uuid4()->toString(),
    "comment"    => "Another review comment",
    "correction" => [
        "content"    => "The frog in Spain (from Barcelona)",
        "time"       => 10000,
        "detailList" => [
            [
                "subContent" => "(from Barcelona)",
                "type"       => "deleted"
            ],
            [
                "subContent" => "The frog in Spain  ",
                "type"       => "unchanged"
            ],
        ]
    ]
];

$batchId = Uuid::uuid4()->toString();

$updateReviewInBatch = $client->updateReviewInBatch([
    'sessionId'      => $sessionId,
    'projectKey'     => $childReview->dqfUUID,
    'projectId'      => $childReview->dqfId,
    'fileId'         => $masterProjectFile->dqfId,
    'targetLangCode' => $targetFile[ 'lang' ],
    'translationId'  => $firstTranslationId->dqfId,
    'batchId'        => $batchId,
    'overwrite'      => true,
    'body'           => $corrections,
]);

/**
****************************************************************************
* resetting reviews before deleting all the project and child nodes
* (it's forbidden to delete a child node with reviews)
****************************************************************************
*/

$updateReviewInBatch = $client->updateReviewInBatch([
    'sessionId'      => $sessionId,
    'projectKey'     => $childReview->dqfUUID,
    'projectId'      => $childReview->dqfId,
    'fileId'         => $masterProjectFile->dqfId,
    'targetLangCode' => $targetFile[ 'lang' ],
    'translationId'  => $firstTranslationId->dqfId,
    'batchId'        => $batchId,
    'overwrite'      => true,
    'body'           => [],
]);

/**
****************************************************************************
* delete the master and child nodes
****************************************************************************
*/

$deleteChildReview = $client->deleteChildProject([
    'sessionId'  => $sessionId,
    'projectKey' => $childReview->dqfUUID,
    'projectId'  => $childReview->dqfId,
]);

$deleteChildProject = $client->deleteChildProject([
    'sessionId'  => $sessionId,
    'projectKey' => $childTranslation->dqfUUID,
    'projectId'  => $childTranslation->dqfId,
]);

$deleteMasterProject = $client->deleteMasterProject([
    'sessionId'  => $sessionId,
    'projectKey' => $masterProject->dqfUUID,
    'projectId'  => $masterProject->dqfId,
]);

```

## Abstraction layer approach (OOP approach)

```php
//...

/**
****************************************************************************
* create the master project
****************************************************************************
*/

$masterProject = new MasterProject('master-project-test', 'it-IT', 1, 2, 3, 1);

// file(s)
$file = new File('test-file', 3);
$file->setClientId(Uuid::uuid4()->toString());
$masterProject->addFile($file);

// assoc targetLang to file(s)
$masterProject->assocTargetLanguageToFile('en-US', $file);
$masterProject->assocTargetLanguageToFile('fr-FR', $file);

// review settings
$reviewSettings = new ReviewSettings(Constants::REVIEW_TYPE_COMBINED);
$reviewSettings->addErrorCategoryId(1);
$reviewSettings->addErrorCategoryId(2);
$reviewSettings->addErrorCategoryId(3);
$reviewSettings->addErrorCategoryId(4);
$reviewSettings->addErrorCategoryId(5);

$sev1 = new Severity(1,1);
$sev2 = new Severity(2,2);
$sev3 = new Severity(3,3);
$sev4 = new Severity(4,4);

$reviewSettings->addSeverityWeight($sev1);
$reviewSettings->addSeverityWeight($sev2);
$reviewSettings->addSeverityWeight($sev3);
$reviewSettings->addSeverityWeight($sev4);

$reviewSettings->setPassFailThreshold(0.00);
$masterProject->setReviewSettings($reviewSettings);

// source segments
foreach ($sourceSegments as $sourceSegment) {
    $masterProject->addSourceSegment($sourceSegment);
}

// save the master project
$masterProjectRepo->save($masterProject);

/**
****************************************************************************
* create the child project (translation)
****************************************************************************
*/

$childProject = new ChildProject(Constants::PROJECT_TYPE_TRANSLATION);
$childProject->setParentProjectUuid($masterProject->getDqfUuid());
$childProject->setName('Translation Job');

// assoc targetLang to file(s)
$childProject->assocTargetLanguageToFile('en-US', $masterProject->getFiles()[ 0 ]);
$childProject->assocTargetLanguageToFile('fr-FR', $masterProject->getFiles()[ 0 ]);

// review settings
$reviewSettings = new ReviewSettings(Constants::REVIEW_TYPE_COMBINED);
$reviewSettings->addErrorCategoryId(1);
$reviewSettings->addErrorCategoryId(2);
$reviewSettings->addErrorCategoryId(3);
$reviewSettings->addErrorCategoryId(4);
$reviewSettings->addErrorCategoryId(5);

$sev1 = new Severity(1,1);
$sev2 = new Severity(2,2);
$sev3 = new Severity(3,3);
$sev4 = new Severity(4,4);

$reviewSettings->addSeverityWeight($sev1);
$reviewSettings->addSeverityWeight($sev2);
$reviewSettings->addSeverityWeight($sev3);
$reviewSettings->addSeverityWeight($sev4);

$reviewSettings->setPassFailThreshold(0.00);
$childProject->setReviewSettings($reviewSettings);

// save the child project
$childProjectRepo->save($childProject);
 
/**
****************************************************************************
* build the translation batch
****************************************************************************
*/

$translationBatch = new TranslationBatch($childProject, $file, 'en-US');
$segmTrans1       = new TranslatedSegment(22, 1, 'en-US', $this->getSourceSegments($file)[ 0 ]->getDqfId(), '', 'The frog in Spain', 1);
$segmTrans2       = new TranslatedSegment(22, 2, 'en-US', $this->getSourceSegments($file)[ 1 ]->getDqfId(), 'croaks in countryside matus.', 'croaks in countryside.', 2);
$segmTrans3       = new TranslatedSegment(22, 3, 'en-US', $this->getSourceSegments($file)[ 2 ]->getDqfId(), 'This is just a tongue twister', '', 3);

$translationBatch->addSegment($segmTrans1);
$translationBatch->addSegment($segmTrans2);
$translationBatch->addSegment($segmTrans3);

// save the translation batch
$translationBatch = $translationRepository->save($translationBatch);

/**
****************************************************************************
* update a segment translation
****************************************************************************
*/

// update a segment translation
$firstSegment = $translationBatch->getSegments()[0];
$segment->setTargetSegment( 'The frog in Spain' );
$segment->setEditedSegment( 'The frog in Spain (from Barcelona)' );
$translationRepository->update($translationBatch->getChildProject(), $translationBatch->getFile(), $segment );

/**
****************************************************************************
* create the child project (review)
****************************************************************************
*/

$childProject = new ChildProject(Constants::PROJECT_TYPE_REVIEW);
$childProject->setParentProjectUuid($segment->getChildProject()->getDqfUuid());
$childProject->setName('Review Job');

// assoc targetLang to file(s)
$childProject->assocTargetLanguageToFile('en-US', $file);
$childProject->assocTargetLanguageToFile('fr-FR', $file);

// review settings
$reviewSettings = new ReviewSettings(Constants::REVIEW_TYPE_COMBINED);
$reviewSettings->addErrorCategoryId(1);
$reviewSettings->addErrorCategoryId(2);
$reviewSettings->addErrorCategoryId(3);

$sev1 = new Severity(1,1);
$sev2 = new Severity(2,2);
$sev3 = new Severity(3,3);
$sev4 = new Severity(4,4);

$reviewSettings->addSeverityWeight($sev1);
$reviewSettings->addSeverityWeight($sev2);
$reviewSettings->addSeverityWeight($sev3);
$reviewSettings->addSeverityWeight($sev4);

$reviewSettings->setPassFailThreshold(0.00);
$childProject->setReviewSettings($reviewSettings);

// save the child project
$childReview = $childProjectRepo->save($childProject);

/**
****************************************************************************
* create a review batch
****************************************************************************
*/

$correction = new RevisionCorrection('Another review comment', 10000);
$correction->addItem(new RevisionCorrectionItem('review', 'deleted'));
$correction->addItem(new RevisionCorrectionItem('Another comment', 'unchanged'));

$reviewedSegment = new ReviewedSegment('this is a comment');
$reviewedSegment->addError(new RevisionError(11, 2));
$reviewedSegment->addError(new RevisionError(9, 1, 1, 5));
$reviewedSegment->setCorrection($correction);

$reviewedSegment2 = new ReviewedSegment('this is another comment');
$reviewedSegment2->addError(new RevisionError(10, 2));
$reviewedSegment2->addError(new RevisionError(11, 1, 1, 5));
$reviewedSegment2->setCorrection($correction);

$batchId = Uuid::uuid4()->toString();
$reviewBatch = new ReviewBatch($childReview, $file, 'en-US', $segment, $batchId);
$reviewBatch->addReviewedSegment($reviewedSegment);
$reviewBatch->addReviewedSegment($reviewedSegment2);

$reviewRepository->save($reviewBatch);

/**
****************************************************************************
* delete the master and child nodes
****************************************************************************
*/

// resetting reviews before deleting all the project and child nodes
$emptyReviewBatch = new ReviewBatch($childReview, $file, 'en-US', $segment, $batchId);
$emptyBatch = $reviewRepository->save($emptyReviewBatch);

// deleting the review project
$childProjectRepo->delete($childReview);

// deleting the translation project
$childProjectRepo->delete($childProject);

// deleting the master project
$masterProjectRepo->delete($masterProject);

```
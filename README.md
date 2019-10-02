# DQF Client

**DQF Client** is a Client tailored for [DQF Tool](https://www.taus.net/) easy integration into a PHP application.

## Basic Usage

To instantiate the Client do the following:

```php
use Matecat\Dqf\Client;

$dqfClient = new Client([
    'apiKey'         => 'aaaaa',          // REQUIRED 
    'idPrefix'       => 'zzzzz',          // REQUIRED
    'encryptionKey'  => 'yyyyy',          // REQUIRED
    'encryptionIV'   => 'xxxxx',          // REQUIRED
    'debug'          => false,            // OPTIONAL. You can use Client in debug mode 
    'logStoragePath' => '/../log/api.log' // OPTIONAL. You can specify the path of client logs
];
```

`apiKey`,`idPrefix`,`encryptionKey`,`encryptionIV` are directly furnished by TAUS.

For further config details please refer to the official documentation:

[TAUS Dynamic Quality Framework API](https://dqf-api.stag.taus.net/#/)

## Session Provider

Use `SessionProvider` to obtain the needed sessionId. You have three methods available:

*   `getById($externalReferenceId)` - get the sessionId by your external userId reference
*   `createByCredentials($externalReferenceId, $username, $password)` - get the sessionId by credentials. A `externalReferenceId` is needed to link your application's user and DQF user. Login is
 performed and user data is persisted 
*   `destroy($externalReferenceId)` - destroy the sessionId and performs logout

If you want to login as a generic user you can use:

*   `createAnonymous($email, $genericUsername, $genericPassword)` - get the sessionId by email and generic credentials. Login for generic user is performed and user data is persisted 
*   `getByGenericEmail($email)` - get the sessionId by credentials. Login is performed and user data is persisted 
*   `destroyAnonymous($email)` - destroy the sessionId and performs logout

You can choose between three different drivers:

* **In memory** (`InMemoryDqfUserRepository`) 
* **PDO** (`PDODqfUserRepository`)
* **Redis** (`RedisDqfUserRepository`)

Here is a full example implementing PDO driver:

```php
// ...

use Matecat\Dqf\Client;

$this->config = parse_ini_file(__DIR__ . '/../config/parameters.ini', true);
$client       = new Client([
        'apiKey'         => $this->config[ 'dqf' ][ 'API_KEY' ],
        'idPrefix'       => $this->config[ 'dqf' ][ 'ID_PREFIX' ],
        'encryptionKey'  => $this->config[ 'dqf' ][ 'ENCRYPTION_KEY' ],
        'encryptionIV'   => $this->config[ 'dqf' ][ 'ENCRYPTION_IV' ],
        'debug'          => true,
        'logStoragePath' => __DIR__ . '/../log/api.log'
]);

$pdo  = new \PDO("mysql:host=" . $this->config[ 'pdo' ][ 'SERVER' ] . ";dbname=" . $this->config[ 'pdo' ][ 'DBNAME' ], $this->config[ 'pdo' ][ 'USERNAME' ], $this->config[ 'pdo' ][ 'PASSWORD' ]);
$repo = new PDODqfUserRepository($pdo);

$sessionProvider = new SessionProvider($client, $repo);

// get sessionId by DQF credentials
$sessionId = $this->sessionProvider->createByCredentials($this->config[ 'dqf' ][ 'EXTERNAL_ID' ], $this->config[ 'dqf' ][ 'USERNAME' ], $this->config[ 'dqf' ][ 'PASSWORD' ]);

// get sessionId by your application's user id
$sessionId = $this->sessionProvider->getById($this->config[ 'dqf' ][ 'EXTERNAL_ID' ]);

```

Please note, that __**you need to run migrations**__ before using `PDODqfUserRepository`:

``````
vendor/bin/phinx migrate 
``````

As you can notice [Phinx](https://phinx.org/) library is used, but of course you can run the migrations (which can be found in `db/migrations` folder) with another tool or manually.

## Methods

Here is the list of Client's public methods:

| Command | Description |
| --- | --- |
| `addChildProjectTargetLanguage` | Add a target language for a file of a child project |
| `addCompleteTranslationOfASegment` | Add a complete translation of a segment |
| `addMasterProjectFile` | Add files to a master project |
| `addMasterProjectTargetLanguage` | Add source segments on a file of a project |
| `addProjectReviewCycle` | Convenient method to automatically create review children |
| `addProjectReviewSettings` | Add review preferences on a project |
| `addRemainingTargetSegmentsInBatch` | Add remaining target segments |
| `addReviewTemplate` | Add a review template |
| `addSourceSegmentsInBatchToMasterProject` | Add target languages for the translation of a file |
| `addTemplate` | Add project templates |
| `addTranslationOfASourceSegment` | Add the translation of a source segment |
| `addTranslationsForSourceSegmentsInBatch` | Add the translation of a source segment |
| `checkLanguageCode` | Check language code |
| `checkUserExistence` | Check the existence of a TAUS user |
| `createChildProject` | Add a new child Project to DQF |
| `createMasterProject` | Add a new master project to DQF |
| `deleteChildProject` | Delete an initialized child Project |
| `deleteChildProjectTargetLanguage` | Delete a target language of a child project's file |
| `deleteMasterProject` | Delete an initialized master Project |
| `deleteMasterProjectFile` | Delete a file of an initialized master Project |
| `deleteMasterProjectTargetLanguage` | Delete a target language of a master project |
| `deleteProjectReviewSettings` | Delete the review preferences of an initialized project |
| `deleteReviewTemplate` | Remove the review template of the user |
| `deleteTemplate` | Remove the project template of the user |
| `getBasicAttributesAggregate` | Return an aggregate of DQF basic attributes |
| `getChildProject` | Find the properties of a child Project |
| `getChildProjectFile` | Find the details of a file |
| `getChildProjectFiles` | Find the files of a child Project |
| `getChildProjectStatus` | Get the project status |
| `getFileId` | Return the DQF file id |
| `getChildProjectTargetLanguage` | Find a target language of a child project |
| `getChildProjectTargetLanguages` | Find the target languages of a child Project |
| `getMasterProject` | Find the properties of a master Project |
| `getMasterProjectFile` | Find a file of a master Project |
| `getMasterProjectFiles` | Find the files of a master Project |
| `getMasterProjectTargetLanguage` | Find a target language of a master project |
| `getMasterProjectTargetLanguages` | Find the target languages of a master Project |
| `getProjectId` | Return the DQF project id |
| `getProjectReviewCycle` | Get review children projects |
| `getProjectReviewSettings` | Return the review preferences of a child project |
| `getReviewTemplate` | Return the selected review template of the user |
| `getReviewTemplates` | Return the review templates of a user |
| `getSegmentId` | Return the DQF segment id |
| `getSourceSegmentIdsForAFile` | Get all the source segment ids of a file |
| `getTemplate` | Return the selected project template of the user |
| `getTemplates` | Return the project templates of the user |
| `getTranslationForASegment` | Get the translation of a source segment |
| `getTranslationId` | Return the DQF translation id |
| `getTranslationsForSourceSegmentsInBatch` | Get the multiple translation content |
| `getUser` | Get an existing TAUS user |
| `login` | Login to the DQF APIv3 service  |
| `logout` | Logout of the DQF APIv3 service |
| `updateChildProject` | Update the properties of a child project |
| `updateChildProjectStatus` | Update project status |
| `updateCompleteTranslatedSegment` | Update a complete translated segment |
| `updateMasterProject` | Update the master project |
| `updateMasterProjectFile` | Update the file of a master project |
| `updateProjectReviewSettings` | Update the project's review preferences |
| `updateReviewInBatch` | Add a review for a segment |
| `updateReviewTemplate` | Update a review template of the user |
| `updateTemplate` | Update a project template of the user |
| `updateTranslationForASegment` | Update the translation of a source segment |

Please note that __**every command takes an associative array of params as input**__. 

Every command validates input data against an array map of required/expected type values.

If validation fails, a `ParamsValidatorException` is raised and **the request is not sent** to DQF. You can use the provided `dqf:client:helper 
` (see below) to know the parameter(s) required for each method.

## Working with generic sessions

If you want to use generic sessions, provide `generic_email` to each command:

```php
// ...

$sessionProvider = new SessionProvider($client, $repo);
$sessionId = $sessionProvider->createAnonymous('email@example.org', 'DQF_GENERIC_USERNAME', 'DQF_GENERIC_PASSWORD');

$masterProject = $client->createMasterProject([
    'generic_email'      => 'email@example.org',
    'sessionId'          => $sessionId,
    'name'               => 'master-workflow-test',
    'sourceLanguageCode' => 'it-IT',
    'contentTypeId'      => 1,
    'industryId'         => 2,
    'processId'          => 1,
    'qualityLevelId'     => 1,
    'clientId'           => 'XXXXYYYY',
]);

```

Please note that the `generic_email` **MUST BE THE SAME** of the one passed to SessionProvider to obtain the `sessionId`. 

## Submitting data to DQF: a complete workflow

Here is a typical, complete and detailed workflow. These are the principal steps:
 
* creation of a master project;
* applying settings for master project;
* creation of a translation child;
* submitting of a translation in batch, and then editing a single segment translation;
* creation of a revision child;
* submitting a revision for a segment translation;
* deleting of projects.

```php
// ...

use Ramsey\Uuid\Uuid;

$sourceFile = $this->getSourceFile();       //  a fake array used as source file
$targetFile = $this->getTranslationFile();  //  a fake array used as target file

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
    'errorCategoryIds[0]' => 9,
    'errorCategoryIds[1]' => 10,
    'errorCategoryIds[2]' => 11,
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
    'errorCategoryIds[0]' => 9,
    'errorCategoryIds[1]' => 10,
    'errorCategoryIds[2]' => 11,
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
    'errorCategoryIds[0]' => 9,
    'errorCategoryIds[1]' => 10,
    'errorCategoryIds[2]' => 11,
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

## Commands

If you have an application which uses [Symfony Console](https://github.com/symfony/console), you have some commands available:

*  ``` dqf:client:helper```     displays the complete list of all client's available commands.

You can register the commands in your app, consider this example:

```php
#!/usr/bin/env php
<?php
set_time_limit(0);
require __DIR__.'/../vendor/autoload.php';

use Matecat\Dqf\Client;

$config = parse_ini_file(__DIR__ . '/../config/parameters.ini', true);
$client = new Client([
    'apiKey'         => $config[ 'dqf' ][ 'API_KEY' ],
    'idPrefix'       => $config[ 'dqf' ][ 'ID_PREFIX' ],
    'encryptionKey'  => $config[ 'dqf' ][ 'ENCRYPTION_KEY' ],
    'encryptionIV'   => $config[ 'dqf' ][ 'ENCRYPTION_IV' ],
    'debug'          => true,
    'logStoragePath' => __DIR__ . '/../log/api.log'
]);

// create symfony console app
$app = new \Symfony\Component\Console\Application('DQF Client', 'console tool');

// add commands here
$app->add(new \Matecat\Dqf\Console\ClientHelperCommand($client));
$app->run();
```

You get this output:

```
+-----------------------------------------+----------------------+---------+----------+
| updateChildProjectStatus                | generic_email        | string  | YES      |
|                                         | projectId            | string  | YES      |
|                                         | projectKey           | integer | YES      |
|                                         | sessionId            | string  | NO       |
|                                         | status               | string  | NO       |
+-----------------------------------------+----------------------+---------+----------+
```

## Support

If you found an issue or had an idea please refer [to this section](https://github.com/matecat/dqf-client/issues).

## Authors

* **Mauro Cassani** - [github](https://github.com/mauretto78)

## License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details
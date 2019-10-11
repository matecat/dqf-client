# Client

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

## Available methods

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
| `getProjectFileTargetLang` | Get declared files and target languages |
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
| `UpdateMasterProjectTargetLanguage` | Update the master project's target language |
| `updateProjectReviewSettings` | Update the project's review preferences |
| `updateReviewInBatch` | Add a review for a segment |
| `updateReviewTemplate` | Update a review template of the user |
| `updateTemplate` | Update a project template of the user |
| `updateTranslationForASegment` | Update the translation of a source segment |

Please note that __**every command takes an associative array of params as input**__. 

Every command validates input data against an array map of required/expected type values.

If validation fails, a `ParamsValidatorException` is raised and **the request is not sent** to DQF. You can use the provided `dqf:client:helper 
` (see below) to know the parameter(s) required for each method.

## Example



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
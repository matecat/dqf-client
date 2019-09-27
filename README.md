# DQF Client

**DQF Client** is a Client tailored for [DQF Tool by TAUS](https://www.taus.net/) easy integration into a PHP application.

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

Use `SessionProvider` to manage SessionId. You have three methods available:

*   `getById($externalReferenceId)` - get the SessionId by your external userId reference
*   `createByCredentials($externalReferenceId, $username, $password)` -  get the SessionId by credentials. Login is performed and user data is persisted 
*   `destroy($externalReferenceId)` - destroy the SessionId and performs logout

If you want to login as a generic user you can use:

*   `createAnonymous($email, $genericUsername, $genericPassword)` - get the SessionId by email and generic credentials. Login for generic user is performed and user data is persisted 
*   `getByGenericEmail($email)` -  get the SessionId by credentials. Login is performed and user data is persisted 
*   `destroyAnonymous($email)` - destroy the SessionId and performs logout

## Methods

Here is the list of Client's public methods:

* `addCompleteTranslationOfASegment` -  
* `addMasterProjectFile` -  
* `addMasterProjectReviewSettings` -  
* `addRemainingTargetSegmentsInBatch` -  
* `addSourceSegmentsInBatchToMasterProject` -  
* `addTargetLanguageToChildProject` -  
* `addTargetLanguageToMasterProject` -  
* `addTranslationOfASourceSegment` -  
* `addTranslationsForSourceSegmentsInBatch` -  
* `checkLanguageCode` -  
* `checkUserExistence` -  
* `createMasterProject` -  
* `createChildProject` -  
* `deleteChildProject` -  
* `deleteMasterProject` -  
* `deleteMasterProjectFile` -  
* `deleteMasterProjectReviewSettings` -  
* `deleteTargetLanguageForChildProject` -  
* `deleteTargetLanguageForMasterProject` -  
* `getBasicAttributesAggregate` -  
* `getChildProjectFile` -  
* `getChildProjectFiles` -  
* `getChildProjectStatus` -  
* `getFileId` -  
* `getProjectId` -  
* `getMasterProject` -  
* `getMasterProjectFile` -  
* `getMasterProjectReviewSettings` -  
* `getSegmentId` -  
* `getTargetLanguageForChildProject` -  
* `getTargetLanguageForMasterProject` -  
* `getTargetLanguageForChildProjectByLang` -  
* `getTargetLanguageForMasterProjectByLang` -  
* `getTranslationId` -  
* `getTranslationsForSourceSegmentsInBatch` -  
* `getUser` -  
* `login` -  
* `logout` -  
* `updateChildProject` -  
* `updateChildProjectStatus` -  
* `updateCompleteTranslatedSegment` - 
* `updateMasterProject` -  
* `updateMasterProjectFile` -  
* `updateMasterProjectReviewSettings` -  
* `updateSegmentTranslationReviewInChildProject` -  
* `updateTranslationForASegment` -  

## Input validation

The Client performs a validation when a command is invoked. Every command validates input data against an array map of required/expected type values.

If validation fails, a `ParamsValidatorException` is raised and **the request is not sent** to DQF.

## Submitting data to DQF: a complete workflow


## Support

If you found an issue or had an idea please refer [to this section](https://github.com/matecat/dqf-client/issues).

## Authors

* **Mauro Cassani** - [github](https://github.com/mauretto78)

## License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details
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

Use `SessionProvider` to manage SessionId. You have three methods available:

*   `getById($externalReferenceId)` | ... |get the SessionId by your external userId reference
*   `createByCredentials($externalReferenceId, $username, $password)` | ... | get the SessionId by credentials. A `externalReferenceId` is needed to link your application's user and DQF user. Login is
 performed and user data is persisted 
*   `destroy($externalReferenceId)` | ... |destroy the SessionId and performs logout

If you want to login as a generic user you can use:

*   `createAnonymous($email, $genericUsername, $genericPassword)` | ... |get the SessionId by email and generic credentials. Login for generic user is performed and user data is persisted 
*   `getByGenericEmail($email)` | ... | get the SessionId by credentials. Login is performed and user data is persisted 
*   `destroyAnonymous($email)` | ... |destroy the SessionId and performs logout

Here is a full example:

```php
// ...

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
| `addCompleteTranslationOfASegment` | Add a complete translation of a segment |
| `addMasterProjectFile` | Add files to a master project |
| `addProjectReviewCycle` | Convenient method to automatically create review children |
| `addProjectReviewSettings` | Add review preferences on a project |
| `addRemainingTargetSegmentsInBatch` | Add remaining target segments |
| `addReviewTemplate` | Add a review template |
| `addSourceSegmentsInBatchToMasterProject` | Add source segments on a file of a project |
| `addTargetLanguageToChildProject` | Add a target language for a file of a child project |
| `addTargetLanguageToMasterProject` | Add target languages for the translation of a file |
| `addTemplate` | Add project templates |
| `addTranslationOfASourceSegment` | Add the translation of a source segment |
| `addTranslationsForSourceSegmentsInBatch` | Add the translation of a source segment |
| `checkLanguageCode` | Check language code |
| `checkUserExistence` | Check the existence of a TAUS user |
| `createMasterProject` | Add a new master project to DQF |
| `createChildProject` | Add a new child Project to DQF |
| `deleteChildProject` | Delete an initialized child Project |
| `deleteMasterProject` | Delete an initialized master Project |
| `deleteMasterProjectFile` | Delete a file of an initialized master Project |
| `deleteProjectReviewSettings` | Delete the review preferences of an initialized project |
| `deleteReviewTemplate` | Remove the review template of the user |
| `deleteTargetLanguageForChildProject` | Delete a target language of a child project's file |
| `deleteTargetLanguageForMasterProject` | Delete a target language of a master project |
| `deleteTemplate` | Remove the project template of the user |
| `getBasicAttributesAggregate` | Return an aggregate of DQF basic attributes |
| `getChildProject` | Find the properties of a child Project |
| `getChildProjectFile` | Find the details of a file |
| `getChildProjectFiles` | Find the files of a child Project |
| `getChildProjectStatus` | Get the project status |
| `getFileId` | Return the DQF file id |
| `getMasterProject` | Find the properties of a master Project |
| `getMasterProjectFile` | Find a file of a master Project |
| `getMasterProjectFiles` | Find the files of a master Project |
| `getProjectReviewSettings` | Return the review preferences of a project |
| `getProjectId` | Return the DQF project id |
| `getProjectReviewCycle` | Get review children projects |
| `getReviewTemplate` | Return the selected review template of the user |
| `getReviewTemplates` | Return the review templates of a user |
| `getSegmentId` | Return the DQF segment id |
| `getSourceSegmentIdsForAFile` | Get all the source segment ids of a file |
| `getTargetLanguageForChildProject` | Find the target languages of a child Project |
| `getTargetLanguageForMasterProject` | Find the target languages of a master Project |
| `getTargetLanguageForChildProjectByLang` | Find a target language of a child project |
| `getTargetLanguageForMasterProjectByLang` | Find a target language of a master project |
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
| `updateReviewTemplate` | Update a review template of the user |
| `updateReviewInBatch` | Add a review for a segment |
| `updateTemplate` | Update a project template of the user |
| `updateTranslationForASegment` | Update the translation of a source segment |

Please note that __**every command takes an associative array of params as input**__. 

## Input validation

The Client performs a validation when a command is invoked. Every command validates input data against an array map of required/expected type values.

If validation fails, a `ParamsValidatorException` is raised and **the request is not sent** to DQF.

## Submitting data to DQF: a complete workflow

Here is a complete, detailed typical workflow.

## Support

If you found an issue or had an idea please refer [to this section](https://github.com/matecat/dqf-client/issues).

## Authors

* **Mauro Cassani** - [github](https://github.com/mauretto78)

## License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details
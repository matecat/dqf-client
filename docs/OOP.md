# OOP Abstraction Approach

If you prefer, you can choose to the OOP abstraction layer. You create and manipulate objects(entities), and then send them to DQF persistence.

The library comes with four Repositories, built on top of `Client` class:

* `Matecat\Dqf\Repository\Api\ChildProjectRepository` - performs CRUD operations on child projects;
* `Matecat\Dqf\Repository\Api\MasterProjectRepository` - performs CRUD operations on master projects;
* `Matecat\Dqf\Repository\Api\ReviewRepository` - performs review updates;
* `Matecat\Dqf\Repository\Api\TranslationRepository` - performs translation updates.

Take a look on what you can create a master project and then send it to DQF:

```php
//...
use Matecat\Dqf\Model\Entity\File;
use Matecat\Dqf\Model\Entity\MasterProject;
use Matecat\Dqf\Repository\Api\MasterProjectRepository;

// create a new MasterProject
$masterProject = new MasterProject('master-project-test', 'it-IT', 1, 2, 3, 1);

// add a file to project
$file = new File('test-file', 200);
$file->setClientId('YOUR-CLIENT-ID');
$masterProject->addFile($file);

// assoc targetLang to file
$masterProject->assocTargetLanguageToFile('en-US', $file);

// review settings
$reviewSettings = new ReviewSettings('combined');
$reviewSettings->setErrorCategoryIds0(1);
$reviewSettings->setErrorCategoryIds1(2);
$reviewSettings->setErrorCategoryIds2(3);
$reviewSettings->setSeverityWeights('[{"severityId":"1","weight":1}, {"severityId":"2","weight":2}, {"severityId":"3","weight":3}, {"severityId":"4","weight":4}]');
$reviewSettings->setPassFailThreshold(0.00);
$masterProject->setReviewSettings($reviewSettings);

// add source segments from a $sourceSegment array 
foreach ($sourceSegments as $sourceSegment) {
    $masterProject->addSourceSegment($sourceSegment);
}

// save project
$masterProjectRepo = new MasterProjectRepository($dqfClient, $sessionId);
$masterProjectRepo->save($masterProject);

```

In this example, a new `MasterProject` object is created; then were appended a file, a target lang, and finally data is sent to DQF using `save` method.

Take a look at this other example:

```php
//...
use Matecat\Dqf\Model\Entity\File;
use Matecat\Dqf\Model\Entity\MasterProject;
use Matecat\Dqf\Repository\Api\MasterProjectRepository;

// create a new MasterProject
$masterProject = new MasterProject('master-project-test', 'it-IT', 1, 2, 3, 1);

// save the project
$masterProjectRepo->save($masterProject);

// retrieve the project
$retrivedMasterProject = $masterProjectRepo->get($masterProject->getDqfId(), $masterProject->getDqfUuid());

// add a file to project
$file = new File('test-file', 200);
$file->setClientId('YOUR-CLIENT-ID');
$retrivedMasterProject->addFile($file);

// assoc targetLang to file
$retrivedMasterProject->assocTargetLanguageToFile('en-US', $file);

// review settings
$reviewSettings = new ReviewSettings('combined');
$reviewSettings->setErrorCategoryIds0(1);
$reviewSettings->setErrorCategoryIds1(2);
$reviewSettings->setErrorCategoryIds2(3);
$reviewSettings->setSeverityWeights('[{"severityId":"1","weight":1}, {"severityId":"2","weight":2}, {"severityId":"3","weight":3}, {"severityId":"4","weight":4}]');
$reviewSettings->setPassFailThreshold(0.00);
$retrivedMasterProject->setReviewSettings($reviewSettings);

// add source segments from a $sourceSegments array 
foreach ($sourceSegments as $sourceSegment) {
    $retrivedMasterProject->addSourceSegment($sourceSegment);
}

// update the project
$masterProjectRepo->update($retrivedMasterProject);

```

In this case, the project is firstly sent and saved on DQF; and THEN a file, a target language, a review setting and some source segments are set, and the project is finally updated on DQF by using
 the `update` method.
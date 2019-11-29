# Utils

## Data Encryptor

The class `DataEncryptor` encrypts/decrypts string (this class is already used to encrypt credentials while requesting login/logout to DQF). 

Please note that in order to instantiate this class you have to provide your ENCRYPTION_KEY and ENCRYPTION_IV furnished by DQF:

```php
//...
use Matecat\Dqf\Utils\DataEncryptor;

$dataEncryptor = new DataEncryptor('ENCRYPTION_KEY', 'ENCRYPTION_IV');

$string = 'email@domain.com';
$encrypted = $dataEncryptor->encrypt($string);
$decrypted = $dataEncryptor->decrypt($encrypted);

```

## Analysers

### RevisionCorrectionAnalyser

The class `RevisionCorrectionAnalyser` compares two strings and returns the differences as an array. 

Please note that the word comparison is done by exploding words by **standard space separator** (' ').

Look at the example below:

```php
//...
use Matecat\Dqf\Utils\Analysers\RevisionCorrectionAnalyser;

$old = 'Test Segment';
$new = 'Some in Test and also added.';

// 
// $analysized will be equals to:
//
// [
//     'Some in'         => 'added',
//     'Test'            => 'unchanged',
//     'Segment'         => 'deleted',
//     'and also added.' => 'added',
// ]
//
$analysized = RevisionCorrectionAnalyser::analyse($old, $new);

```

You can use this class to easily add `RevisionCorrectionItem` to `RevisionCorrection`:

```php
//..

$correction = new RevisionCorrection( 'Some in Test and also added.', 10000 );
$corrections = RevisionCorrectionAnalyser::analyse('Test Segment', 'Some in Test and also added.');

foreach ($corrections as $key => $value){
    $correction->addItem( new RevisionCorrectionItem( $key, $value ) );
}

```

### MatecatSegmentOriginAnalyser 

This analyser is tailored for Matecat segment translation structure.

You can create your own with your business logic, just be sure to implement `SegmentOriginAnalyserInterface`.

Take a look on the example below:

```php
//..

use Matecat\Dqf\Utils\Analysers\MatecatSegmentOriginAnalyser;

$sample_row = [
    'autopropagated_from' => null,
    'suggestions_array' => '[{"id":"0","segment":"Your home is your sanctuary from the world \u2014 or it should be.","translation":"Dit hjem er dit fristed fra verden - eller det skal v\u00e6re.","raw_translation":"Dit hjem er dit fristed fra verden - eller det skal v\u00e6re.","quality":"70","reference":"Machine Translation provided by Google, Microsoft, Worldlingo or MyMemory customized engine.","usage_count":1,"subject":"All","created_by":"MT!","last_updated_by":"MT!","create_date":"2013-02-25","last_update_date":"2013-02-25","match":"85%"},{"id":"413721806","segment":"where is your photo","translation":"hvor er dit foto","raw_translation":"hvor er dit foto","quality":"74","reference":"","usage_count":1,"subject":"General","created_by":"Anonymous","last_updated_by":"anonymous","create_date":"2010-06-18 20:09:53","last_update_date":"2010-06-18","match":"17%"},{"id":"424290266","segment":"\ufeffIt is based on the","translation":"farve","raw_translation":"farve","quality":"74","reference":"","usage_count":1,"subject":"General","created_by":"Anonymous","last_updated_by":"anonymous","create_date":"2011-10-04 20:23:40","last_update_date":"2011-10-04","match":"15%"},{"id":"427707852","segment":"\ufeffThis is the best part of verdensl\u00e5","translation":"elsker dig","raw_translation":"elsker dig","quality":"74","reference":"","usage_count":1,"subject":"General","created_by":"Anonymous","last_updated_by":"anonymous","create_date":"2012-03-15 10:09:30","last_update_date":"2012-03-15","match":"14%"},{"id":"432840673","segment":"Printed from the Civil Registration System","translation":"Udskrift fra Det Centrale Person Register","raw_translation":"Udskrift fra Det Centrale Person Register","quality":"74","reference":"","usage_count":1,"subject":"All","created_by":"Anonymous","last_updated_by":"anonymous","create_date":"2012-12-19 16:49:09","last_update_date":"2012-12-19","match":"13%"},{"id":"248656188","segment":"The amounts receivable can be collected as they fall due","translation":"Fordringerne kan inddrives efterh\u00e5nden som de forfalder","raw_translation":"Fordringerne kan inddrives efterh\u00e5nden som de forfalder","quality":"74","reference":"","usage_count":1,"subject":"Legal_and_Notarial","created_by":"Anonymous","last_updated_by":"anonymous","create_date":"2009-11-23 00:00:51","last_update_date":"2009-11-23","match":"12%"},{"id":"424290514","segment":"your","translation":"\ufefflips","raw_translation":"\ufefflips","quality":"74","reference":"","usage_count":1,"subject":"General","created_by":"Anonymous","last_updated_by":"anonymous","create_date":"2011-12-21 10:31:53","last_update_date":"2011-12-21","match":"11%"},{"id":"427707572","segment":"This compromise is welcome but it is not enough;","translation":"Det er et udm\u00e6rket kompromis, som vi gl\u00e6der os over, men det er ikke tilstr\u00e6kkeligt.","raw_translation":"Det er et udm\u00e6rket kompromis, som vi gl\u00e6der os over, men det er ikke tilstr\u00e6kkeligt.","quality":"74","reference":"","usage_count":1,"subject":"General","created_by":"Anonymous","last_updated_by":"anonymous","create_date":"2012-03-01 12:56:17","last_update_date":"2012-03-01","match":"10%"}]',
    'match_type' => 'MT',
    'suggestion_match' => '85',
    'suggestion_position' => null,
    'suggestion' => 'Dit hjem er dit fristed fra verden - eller det skal være.',
    'translation' => 'Dit hjem er dit fristed fra verden - eller det burde det være.',
];

// 
// $analysed will be equals to:
//
// [
//     'segment_origin'   => 'MT',
//     'suggestion_match' => null,
// ]
//
$analysed = MatecatSegmentOriginAnalyser::analyse($sample_row);

```

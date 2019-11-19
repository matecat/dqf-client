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

## RevisionCorrection Analyser

The class `RevisionCorrectionAnalyser` compares two strings and returns the differences as an array. 

Please note that the word comparison is done by exploding words by **standard space separator** (' ').

Look at the example below:

```php
//...
use Matecat\Dqf\Utils\RevisionCorrectionAnalyser;

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
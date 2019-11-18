# Utils

## RevisionCorrection Analyser

The class `RevisionCorrectionAnalyser` compares two strings and returns the differences as an array. Look at the example below:

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

# Commands

If you have an application which uses [Symfony Console](https://github.com/symfony/console), you have some commands available:

*  ``` dqf:cache:refresh```     refresh the local cache for basic attributes.
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
$app->add(new \Matecat\Dqf\Console\CacheRefreshCommand($client));
$app->run();
```

## Cache refresh

A positive message is displayed if the operation is successful.

## Client helper

This is the output that you get:

```
+--------------------------+---------------+---------+----------+
| method                   | parameter(s)  | type    | required |
+--------------------------+---------------+---------+----------+
| updateChildProjectStatus | generic_email | string  | NO       |
|                          | projectId     | integer | YES      |
|                          | projectKey    | string  | YES      |
|                          | sessionId     | string  | YES      |
|                          | status        | string  | NO       |
+--------------------------+---------------+---------+----------+

```
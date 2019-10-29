# Session Provider

Use `SessionProvider` to obtain the needed sessionId. 

You can perform a login and get a sessionId by using `create()` method. This function accepts an associative array of parameters:

* `externalReferenceId` (OPTIONAL) - the user ID in your application
* `username` (REQUIRED) - DQF username
* `password` (REQUIRED) - DQF password
* `isGeneric` (OPTIONAL) - specify if the login is anonymous
* `genericEmail` (OPTIONAL) - the generic email . Mandatary if `isGeneric` is set to true

You have also some other methods available:

* `getByGenericEmail($email)` - get the sessionId by credentials. Login is performed and user data is persisted 
* `getById($externalReferenceId)` - get the sessionId by your external userId reference
* `destroy($externalReferenceId)` - destroy the sessionId and performs logout
* `destroyAnonymous($email)` - destroy the sessionId and performs logout

## Drivers

You can choose between three different drivers:

* **In memory** (`InMemoryDqfUserRepository`) 
* **PDO** (`PDODqfUserRepository`)
* **Redis** (`RedisDqfUserRepository`)

## Example

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
$sessionId = $this->sessionProvider->create([
    'externalReferenceId' => $this->config[ 'dqf' ][ 'EXTERNAL_ID' ],
    'username'            => $this->config[ 'dqf' ][ 'USERNAME' ],
    'password'            => $this->config[ 'dqf' ][ 'PASSWORD' ],
]);

// get sessionId by your application's user id
$sessionId = $this->sessionProvider->getById($this->config[ 'dqf' ][ 'EXTERNAL_ID' ]);

```

## Note

Please note, that __**you need to run migrations**__ before using `PDODqfUserRepository`:

``````
vendor/bin/phinx migrate 
``````

As you can notice [Phinx](https://phinx.org/) library is used, but of course you can run the migrations (which can be found in `db/migrations` folder) with another tool or manually.

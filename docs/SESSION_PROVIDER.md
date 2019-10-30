# Session Provider

Use `SessionProvider` to obtain the needed sessionId. 

You can login and get a valid sessionId by using `create()` method. This function accepts an associative array of parameters:

* `externalReferenceId` (OPTIONAL) - the user ID in your application
* `username`            (REQUIRED) - DQF username
* `password`            (REQUIRED) - DQF password
* `isGeneric`           (OPTIONAL) - specify if the login is anonymous
* `genericEmail`        (OPTIONAL) - the generic email . Mandatary if `isGeneric` is set to true

You have also some other methods available:

* `destroy($externalReferenceId)` - destroy the sessionId and does logout by generic email
* `destroyAnonymous($email)`      - destroy the sessionId and does logout by external user ID reference
* `hasGenericEmail($email)`       - check if a user had been persisted by generic email
* `hasId($externalReferenceId)`   - check if a user had been persisted by external user ID reference
* `getByGenericEmail($email)`     - get the sessionId by generic email. Login is performed and user data is persisted 
* `getById($externalReferenceId)` - get the sessionId by external user ID reference

## Drivers

You can choose between three different drivers:

* **In memory** (`InMemoryDqfUserRepository`) 
* **PDO** (`PDODqfUserRepository`)
* **Redis** (`RedisDqfUserRepository`)

## Example

Here is an extract taken from a unit test (implementing PDO driver):

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
$sessionId = $sessionProvider->create([
    'externalReferenceId' => $this->config[ 'dqf' ][ 'EXTERNAL_ID' ],
    'username'            => $this->config[ 'dqf' ][ 'USERNAME' ],
    'password'            => $this->config[ 'dqf' ][ 'PASSWORD' ],
]);

// now you can get sessionId by your application's user ID
$sessionId = $sessionProvider->getById($this->config[ 'dqf' ][ 'EXTERNAL_ID' ]);

// that's return true
$exists = $sessionProvider->hasId($this->config[ 'dqf' ][ 'EXTERNAL_ID' ]);

// destroy the DQF user and logout from DQF
$destroy = $sessionProvider->destroy($this->config[ 'dqf' ][ 'EXTERNAL_ID' ]);

```

## Note

Please note, that __**you need to run migrations**__ before using `PDODqfUserRepository`:

``````
vendor/bin/phinx migrate 
``````

As you can notice [Phinx](https://phinx.org/) library is used, but of course you can run the migrations (which can be found in `db/migrations` folder) with another tool or manually.

# Session Provider

Use `SessionProvider` to obtain the needed sessionId. You have three methods available:

*   `getById($externalReferenceId)` - get the sessionId by your external userId reference
*   `createByCredentials($externalReferenceId, $username, $password)` - get the sessionId by credentials. A `externalReferenceId` is needed to link your application's user and DQF user. Login is
 performed and user data is persisted 
*   `destroy($externalReferenceId)` - destroy the sessionId and performs logout

If you want to login as a generic user you can use:

*   `createAnonymous($email, $genericUsername, $genericPassword)` - get the sessionId by email and generic credentials. Login for generic user is performed and user data is persisted 
*   `getByGenericEmail($email)` - get the sessionId by credentials. Login is performed and user data is persisted 
*   `destroyAnonymous($email)` - destroy the sessionId and performs logout

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
$sessionId = $this->sessionProvider->createByCredentials($this->config[ 'dqf' ][ 'EXTERNAL_ID' ], $this->config[ 'dqf' ][ 'USERNAME' ], $this->config[ 'dqf' ][ 'PASSWORD' ]);

// get sessionId by your application's user id
$sessionId = $this->sessionProvider->getById($this->config[ 'dqf' ][ 'EXTERNAL_ID' ]);

```

## Note

Please note, that __**you need to run migrations**__ before using `PDODqfUserRepository`:

``````
vendor/bin/phinx migrate 
``````

As you can notice [Phinx](https://phinx.org/) library is used, but of course you can run the migrations (which can be found in `db/migrations` folder) with another tool or manually.

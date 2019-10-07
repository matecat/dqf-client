<?php

namespace Matecat\Dqf\Tests\SessionProvider;

use Faker\Factory;
use Matecat\Dqf\Client;
use Matecat\Dqf\Repository\Persistence\InMemoryDqfUserRepository;
use Matecat\Dqf\SessionProvider;
use Matecat\Dqf\Tests\BaseTest;

class AnonymousSessionProviderInMemoryTest extends BaseTest
{
    /**
     * @var SessionProvider
     */
    protected $sessionProvider;

    protected function setUp()
    {
        parent::setUp();

        $this->config = parse_ini_file(__DIR__ . '/../../config/parameters.ini', true);
        $client       = new Client([
                'apiKey'         => $this->config[ 'dqf' ][ 'API_KEY' ],
                'idPrefix'       => $this->config[ 'dqf' ][ 'ID_PREFIX' ],
                'encryptionKey'  => $this->config[ 'dqf' ][ 'ENCRYPTION_KEY' ],
                'encryptionIV'   => $this->config[ 'dqf' ][ 'ENCRYPTION_IV' ],
                'debug'          => true,
                'logStoragePath' => __DIR__ . '/../../log/api.log'
        ]);

        $repo = new InMemoryDqfUserRepository();

        $this->sessionProvider = new SessionProvider($client, $repo);
    }

    /**
     * @throws \Exception
     * @test
     */
    public function test_20_anonymous_sessions_are_persisted_and_then_removed()
    {
        $faker = Factory::create();

        for ($i=0;$i<20;$i++) {
            $genericEmail = $faker->email;
            $genericSessionId = $this->sessionProvider->createAnonymous($genericEmail, $this->config[ 'dqf' ][ 'DQF_GENERIC_USERNAME' ], $this->config[ 'dqf' ][ 'DQF_GENERIC_PASSWORD' ]);

            $this->assertEquals($genericSessionId, $this->sessionProvider->getByGenericEmail($genericEmail));

            $this->sessionProvider->destroyAnonymous($genericEmail);

            try {
                $this->sessionProvider->getByGenericEmail($genericEmail);
            } catch (\Exception $e) {
                $this->assertEquals($e->getMessage(), "Generic user with email {$genericEmail} does not exists");
            }
        }
    }
}

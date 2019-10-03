<?php

namespace Matecat\Dqf\Tests;

use Faker\Factory;

class AnonymousSessionProviderWithPDOTest extends AbstractClientTest
{
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

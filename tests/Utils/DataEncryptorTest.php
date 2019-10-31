<?php

namespace Matecat\Dqf\Tests\SessionProvider;

use Matecat\Dqf\Client;
use Matecat\Dqf\Repository\Persistence\InMemoryDqfUserRepository;
use Matecat\Dqf\SessionProvider;
use Matecat\Dqf\Utils\DataEncryptor;

class DataEncryptorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function encrypt_and_decrypt()
    {
        $params = parse_ini_file(__DIR__.'/../../config/parameters.ini', true);

        $dataEncryptor = new DataEncryptor($params['dqf']['ENCRYPTION_KEY'], $params['dqf']['ENCRYPTION_IV']);

        $string = 'luca.defranceschi@translated.net';
        $encrypted = $dataEncryptor->encrypt($string);
        $decrypted = $dataEncryptor->decrypt($encrypted);

        $this->assertEquals($decrypted, $string);
    }
}
<?php

namespace Matecat\Dqf\Commands\Handlers;

use Matecat\Dqf\Commands\CommandHandler;
use Matecat\Dqf\Utils\DataEncryptor;
use Teapot\StatusCode;

class Login extends CommandHandler
{
    protected $required = [
            'email',
            'password',
    ];

    /**
     * @param array $params
     *
     * @return mixed|void
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function handle($params = [])
    {
        $dataEncryptor = new DataEncryptor($this->params[ 'encryptionKey' ], $this->params[ 'encryptionIV' ]);
        $response      = $this->httpClient->request('POST', $this->buildUri('login'), [
                'form_params' => [
                        'email'    => $dataEncryptor->encrypt($params[ 'email' ]),
                        'password' => $dataEncryptor->encrypt($params[ 'password' ]),
                ]
        ]);

        if ($response->getStatusCode() === StatusCode::OK) {
            return $this->decodeResponse($response)->loginResponse;
        }
    }
}

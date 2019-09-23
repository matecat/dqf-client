<?php

namespace Matecat\Dqf\Commands\Handlers;

use Matecat\Dqf\Commands\CommandHandler;
use Matecat\Dqf\Utils\DataEncryptor;
use Teapot\StatusCode;

class Logout extends CommandHandler
{
    protected $required = [
            'email',
            'sessionId',
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
        $response      = $this->httpClient->request('POST', $this->buildUri('logout'), [
                'headers'     => [
                        'sessionId' => $params[ 'sessionId' ],
                ],
                'form_params' => [
                        'email' => $dataEncryptor->encrypt($params[ 'email' ]),
                ]
        ]);

        if ($response->getStatusCode() === StatusCode::OK) {
            return $this->decodeResponse($response);
        }
    }
}

<?php

namespace Matecat\Dqf\Commands\Handlers;

use Matecat\Dqf\Commands\CommandHandler;
use Matecat\Dqf\Constants;
use Matecat\Dqf\Utils\DataEncryptor;
use Teapot\StatusCode;

class Login extends CommandHandler
{
    protected function setRules() {
        $rules = [
                'username' => [
                        'required' => true,
                        'type'     => Constants::DATA_TYPE_STRING,
                ],
                'password' => [
                        'required' => true,
                        'type'     => Constants::DATA_TYPE_STRING,
                ],
        ];

        $this->rules = $rules;
    }

    /**
     * @param array $params
     *
     * @return mixed|void
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function handle($params = [])
    {
        $dataEncryptor = new DataEncryptor($this->params[ 'encryptionKey' ], $this->params[ 'encryptionIV' ]);
        $response      = $this->httpClient->request(Constants::HTTP_VERBS_CREATE, $this->buildUri('login'), [
                'headers'     => [
                        'email' => isset($params[ 'generic_email' ]) ? $params[ 'generic_email' ] : null,
                ],
                'form_params' => [
                        'email'    => $dataEncryptor->encrypt($params[ 'username' ]),
                        'password' => $dataEncryptor->encrypt($params[ 'password' ]),
                ]
        ]);

        if ($response->getStatusCode() === StatusCode::OK) {
            return $this->decodeResponse($response)->loginResponse;
        }
    }
}

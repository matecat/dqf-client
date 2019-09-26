<?php

namespace Matecat\Dqf\Commands\Handlers;

use Matecat\Dqf\Commands\CommandHandler;
use Matecat\Dqf\Utils\ParamsValidator;
use Teapot\StatusCode;

class AddTargetLanguageToChildProject extends CommandHandler
{
    protected $rules = [
            'sessionId'          => [
                    'required' => true,
                    'type'     => ParamsValidator::DATA_TYPE_STRING,
            ],
            'projectKey'         => [
                    'required' => true,
                    'type'     => ParamsValidator::DATA_TYPE_STRING,
            ],
            'projectId'          => [
                    'required' => true,
                    'type'     => ParamsValidator::DATA_TYPE_INTEGER,
            ],
            'fileId'             => [
                    'required' => true,
                    'type'     => ParamsValidator::DATA_TYPE_INTEGER,
            ],
            'targetLanguageCode' => [
                    'required' => true,
                    'type'     => ParamsValidator::DATA_TYPE_STRING,
            ],
    ];

    /**
     * @param array $params
     *
     * @return mixed|void
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function handle($params = [])
    {
        $response = $this->httpClient->request('POST', $this->buildUri(
            'project/child/{projectId}/file/{fileId}/targetLang',
            [
                        'projectId' => $params[ 'projectId' ],
                        'fileId'    => $params[ 'fileId' ],
                ]
        ), [
                'headers'     => [
                        'projectKey' => $params[ 'projectKey' ],
                        'sessionId'  => $params[ 'sessionId' ],
                        'email'      => isset($params[ 'generic_email' ]) ? $params[ 'generic_email' ] : null,
                ],
                'form_params' => [
                        'targetLanguageCode' => $params[ 'body' ]
                ],
        ]);

        if ($response->getStatusCode() === StatusCode::CREATED) {
            return $this->decodeResponse($response);
        }
    }
}

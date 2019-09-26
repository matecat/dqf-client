<?php

namespace Matecat\Dqf\Commands\Handlers;

use Matecat\Dqf\Commands\CommandHandler;
use Matecat\Dqf\Constants;
use Teapot\StatusCode;

class AddTargetLanguageToMasterProject extends CommandHandler
{
    protected $rules = [
            'sessionId'          => [
                    'required' => true,
                    'type'     => Constants::DATA_TYPE_STRING,
            ],
            'projectKey'         => [
                    'required' => true,
                    'type'     => Constants::DATA_TYPE_STRING,
            ],
            'projectId'          => [
                    'required' => true,
                    'type'     => Constants::DATA_TYPE_INTEGER,
            ],
            'fileId'             => [
                    'required' => true,
                    'type'     => Constants::DATA_TYPE_INTEGER,
            ],
            'targetLanguageCode' => [
                    'required' => true,
                    'type'     => Constants::DATA_TYPE_STRING,
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
        $response = $this->httpClient->request(Constants::HTTP_VERBS_CREATE, $this->buildUri(
            'project/master/{projectId}/file/{fileId}/targetLang',
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

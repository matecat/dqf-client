<?php

namespace Matecat\Dqf\Commands\Handlers;

use Matecat\Dqf\Commands\CommandHandler;
use Matecat\Dqf\Utils\ParamsValidator;
use Teapot\StatusCode;

class AddMasterProjectFile extends CommandHandler
{
    protected $rules = [
            'sessionId'        => [
                    'required' => true,
                    'type'     => ParamsValidator::DATA_TYPE_STRING,
            ],
            'projectKey'       => [
                    'required' => true,
                    'type'     => ParamsValidator::DATA_TYPE_STRING,
            ],
            'projectId'        => [
                    'required' => true,
                    'type'     => ParamsValidator::DATA_TYPE_INTEGER,
            ],
            'name'             => [
                    'required' => true,
                    'type'     => ParamsValidator::DATA_TYPE_STRING,
            ],
            'numberOfSegments' => [
                    'required' => true,
                    'type'     => ParamsValidator::DATA_TYPE_INTEGER,
            ],
            'clientId'         => [
                    'required' => false,
                    'type'     => ParamsValidator::DATA_TYPE_STRING,
            ],
            'tmsFileId'        => [
                    'required' => false,
                    'type'     => ParamsValidator::DATA_TYPE_INTEGER,
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
        $response = $this->httpClient->request('POST', $this->buildUri('project/master/{projectId}/file', [ 'projectId' => $params[ 'projectId' ] ]), [
                'headers'     => [
                        'projectKey' => $params[ 'projectKey' ],
                        'sessionId'  => $params[ 'sessionId' ],
                        'email'      => isset($params[ 'generic_email' ]) ? $params[ 'generic_email' ] : null,
                ],
                'form_params' => [
                        'name'             => $params[ 'name' ],
                        'numberOfSegments' => $params[ 'numberOfSegments' ],
                        'clientId'         => isset($params[ 'clientId' ]) ? $params[ 'clientId' ] : null,
                        'tmsFileId'        => isset($params[ 'tmsFileId' ]) ? $params[ 'tmsFileId' ] : null,
                ],
        ]);

        if ($response->getStatusCode() === StatusCode::CREATED) {
            return $this->decodeResponse($response);
        }
    }
}

<?php

namespace Matecat\Dqf\Commands\Handlers;

use Matecat\Dqf\Commands\CommandHandler;
use Matecat\Dqf\Constants;
use Teapot\StatusCode;

class UpdateMasterProjectFile extends CommandHandler
{
    protected $rules = [
            'sessionId'        => [
                    'required' => true,
                    'type'     => Constants::DATA_TYPE_STRING,
            ],
            'projectKey'       => [
                    'required' => true,
                    'type'     => Constants::DATA_TYPE_STRING,
            ],
            'projectId'        => [
                    'required' => true,
                    'type'     => Constants::DATA_TYPE_INTEGER,
            ],
            'fileId'           => [
                    'required' => true,
                    'type'     => Constants::DATA_TYPE_INTEGER,
            ],
            'name'             => [
                    'required' => false,
                    'type'     => Constants::DATA_TYPE_STRING,
            ],
            'numberOfSegments' => [
                    'required' => false,
                    'type'     => Constants::DATA_TYPE_INTEGER,
            ],
            'clientId'         => [
                    'required' => false,
                    'type'     => Constants::DATA_TYPE_STRING,
            ],
            'tmsFileId'        => [
                    'required' => false,
                    'type'     => Constants::DATA_TYPE_INTEGER,
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
        $response = $this->httpClient->request(Constants::HTTP_VERBS_UPDATE, $this->buildUri('project/master/{projectId}/file/{fileId}', [
                'projectId' => $params[ 'projectId' ],
                'fileId'    => $params[ 'fileId' ],
        ]), [
                'headers'     => [
                        'projectKey' => $params[ 'projectKey' ],
                        'sessionId'  => $params[ 'sessionId' ],
                        'email'      => isset($params[ 'generic_email' ]) ? $params[ 'generic_email' ] : null,
                ],
                'form_params' => [
                        'name'             => isset($params[ 'name' ]) ? $params[ 'name' ] : null,
                        'numberOfSegments' => isset($params[ 'numberOfSegments' ]) ? $params[ 'numberOfSegments' ] : null,
                        'clientId'         => isset($params[ 'clientId' ]) ? $params[ 'clientId' ] : null,
                        'tmsFileId'        => isset($params[ 'tmsFileId' ]) ? $params[ 'tmsFileId' ] : null,
                ],
        ]);

        if ($response->getStatusCode() === StatusCode::CREATED) {
            return $this->decodeResponse($response);
        }
    }
}

<?php

namespace Matecat\Dqf\Commands\Handlers;

use Matecat\Dqf\Commands\CommandHandler;
use Matecat\Dqf\Constants;
use Teapot\StatusCode;

class AddMasterProjectFile extends CommandHandler
{
    protected function setRules() {
        $rules = [
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
                'name'             => [
                        'required' => true,
                        'type'     => Constants::DATA_TYPE_STRING,
                ],
                'numberOfSegments' => [
                        'required' => true,
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
        $response = $this->httpClient->request(Constants::HTTP_VERBS_CREATE, $this->buildUri('project/master/{projectId}/file', [ 'projectId' => $params[ 'projectId' ] ]), [
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

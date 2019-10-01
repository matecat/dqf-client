<?php

namespace Matecat\Dqf\Commands\Handlers;

use Matecat\Dqf\Commands\CommandHandler;
use Matecat\Dqf\Constants;
use Teapot\StatusCode;

class GetMasterProjectFile extends CommandHandler
{
    protected function setRules()
    {
        $rules = [
                'sessionId'  => [
                        'required' => true,
                        'type'     => Constants::DATA_TYPE_STRING,
                ],
                'projectKey' => [
                        'required' => true,
                        'type'     => Constants::DATA_TYPE_STRING,
                ],
                'projectId'  => [
                        'required' => true,
                        'type'     => Constants::DATA_TYPE_INTEGER,
                ],
                'fileId'     => [
                        'required' => true,
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
        $response = $this->httpClient->request(Constants::HTTP_VERBS_GET, $this->buildUri('project/master/{projectId}/file/{fileId}', [
                'projectId' => $params[ 'projectId' ],
                'fileId'    => $params[ 'fileId' ],
        ]), [
                'headers' => [
                        'sessionId'  => $params[ 'sessionId' ],
                        'projectKey' => $params[ 'projectKey' ],
                        'email'      => isset($params[ 'generic_email' ]) ? $params[ 'generic_email' ] : null,
                ],
        ]);

        if ($response->getStatusCode() === StatusCode::OK) {
            return $this->decodeResponse($response);
        }
    }
}

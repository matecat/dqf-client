<?php

namespace Matecat\Dqf\Commands\Handlers;

use Matecat\Dqf\Commands\CommandHandler;
use Matecat\Dqf\Constants;
use Teapot\StatusCode;

class AddSourceSegmentsInBatchToMasterProject extends CommandHandler
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
                'body'       => [
                        'required' => true,
                        'type'     => Constants::DATA_TYPE_ARRAY,
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
        $response = $this->httpClient->request(Constants::HTTP_VERBS_CREATE, $this->buildUri(
            'project/master/{projectId}/file/{fileId}/sourceSegment/batch',
            [
                        'projectId' => $params[ 'projectId' ],
                        'fileId'    => $params[ 'fileId' ],
                ]
        ), [
                'headers' => [
                        'Content-Type'   => 'application/json',
                        'Content-Length' => strlen(json_encode($params[ 'body' ])),
                        'projectKey'     => $params[ 'projectKey' ],
                        'sessionId'      => $params[ 'sessionId' ],
                        'email'          => isset($params[ 'generic_email' ]) ? $params[ 'generic_email' ] : null,
                ],
                'json'    => [
                        'sourceSegments' => $params[ 'body' ]
                ],
        ]);

        if ($response->getStatusCode() === StatusCode::CREATED) {
            return $this->decodeResponse($response);
        }
    }
}

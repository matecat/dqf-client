<?php

namespace Matecat\Dqf\Commands\Handlers;

use Matecat\Dqf\Commands\CommandHandler;
use Matecat\Dqf\Constants;
use Teapot\StatusCode;

class UpdateMasterProject extends CommandHandler
{
    protected function setRules()
    {
        $rules = [
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
                'name'               => [
                        'required' => false,
                        'type'     => Constants::DATA_TYPE_STRING,
                ],
                'sourceLanguageCode' => [
                        'required' => false,
                        'type'     => Constants::DATA_TYPE_STRING,
                ],
                'contentTypeId'      => [
                        'required' => false,
                        'type'     => Constants::DATA_TYPE_INTEGER,
                ],
                'industryId'         => [
                        'required' => false,
                        'type'     => Constants::DATA_TYPE_INTEGER,
                ],
                'processId'          => [
                        'required' => false,
                        'type'     => Constants::DATA_TYPE_INTEGER,
                ],
                'qualityLevelId'     => [
                        'required' => false,
                        'type'     => Constants::DATA_TYPE_INTEGER,
                ],
                'clientId'           => [
                        'required' => false,
                        'type'     => Constants::DATA_TYPE_STRING,
                ],
                'templateName'       => [
                        'required' => false,
                        'type'     => Constants::DATA_TYPE_STRING,
                ],
                'tmsProjectKey'      => [
                        'required' => false,
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
        $response = $this->httpClient->request(Constants::HTTP_VERBS_UPDATE, $this->buildUri('project/master/{projectId}', [ 'projectId' => $params[ 'projectId' ] ]), [
                'headers'     => [
                        'projectKey' => $params[ 'projectKey' ],
                        'sessionId'  => $params[ 'sessionId' ],
                        'email'      => isset($params[ 'generic_email' ]) ? $params[ 'generic_email' ] : null,
                ],
                'form_params' => [
                        'name'               => isset($params[ 'name' ]) ? $params[ 'name' ] : null,
                        'sourceLanguageCode' => isset($params[ 'sourceLanguageCode' ]) ? $params[ 'sourceLanguageCode' ] : null,
                        'contentTypeId'      => isset($params[ 'contentTypeId' ]) ? $params[ 'contentTypeId' ] : null,
                        'industryId'         => isset($params[ 'industryId' ]) ? $params[ 'industryId' ] : null,
                        'processId'          => isset($params[ 'processId' ]) ? $params[ 'processId' ] : null,
                        'qualityLevelId'     => isset($params[ 'qualityLevelId' ]) ? $params[ 'qualityLevelId' ] : null,
                        'clientId'           => isset($params[ 'clientId' ]) ? $params[ 'clientId' ] : null,
                        'templateName'       => isset($params[ 'templateName' ]) ? $params[ 'templateName' ] : null,
                        'tmsProjectKey'      => isset($params[ 'tmsProjectKey' ]) ? $params[ 'tmsProjectKey' ] : null,
                ]
        ]);

        if ($response->getStatusCode() === StatusCode::OK) {
            return $this->decodeResponse($response);
        }
    }
}

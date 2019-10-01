<?php

namespace Matecat\Dqf\Commands\Handlers;

use Matecat\Dqf\Commands\CommandHandler;
use Matecat\Dqf\Constants;
use Teapot\StatusCode;

class CreateMasterProject extends CommandHandler
{
    protected function setRules()
    {
        $rules = [
                'sessionId'          => [
                        'required' => true,
                        'type'     => Constants::DATA_TYPE_STRING,
                ],
                'name'               => [
                        'required' => true,
                        'type'     => Constants::DATA_TYPE_STRING,
                ],
                'sourceLanguageCode' => [
                        'required' => true,
                        'type'     => Constants::DATA_TYPE_STRING,
                ],
                'contentTypeId'      => [
                        'required' => true,
                        'type'     => Constants::DATA_TYPE_INTEGER,
                ],
                'industryId'         => [
                        'required' => true,
                        'type'     => Constants::DATA_TYPE_INTEGER,
                ],
                'processId'          => [
                        'required' => true,
                        'type'     => Constants::DATA_TYPE_INTEGER,
                ],
                'qualityLevelId'     => [
                        'required' => true,
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
        $response = $this->httpClient->request(Constants::HTTP_VERBS_CREATE, $this->buildUri('project/master'), [
                'headers'     => [
                        'sessionId' => $params[ 'sessionId' ],
                        'email'     => isset($params[ 'generic_email' ]) ? $params[ 'generic_email' ] : null,
                ],
                'form_params' => [
                        'name'               => $params[ 'name' ],
                        'sourceLanguageCode' => $params[ 'sourceLanguageCode' ],
                        'contentTypeId'      => $params[ 'contentTypeId' ],
                        'industryId'         => $params[ 'industryId' ],
                        'processId'          => $params[ 'processId' ],
                        'qualityLevelId'     => $params[ 'qualityLevelId' ],
                        'clientId'           => isset($params[ 'clientId' ]) ? $params[ 'clientId' ] : null,
                        'templateName'       => isset($params[ 'templateName' ]) ? $params[ 'templateName' ] : null,
                        'tmsProjectKey'      => isset($params[ 'tmsProjectKey' ]) ? $params[ 'tmsProjectKey' ] : null,
                ]
        ]);

        if ($response->getStatusCode() === StatusCode::CREATED) {
            return $this->decodeResponse($response);
        }
    }
}

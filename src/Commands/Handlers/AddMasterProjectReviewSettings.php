<?php

namespace Matecat\Dqf\Commands\Handlers;

use Matecat\Dqf\Commands\CommandHandler;
use Matecat\Dqf\Constants;
use Teapot\StatusCode;

class AddMasterProjectReviewSettings extends CommandHandler
{
    protected $rules = [
            'sessionId'           => [
                    'required' => true,
                    'type'     => Constants::DATA_TYPE_STRING,
            ],
            'projectKey'          => [
                    'required' => true,
                    'type'     => Constants::DATA_TYPE_STRING,
            ],
            'projectId'           => [
                    'required' => true,
                    'type'     => Constants::DATA_TYPE_INTEGER,
            ],
            'templateName'        => [
                    'required' => false,
                    'type'     => Constants::DATA_TYPE_STRING,
            ],
            'reviewType'          => [
                    'required' => true,
                    'type'     => Constants::DATA_TYPE_STRING,
                    'values'   => 'correction|error_typology|combined'
            ],
            'severityWeights'     => [
                    'required' => false,
                    'type'     => Constants::DATA_TYPE_STRING,
            ],
            'errorCategoryIds[0]' => [
                    'required' => false,
                    'type'     => Constants::DATA_TYPE_INTEGER,
            ],
            'errorCategoryIds[1]' => [
                    'required' => false,
                    'type'     => Constants::DATA_TYPE_INTEGER,
            ],
            'errorCategoryIds[2]' => [
                    'required' => false,
                    'type'     => Constants::DATA_TYPE_INTEGER,
            ],
            'passFailThreshold'   => [
                    'required' => false,
                    'type'     => Constants::DATA_TYPE_DOUBLE,
            ],
            'sampling'            => [
                    'required' => false,
                    'type'     => Constants::DATA_TYPE_DOUBLE,
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
        $response = $this->httpClient->request(Constants::HTTP_VERBS_CREATE, $this->buildUri('project/{projectId}/reviewSettings', [
                'projectId' => $params[ 'projectId' ]
        ]), [
                'headers'     => [
                        'projectKey' => $params[ 'projectKey' ],
                        'sessionId'  => $params[ 'sessionId' ],
                        'email'      => isset($params[ 'generic_email' ]) ? $params[ 'generic_email' ] : null,
                ],
                'form_params' => [
                        'reviewType'          => $params[ 'reviewType' ],
                        'templateName'        => isset($params[ 'templateName' ]) ? $params[ 'templateName' ] : null,
                        'severityWeights'     => isset($params[ 'severityWeights' ]) ? $params[ 'severityWeights' ] : null,
                        'errorCategoryIds[0]' => isset($params[ 'errorCategoryIds[0]' ]) ? $params[ 'errorCategoryIds[0]' ] : null,
                        'errorCategoryIds[1]' => isset($params[ 'errorCategoryIds[1]' ]) ? $params[ 'errorCategoryIds[1]' ] : null,
                        'errorCategoryIds[2]' => isset($params[ 'errorCategoryIds[2]' ]) ? $params[ 'errorCategoryIds[2]' ] : null,
                        'passFailThreshold'   => isset($params[ 'passFailThreshold' ]) ? $params[ 'passFailThreshold' ] : null,
                        'sampling'            => isset($params[ 'sampling' ]) ? $params[ 'sampling' ] : null,
                ],
        ]);

        if ($response->getStatusCode() === StatusCode::CREATED) {
            return $this->decodeResponse($response);
        }
    }
}

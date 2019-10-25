<?php

namespace Matecat\Dqf\Commands\Handlers;

use Matecat\Dqf\Commands\CommandHandler;
use Matecat\Dqf\Constants;
use Teapot\StatusCode;

class AddProjectReviewSettings extends CommandHandler
{
    protected function setRules()
    {
        $rules = [
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
                        'required'    => false,
                        'type'        => Constants::DATA_TYPE_STRING,
                        'required_if' => [ 'reviewType', Constants::LOGICAL_OPERATOR_EQUALS, 'combined|error_typology' ]
                ],
                'errorCategoryIds' => [
                        'required'    => false,
                        'type'        => Constants::DATA_TYPE_ARRAY,
                        'required_if' => [ 'reviewType', Constants::LOGICAL_OPERATOR_EQUALS, 'combined|error_typology' ]
                ],
                'passFailThreshold'   => [
                        'required'    => false,
                        'type'        => Constants::DATA_TYPE_DOUBLE,
                        'required_if' => [ 'reviewType', Constants::LOGICAL_OPERATOR_EQUALS, 'combined|error_typology' ]
                ],
                'sampling'            => [
                        'required' => false,
                        'type'     => Constants::DATA_TYPE_DOUBLE,
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
        $errorCategoryIds = [];
        foreach ($params[ 'errorCategoryIds'] as $i => $errorCategoryId) {
            $errorCategoryIds['errorCategoryIds['.$i.']'] = $errorCategoryId;
        }

        $formParams = array_merge([
                'reviewType'          => $params[ 'reviewType' ],
                'templateName'        => isset($params[ 'templateName' ]) ? $params[ 'templateName' ] : null,
                'severityWeights'     => isset($params[ 'severityWeights' ]) ? $params[ 'severityWeights' ] : null,
                'passFailThreshold'   => isset($params[ 'passFailThreshold' ]) ? $params[ 'passFailThreshold' ] : null,
                'sampling'            => isset($params[ 'sampling' ]) ? $params[ 'sampling' ] : null,
        ], $errorCategoryIds);

        $response = $this->httpClient->request(Constants::HTTP_VERBS_CREATE, $this->buildUri('project/{projectId}/reviewSettings', [
                'projectId' => $params[ 'projectId' ]
        ]), [
                'headers'     => [
                        'projectKey' => $params[ 'projectKey' ],
                        'sessionId'  => $params[ 'sessionId' ],
                        'email'      => isset($params[ 'generic_email' ]) ? $params[ 'generic_email' ] : null,
                ],
                'form_params' => $formParams,
        ]);

        if ($response->getStatusCode() === StatusCode::CREATED) {
            return $this->decodeResponse($response);
        }
    }
}

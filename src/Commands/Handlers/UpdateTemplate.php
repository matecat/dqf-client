<?php

namespace Matecat\Dqf\Commands\Handlers;

use Matecat\Dqf\Commands\CommandHandler;
use Matecat\Dqf\Constants;
use Teapot\StatusCode;

class UpdateTemplate extends CommandHandler
{
    protected function setRules() {
        $rules = [
                'sessionId'  => [
                        'required' => true,
                        'type'     => Constants::DATA_TYPE_STRING,
                ],
                'name'  => [
                        'required' => false,
                        'type'     => Constants::DATA_TYPE_STRING,
                ],
                'contentTypeId'  => [
                        'required' => false,
                        'type'     => Constants::DATA_TYPE_INTEGER,
                ],
                'industryId'  => [
                        'required' => false,
                        'type'     => Constants::DATA_TYPE_INTEGER,
                ],
                'processId'  => [
                        'required' => false,
                        'type'     => Constants::DATA_TYPE_INTEGER,
                ],
                'qualityLevelId'  => [
                        'required' => false,
                        'type'     => Constants::DATA_TYPE_INTEGER,
                ],
                'isPublic'  => [
                        'required' => false,
                        'type'     => Constants::DATA_TYPE_BOOLEAN,
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
        $response = $this->httpClient->request(Constants::HTTP_VERBS_UPDATE, $this->buildUri('user/projectTemplate/{projectTemplateId}', [ 'projectTemplateId' => $params['projectTemplateId'] ]), [
                'headers' => [
                        'sessionId'  => $params[ 'sessionId' ],
                        'email'      => isset($params[ 'generic_email' ]) ? $params[ 'generic_email' ] : null,
                ],
        ]);

        if ($response->getStatusCode() === StatusCode::CREATED) {
            return $this->decodeResponse($response);
        }
    }
}

<?php

namespace Matecat\Dqf\Commands\Handlers;

use Matecat\Dqf\Commands\CommandHandler;
use Matecat\Dqf\Constants;
use Teapot\StatusCode;

class AddTemplate extends CommandHandler
{
    protected function setRules() {
        $rules = [
                'sessionId'  => [
                        'required' => true,
                        'type'     => Constants::DATA_TYPE_STRING,
                ],
                'name'  => [
                        'required' => true,
                        'type'     => Constants::DATA_TYPE_STRING,
                ],
                'contentTypeId'  => [
                        'required' => true,
                        'type'     => Constants::DATA_TYPE_INTEGER,
                ],
                'industryId'  => [
                        'required' => true,
                        'type'     => Constants::DATA_TYPE_INTEGER,
                ],
                'processId'  => [
                        'required' => true,
                        'type'     => Constants::DATA_TYPE_INTEGER,
                ],
                'qualityLevelId'  => [
                        'required' => true,
                        'type'     => Constants::DATA_TYPE_INTEGER,
                ],
                'isPublic'  => [
                        'required' => true,
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
        $response = $this->httpClient->request(Constants::HTTP_VERBS_CREATE, $this->buildUri('user/projectTemplate', []), [
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

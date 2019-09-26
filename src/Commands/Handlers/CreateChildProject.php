<?php

namespace Matecat\Dqf\Commands\Handlers;

use Matecat\Dqf\Commands\CommandHandler;
use Matecat\Dqf\Constants;
use Teapot\StatusCode;

class CreateChildProject extends CommandHandler
{
    protected $rules = [
            'sessionId'       => [
                    'required' => true,
                    'type'     => Constants::DATA_TYPE_STRING,
            ],
            'parentKey'       => [
                    'required' => true,
                    'type'     => Constants::DATA_TYPE_STRING,
            ],
            'type'            => [
                    'required' => true,
                    'type'     => Constants::DATA_TYPE_STRING,
            ],
            'clientId'        => [
                    'required' => false,
                    'type'     => Constants::DATA_TYPE_STRING,
            ],
            'name'            => [
                    'required' => false,
                    'type'     => Constants::DATA_TYPE_STRING,
            ],
            'assignee'        => [
                    'required' => false,
                    'type'     => Constants::DATA_TYPE_STRING,
            ],
            'assigner'        => [
                    'required' => false,
                    'type'     => Constants::DATA_TYPE_STRING,
            ],
            'reviewSettingId' => [
                    'required' => false,
                    'type'     => Constants::DATA_TYPE_INTEGER,
            ],
            'isDummy'         => [
                    'required' => false,
                    'type'     => Constants::DATA_TYPE_BOOLEAN,
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
        $response = $this->httpClient->request(Constants::HTTP_VERBS_CREATE, $this->buildUri('project/child'), [
                'headers'     => [
                        'sessionId' => $params[ 'sessionId' ],
                        'email'     => isset($params[ 'generic_email' ]) ? $params[ 'generic_email' ] : null,
                ],
                'form_params' => [
                        'parentKey'       => $params[ 'parentKey' ],
                        'type'            => $params[ 'type' ],
                        'clientId'        => isset($params[ 'clientId' ]) ? $params[ 'clientId' ] : null,
                        'name'            => isset($params[ 'name' ]) ? $params[ 'name' ] : null,
                        'assignee'        => isset($params[ 'assignee' ]) ? $params[ 'assignee' ] : null,
                        'assigner'        => isset($params[ 'assigner' ]) ? $params[ 'assigner' ] : null,
                        'reviewSettingId' => isset($params[ 'reviewSettingId' ]) ? $params[ 'reviewSettingId' ] : null,
                        'isDummy'         => isset($params[ 'isDummy' ]) ? $params[ 'isDummy' ] : null,
                ]
        ]);

        if ($response->getStatusCode() === StatusCode::CREATED) {
            return $this->decodeResponse($response);
        }
    }
}

<?php

namespace Matecat\Dqf\Commands\Handlers;

use Matecat\Dqf\Commands\CommandHandler;
use Matecat\Dqf\Constants;
use Teapot\StatusCode;

class UpdateChildProjectStatus extends CommandHandler
{
    protected $rules = [
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
            'status'     => [
                    'required' => false,
                    'type'     => Constants::DATA_TYPE_STRING,
                    'values'   => 'completed',
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
        $response = $this->httpClient->request(Constants::HTTP_VERBS_UPDATE, $this->buildUri('project/child/{projectId}/status', [ 'projectId' => $params[ 'projectId' ] ]), [
                'headers'     => [
                        'projectKey' => $params[ 'projectKey' ],
                        'sessionId'  => $params[ 'sessionId' ],
                        'email'      => isset($params[ 'generic_email' ]) ? $params[ 'generic_email' ] : null,
                ],
                'form_params' => [
                        'status' => isset($params[ 'status' ]) ? $params[ 'status' ] : null,
                ]
        ]);

        if ($response->getStatusCode() === StatusCode::OK) {
            return $this->decodeResponse($response);
        }
    }
}

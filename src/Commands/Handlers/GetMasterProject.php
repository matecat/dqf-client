<?php

namespace Matecat\Dqf\Commands\Handlers;

use Matecat\Dqf\Commands\CommandHandler;
use Matecat\Dqf\Utils\ParamsValidator;
use Teapot\StatusCode;

class GetMasterProject extends CommandHandler
{
    protected $rules = [
            'sessionId'  => [
                    'required' => true,
                    'type'     => ParamsValidator::DATA_TYPE_STRING,
            ],
            'projectKey' => [
                    'required' => true,
                    'type'     => ParamsValidator::DATA_TYPE_STRING,
            ],
            'projectId'  => [
                    'required' => true,
                    'type'     => ParamsValidator::DATA_TYPE_INTEGER,
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
        $response = $this->httpClient->request('GET', $this->buildUri('project/master/{projectId}', [ 'projectId' => $params[ 'projectId' ] ]), [
                'headers' => [
                        'projectKey' => $params[ 'projectKey' ],
                        'sessionId'  => $params[ 'sessionId' ],
                        'email'      => isset($params[ 'generic_email' ]) ? $params[ 'generic_email' ] : null,
                ],
        ]);

        if ($response->getStatusCode() === StatusCode::OK) {
            return $this->decodeResponse($response);
        }
    }
}

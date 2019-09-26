<?php

namespace Matecat\Dqf\Commands\Handlers;

use Matecat\Dqf\Commands\CommandHandler;
use Matecat\Dqf\Constants;
use Teapot\StatusCode;

class GetProjectId extends CommandHandler
{
    protected $rules = [
            'sessionId'  => [
                    'required' => true,
                    'type'     => Constants::DATA_TYPE_STRING,
            ],
            'clientId' => [
                    'required' => true,
                    'type'     => Constants::DATA_TYPE_STRING,
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
        $response = $this->httpClient->request(Constants::HTTP_VERBS_GET, $this->buildUri('DQFProjectId'), [
                'headers' => [
                        'sessionId'  => $params[ 'sessionId' ],
                        'clientId'   => $params[ 'clientId' ],
                        'email'      => isset($params[ 'generic_email' ]) ? $params[ 'generic_email' ] : null,
                ],
        ]);

        if ($response->getStatusCode() === StatusCode::OK) {
            return $this->decodeResponse($response);
        }
    }
}

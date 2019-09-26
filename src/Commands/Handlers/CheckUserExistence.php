<?php

namespace Matecat\Dqf\Commands\Handlers;

use Matecat\Dqf\Commands\CommandHandler;
use Matecat\Dqf\Constants;
use Teapot\StatusCode;

class CheckUserExistence extends CommandHandler
{
    protected $rules = [
            'sessionId'          => [
                    'required' => true,
                    'type'     => Constants::DATA_TYPE_STRING,
            ],
            'email'         => [
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
        $response = $this->httpClient->request(Constants::HTTP_VERBS_GET, $this->buildUri(
                'check/user/{email}', [ 'email' => $params[ 'email' ], ] ), [
                'headers'     => [
                        'sessionId'  => $params[ 'sessionId' ],
                        'email'      => isset($params[ 'generic_email' ]) ? $params[ 'generic_email' ] : null,
                ],
        ]);

        if ($response->getStatusCode() === StatusCode::OK) {
            return $this->decodeResponse($response);
        }
    }
}

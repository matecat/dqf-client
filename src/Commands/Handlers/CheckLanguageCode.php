<?php

namespace Matecat\Dqf\Commands\Handlers;

use Matecat\Dqf\Commands\CommandHandler;
use Matecat\Dqf\Constants;
use Teapot\StatusCode;

class CheckLanguageCode extends CommandHandler
{
    protected $rules = [
            'languageCode'          => [
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
            'check/language/{languageCode}',
            [ 'languageCode' => $params[ 'languageCode' ], ]
        ), [
        ]);

        if ($response->getStatusCode() === StatusCode::OK) {
            return $this->decodeResponse($response);
        }
    }
}

<?php

namespace Matecat\Dqf\Commands\Handlers;

use Matecat\Dqf\Commands\CommandHandler;
use Matecat\Dqf\Constants;
use Teapot\StatusCode;

class GetTranslationsForSourceSegmentsInBatch extends CommandHandler
{
    protected $rules = [
            'sessionId'      => [
                    'required' => true,
                    'type'     => Constants::DATA_TYPE_STRING,
            ],
            'projectKey'     => [
                    'required' => true,
                    'type'     => Constants::DATA_TYPE_STRING,
            ],
            'projectId'      => [
                    'required' => true,
                    'type'     => Constants::DATA_TYPE_INTEGER,
            ],
            'targetLangCode' => [
                    'required' => true,
                    'type'     => Constants::DATA_TYPE_STRING,
            ],
            'fileId'         => [
                    'required' => true,
                    'type'     => Constants::DATA_TYPE_INTEGER,
            ],
            'offset'           => [
                    'required' => true,
                    'type'     => Constants::DATA_TYPE_INTEGER,
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
            'project/child/{projectId}/file/{fileId}/targetLang/{targetLangCode}/sourceSegment/translation/batch/{offset}',
            [
                        'projectId'      => $params[ 'projectId' ],
                        'fileId'         => $params[ 'fileId' ],
                        'targetLangCode' => $params[ 'targetLangCode' ],
                        'offset'         => $params[ 'offset' ],
                ]
        ), [
                'headers' => [
                        'projectKey'     => $params[ 'projectKey' ],
                        'sessionId'      => $params[ 'sessionId' ],
                        'email'          => isset($params[ 'generic_email' ]) ? $params[ 'generic_email' ] : null,
                ],
        ]);

        if ($response->getStatusCode() === StatusCode::CREATED) {
            return $this->decodeResponse($response);
        }
    }
}

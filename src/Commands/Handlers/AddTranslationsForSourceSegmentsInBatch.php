<?php

namespace Matecat\Dqf\Commands\Handlers;

use Matecat\Dqf\Commands\CommandHandler;
use Matecat\Dqf\Constants;
use Teapot\StatusCode;

class AddTranslationsForSourceSegmentsInBatch extends CommandHandler
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
            'body'           => [
                    'required' => true,
                    'type'     => Constants::DATA_TYPE_ARRAY,
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
        $response = $this->httpClient->request(Constants::HTTP_VERBS_CREATE, $this->buildUri(
            'project/child/{projectId}/file/{fileId}/targetLang/{targetLangCode}/sourceSegment/translation/batch',
            [
                        'projectId'      => $params[ 'projectId' ],
                        'fileId'         => $params[ 'fileId' ],
                        'targetLangCode' => $params[ 'targetLangCode' ],
                ]
        ), [
                'headers' => [
                        'Content-Type'   => 'application/json',
                        'Content-Length' => strlen(json_encode($params[ 'body' ])),
                        'projectKey'     => $params[ 'projectKey' ],
                        'sessionId'      => $params[ 'sessionId' ],
                        'email'          => isset($params[ 'generic_email' ]) ? $params[ 'generic_email' ] : null,
                ],
                'json'    => [
                        'segmentPairs' => $params[ 'body' ]
                ],
        ]);

        if ($response->getStatusCode() === StatusCode::CREATED) {
            return $this->decodeResponse($response);
        }
    }
}

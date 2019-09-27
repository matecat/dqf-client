<?php

namespace Matecat\Dqf\Commands\Handlers;

use Matecat\Dqf\Commands\CommandHandler;
use Matecat\Dqf\Constants;
use Teapot\StatusCode;

class UpdateCompleteTranslatedSegment extends CommandHandler
{
    protected $rules = [
            'sessionId'           => [
                    'required' => true,
                    'type'     => Constants::DATA_TYPE_STRING,
            ],
            'projectKey'          => [
                    'required' => true,
                    'type'     => Constants::DATA_TYPE_STRING,
            ],
            'projectId'           => [
                    'required' => true,
                    'type'     => Constants::DATA_TYPE_INTEGER,
            ],
            'targetLangCode'      => [
                    'required' => true,
                    'type'     => Constants::DATA_TYPE_STRING,
            ],
            'fileId'              => [
                    'required' => true,
                    'type'     => Constants::DATA_TYPE_INTEGER,
            ],
            'segmentId'           => [
                    'required' => true,
                    'type'     => Constants::DATA_TYPE_INTEGER,
            ],
            'sourceSegment'   => [
                    'required' => false,
                    'type'     => Constants::DATA_TYPE_STRING,
            ],
            'indexNo'   => [
                    'required' => true,
                    'type'     => Constants::DATA_TYPE_INTEGER,
            ],
            'targetSegment'   => [
                    'required' => true,
                    'type'     => Constants::DATA_TYPE_STRING,
            ],
            'editedSegment'   => [
                    'required' => true,
                    'type'     => Constants::DATA_TYPE_STRING,
            ],
            'time'            => [
                    'required' => false,
                    'type'     => Constants::DATA_TYPE_INTEGER,
            ],
            'segmentOriginId'     => [
                    'required' => true,
                    'type'     => Constants::DATA_TYPE_INTEGER,
            ],
            'matchRate'           => [
                    'required' => false,
                    'type'     => Constants::DATA_TYPE_DOUBLE,
            ],
            'mtEngineId'          => [
                    'required' => false,
                    'type'     => Constants::DATA_TYPE_INTEGER,
            ],
            'mtEngineOtherName'   => [
                    'required' => false,
                    'type'     => Constants::DATA_TYPE_STRING,
            ],
            'mtEngineVersion'     => [
                    'required' => false,
                    'type'     => Constants::DATA_TYPE_STRING,
            ],
            'segmentOriginDetail' => [
                    'required' => false,
                    'type'     => Constants::DATA_TYPE_STRING,
            ],
            'clientId'            => [
                    'required' => false,
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
        $response = $this->httpClient->request(Constants::HTTP_VERBS_UPDATE, $this->buildUri(
            'project/child/{projectId}/file/{fileId}/targetLang/{targetLangCode}/segment/{segmentId}',
            [
                        'projectId'      => $params[ 'projectId' ],
                        'fileId'         => $params[ 'fileId' ],
                        'targetLangCode' => $params[ 'targetLangCode' ],
                        'segmentId'      => $params[ 'segmentId' ],
                ]
        ), [
                'headers'     => [
                        'projectKey' => $params[ 'projectKey' ],
                        'sessionId'  => $params[ 'sessionId' ],
                        'email'      => isset($params[ 'generic_email' ]) ? $params[ 'generic_email' ] : null,
                ],
                'form_params' => [
                        'sourceSegment'       => $params[ 'sourceSegment' ],
                        'indexNo'             => $params[ 'indexNo' ],
                        'targetSegment'       => $params[ 'targetSegment' ],
                        'editedSegment'       => $params[ 'editedSegment' ],
                        'time'                => isset($params[ 'time' ]) ? $params[ 'time' ] : null,
                        'segmentOriginId'     => $params[ 'segmentOriginId' ],
                        'matchRate'           => isset($params[ 'matchRate' ]) ? $params[ 'matchRate' ] : null,
                        'mtEngineId'          => isset($params[ 'mtEngineId' ]) ? $params[ 'mtEngineId' ] : null,
                        'mtEngineOtherName'   => isset($params[ 'mtEngineOtherName' ]) ? $params[ 'mtEngineOtherName' ] : null,
                        'mtEngineVersion'     => isset($params[ 'mtEngineVersion' ]) ? $params[ 'mtEngineVersion' ] : null,
                        'segmentOriginDetail' => isset($params[ 'segmentOriginDetail' ]) ? $params[ 'segmentOriginDetail' ] : null,
                        'clientId'            => isset($params[ 'clientId' ]) ? $params[ 'clientId' ] : null,
                ],
        ]);

        if ($response->getStatusCode() === StatusCode::CREATED) {
            return $this->decodeResponse($response);
        }
    }
}

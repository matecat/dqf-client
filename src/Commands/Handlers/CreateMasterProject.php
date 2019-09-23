<?php

namespace Matecat\Dqf\Commands\Handlers;

use Matecat\Dqf\Commands\CommandHandler;
use Teapot\StatusCode;

class CreateMasterProject extends CommandHandler {
    protected $required = [
            'sessionId',
            'name',
            'sourceLanguageCode',
            'contentTypeId',
            'industryId',
            'processId',
            'qualityLevelId',
    ];

    /**
     * @param array $params
     *
     * @return mixed|void
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function handle( $params = [] ) {
        $response = $this->httpClient->request( 'POST', $this->buildUri( 'project/master' ), [
                'headers'     => [
                        'sessionId' => $params[ 'sessionId' ],
                ],
                'form_params' => [
                        'name'               => $params[ 'name' ],
                        'sourceLanguageCode' => $params[ 'sourceLanguageCode' ],
                        'contentTypeId'      => $params[ 'contentTypeId' ],
                        'industryId'         => $params[ 'industryId' ],
                        'processId'          => $params[ 'processId' ],
                        'qualityLevelId'     => $params[ 'qualityLevelId' ],
                        'clientId'           => isset($params[ 'clientId' ]) ? $params[ 'clientId' ] : null,
                        'templateName'       => isset($params[ 'templateName' ]) ? $params[ 'templateName' ] : null,
                        'tmsProjectKey'      => isset($params[ 'tmsProjectKey' ]) ? $params[ 'tmsProjectKey' ] : null,
                ]
        ] );

        if ( $response->getStatusCode() === StatusCode::CREATED ) {
            return $this->decodeResponse( $response );
        }
    }
}

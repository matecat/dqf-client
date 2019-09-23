<?php

namespace Matecat\Dqf\Commands\Handlers;

use Matecat\Dqf\Commands\CommandHandler;
use Teapot\StatusCode;

class AddSourceSegmentsInBatchToMasterProject extends CommandHandler {

    protected $required = [
            'sessionId',
            'projectKey',
            'projectId',
            'fileId',
            'body',
    ];

    /**
     * @param array $params
     *
     * @return mixed|void
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function handle( $params = [] ) {

        $response   = $this->httpClient->request( 'POST', $this->buildUri( 'project/master/{projectId}/file/{fileId}/sourceSegment/batch', [
                        'projectId' => $params[ 'projectId' ],
                        'fileId'    => $params[ 'fileId' ],
                ]
        ), [
                'headers'     => [
                        'Content-Type'   => 'application/json',
                        'Content-Length' => strlen( json_encode($params[ 'body' ]) ),
                        'projectKey'     => $params[ 'projectKey' ],
                        'sessionId'      => $params[ 'sessionId' ],
                ],
                'json' => [
                        'sourceSegments' => $params[ 'body' ]
                ],
        ] );


        if ( $response->getStatusCode() === StatusCode::CREATED ) {
            return $this->decodeResponse( $response );
        }
    }
}

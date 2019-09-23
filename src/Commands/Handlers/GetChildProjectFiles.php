<?php

namespace Matecat\Dqf\Commands\Handlers;

use Matecat\Dqf\Commands\CommandHandler;
use Teapot\StatusCode;

class GetChildProjectFiles extends CommandHandler {
    protected $required = [
            'sessionId',
            'projectKey',
            'projectId',
            'fileId',
    ];

    /**
     * @param array $params
     *
     * @return mixed|void
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function handle( $params = [] ) {
        $response = $this->httpClient->request( 'GET', $this->buildUri( 'project/master/{projectId}/file/{fileId}', [
                'projectId' => $params[ 'projectId' ] ,
                'fileId' => $params[ 'fileId' ] ,
        ] ), [
                'headers'     => [
                        'sessionId'  => $params[ 'sessionId' ],
                        'projectKey' => $params[ 'projectKey' ],
                ],
        ] );

        if ( $response->getStatusCode() === StatusCode::OK ) {
            return $this->decodeResponse( $response );
        }
    }
}

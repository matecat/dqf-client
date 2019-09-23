<?php

namespace Matecat\Dqf\Commands\Handlers;

use Matecat\Dqf\Commands\CommandHandler;
use Teapot\StatusCode;

class GetChildProjectFile extends CommandHandler {
    protected $required = [
            'sessionId',
            'projectKey',
            'projectId',
    ];

    /**
     * @param array $params
     *
     * @return mixed|void
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function handle( $params = [] ) {
        $response = $this->httpClient->request( 'GET', $this->buildUri( 'project/child/{projectId}/file', [ 'projectId' => $params[ 'projectId' ] ] ), [
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

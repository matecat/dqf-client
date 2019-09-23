<?php

namespace Matecat\Dqf\Commands\Handlers;

use Matecat\Dqf\Commands\CommandHandler;
use Teapot\StatusCode;

class GetMasterProject extends CommandHandler {
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
        $response = $this->httpClient->request( 'GET', $this->buildUri( 'project/master/{projectId}', [ 'projectId' => $params[ 'projectId' ] ] ), [
                'headers' => [
                        'projectKey' => $params[ 'projectKey' ],
                        'sessionId'  => $params[ 'sessionId' ],
                ],
        ] );

        if ( $response->getStatusCode() === StatusCode::OK ) {
            return $this->decodeResponse( $response );
        }
    }
}

<?php

namespace Matecat\Dqf\Commands\Handlers;

use Matecat\Dqf\Commands\CommandHandler;
use Teapot\StatusCode;

class AddMasterProjectFile extends CommandHandler {
    protected $required = [
            'sessionId',
            'projectKey',
            'projectId',
            'name',
            'numberOfSegments',
    ];

    /**
     * @param array $params
     *
     * @return mixed|void
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function handle( $params = [] ) {
        $response = $this->httpClient->request( 'POST', $this->buildUri( 'project/master/{projectId}/file', [ 'projectId' => $params[ 'projectId' ] ] ), [
                'headers'     => [
                        'projectKey' => $params[ 'projectKey' ],
                        'sessionId'  => $params[ 'sessionId' ],
                ],
                'form_params' => [
                        'name'             => $params[ 'name' ],
                        'numberOfSegments' => $params[ 'numberOfSegments' ],
                        'clientId'         => isset($params[ 'clientId' ]) ? $params[ 'clientId' ] : null,
                        'tmsFileId'        => isset($params[ 'tmsFileId' ]) ? $params[ 'tmsFileId' ] : null,
                ],
        ] );

        if ( $response->getStatusCode() === StatusCode::CREATED ) {
            return $this->decodeResponse( $response );
        }
    }
}

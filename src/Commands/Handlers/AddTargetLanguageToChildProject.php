<?php

namespace Matecat\Dqf\Commands\Handlers;

use Matecat\Dqf\Commands\CommandHandler;
use Teapot\StatusCode;

class AddTargetLanguageToChildProject extends CommandHandler {

    protected $required = [
            'sessionId',
            'projectKey',
            'projectId',
            'fileId',
            'targetLanguageCode',
    ];

    /**
     * @param array $params
     *
     * @return mixed|void
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function handle( $params = [] ) {

        $response = $this->httpClient->request( 'POST', $this->buildUri( 'project/child/{projectId}/file/{fileId}/targetLang', [
                        'projectId' => $params[ 'projectId' ],
                        'fileId'    => $params[ 'fileId' ],
                ]
        ), [
                'headers'     => [
                        'projectKey' => $params[ 'projectKey' ],
                        'sessionId'  => $params[ 'sessionId' ],
                ],
                'form_params' => [
                        'targetLanguageCode' => $params[ 'body' ]
                ],
        ] );

        if ( $response->getStatusCode() === StatusCode::CREATED ) {
            return $this->decodeResponse( $response );
        }
    }
}

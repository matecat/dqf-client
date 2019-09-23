<?php

namespace Matecat\Dqf\Commands\Handlers;

use Matecat\Dqf\Commands\CommandHandler;
use Teapot\StatusCode;

class CreateChildProject extends CommandHandler {

    protected $required = [
            'sessionId',
            'parentKey',
            'type',
    ];

    /**
     * @param array $params
     *
     * @return mixed|void
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function handle( $params = [] ) {
        $response = $this->httpClient->request( 'POST', $this->buildUri( 'project/child' ), [
                'headers'     => [
                        'sessionId' => $params[ 'sessionId' ],
                ],
                'form_params' => [
                        'parentKey'       => $params[ 'parentKey' ],
                        'type'            => $params[ 'type' ],
                        'clientId'        => isset( $params[ 'clientId' ] ) ? $params[ 'clientId' ] : null,
                        'name'            => isset( $params[ 'name' ] ) ? $params[ 'name' ] : null,
                        'assignee'        => isset( $params[ 'assignee' ] ) ? $params[ 'assignee' ] : null,
                        'assigner'        => isset( $params[ 'assigner' ] ) ? $params[ 'assigner' ] : null,
                        'reviewSettingId' => isset( $params[ 'reviewSettingId' ] ) ? $params[ 'reviewSettingId' ] : null,
                        'isDummy'         => isset( $params[ 'isDummy' ] ) ? $params[ 'isDummy' ] : null,
                ]
        ] );

        if ( $response->getStatusCode() === StatusCode::CREATED ) {
            return $this->decodeResponse( $response );
        }
    }
}

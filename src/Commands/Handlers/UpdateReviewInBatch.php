<?php

namespace Matecat\Dqf\Commands\Handlers;

use Matecat\Dqf\Commands\CommandHandler;
use Matecat\Dqf\Constants;
use Teapot\StatusCode;

class UpdateReviewInBatch extends CommandHandler {
    protected function setRules() {
        $rules = [
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
                'fileId'         => [
                        'required' => true,
                        'type'     => Constants::DATA_TYPE_INTEGER,
                ],
                'translationId'  => [
                        'required' => true,
                        'type'     => Constants::DATA_TYPE_INTEGER,
                ],
                'targetLangCode' => [
                        'required' => true,
                        'type'     => Constants::DATA_TYPE_STRING,
                ],
                'body'           => [
                        'required' => true,
                        'type'     => Constants::DATA_TYPE_ARRAY,
                ],
                'batchId'        => [
                        'required' => false,
                        'type'     => Constants::DATA_TYPE_STRING,
                ],
                'overwrite'      => [
                        'required' => true,
                        'type'     => Constants::DATA_TYPE_BOOLEAN,
                ],
        ];

        $this->rules = $rules;
    }

    /**
     * @param array $params
     *
     * @return mixed|void
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function handle( $params = [] ) {
        $body[ 'revisions' ] = $params[ 'body' ];
        $body[ 'batchId' ]   = isset( $params[ 'batchId' ] ) ? $params[ 'batchId' ] : null;
        $body[ 'overwrite' ] = isset( $params[ 'overwrite' ] ) ? $params[ 'overwrite' ] : null;

        $json = json_encode( $body );

        $response = $this->httpClient->request( Constants::HTTP_VERBS_CREATE, $this->buildUri(
                'project/child/{projectId}/file/{fileId}/targetLang/{targetLangCode}/translation/{translationId}/batchReview',
                [
                        'projectId'      => $params[ 'projectId' ],
                        'fileId'         => $params[ 'fileId' ],
                        'targetLangCode' => $params[ 'targetLangCode' ],
                        'translationId'  => $params[ 'translationId' ],
                ]
        ), [
                'headers' => [
                        'Content-Type'   => 'application/json',
                        'Content-Length' => strlen( $json ),
                        'projectKey'     => $params[ 'projectKey' ],
                        'sessionId'      => $params[ 'sessionId' ],
                        'email'          => isset( $params[ 'generic_email' ] ) ? $params[ 'generic_email' ] : null,
                ],
                'json'    => json_decode($json),
        ] );

        if ( $response->getStatusCode() === StatusCode::CREATED ) {
            return $this->decodeResponse( $response );
        }
    }
}

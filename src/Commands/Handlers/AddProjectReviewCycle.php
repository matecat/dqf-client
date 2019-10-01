<?php

namespace Matecat\Dqf\Commands\Handlers;

use Matecat\Dqf\Commands\CommandHandler;
use Matecat\Dqf\Constants;
use Teapot\StatusCode;

class AddProjectReviewCycle extends CommandHandler {
    protected function setRules() {
        $rules = [
                'sessionId'  => [
                        'required' => true,
                        'type'     => Constants::DATA_TYPE_STRING,
                ],
                'projectId'  => [
                        'required' => true,
                        'type'     => Constants::DATA_TYPE_INTEGER,
                ],
                'projectKey' => [
                        'required' => true,
                        'type'     => Constants::DATA_TYPE_STRING,
                ],
                'apiKeyTms' => [
                        'required' => true,
                        'type'     => Constants::DATA_TYPE_STRING,
                ],
                'userId' => [
                        'required' => true,
                        'type'     => Constants::DATA_TYPE_INTEGER,
                ],
                'fileTargetLangIds[0]' => [
                        'required' => true,
                        'type'     => Constants::DATA_TYPE_INTEGER,
                ],
                'fileTargetLangIds[1]' => [
                        'required' => false,
                        'type'     => Constants::DATA_TYPE_INTEGER,
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
        $response = $this->httpClient->request( Constants::HTTP_VERBS_CREATE, $this->buildUri( 'project/{projectId}/reviewCycle', [ 'projectId' => $params[ 'projectId' ] ] ), [
                'headers' => [
                        'sessionId'            => $params[ 'sessionId' ],
                        'projectKey'           => $params[ 'projectKey' ],
                        'apiKeyTms'            => $params[ 'apiKeyTms' ],
                        'userId'               => $params[ 'userId' ],
                        'fileTargetLangIds[0]' => $params[ 'fileTargetLangIds[0]' ],
                        'fileTargetLangIds[1]' => isset( $params[ 'fileTargetLangIds[1]' ] ) ? $params[ 'fileTargetLangIds[1]' ] : null,
                        'email'                => isset( $params[ 'generic_email' ] ) ? $params[ 'generic_email' ] : null,
                ],
        ] );

        if ( $response->getStatusCode() === StatusCode::CREATED ) {
            return $this->decodeResponse( $response );
        }
    }
}

<?php

namespace Matecat\Dqf\Commands\Handlers;

use Matecat\Dqf\Commands\CommandHandler;
use Matecat\Dqf\Constants;
use Teapot\StatusCode;

class GetBasicAttributesAggregate extends CommandHandler
{
    public function handle($params = [])
    {
        $aggregate = [];

        $uris = [
            'language',
            'severity',
            'mtEngine',
            'process',
            'contentType',
            'segmentOrigin',
            'catTool',
            'industry',
            'errorCategory',
            'qualitylevel',
        ];

        foreach ($uris as $uri){
            $response = $this->httpClient->request(Constants::HTTP_VERBS_GET, $this->buildUri($uri), []);

            if ($response->getStatusCode() === StatusCode::OK) {
                $aggregate[$uri] = $this->decodeResponse($response);
            }
        }

        return $aggregate;
    }
}
<?php

namespace Matecat\Dqf;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Middleware;
use Matecat\Dqf\Commands\CommandHandler;
use Matecat\Dqf\Exceptions\MissingParamsException;
use Matecat\Dqf\Exceptions\ParamsValidatorException;
use Monolog\Formatter\JsonFormatter;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

/**
 * Class Client
 *
 * This class is a tailored client for DQF integration into Matecat
 * but it's quite agnostic and can be used elsewhere
 * -------------------------------------------------------------------------
 *
 * Read here the full documentation:
 * https://dqf-api.stag.taus.net/
 *
 * Method list:
 *
 * @method mixed addMasterProjectFile( array $input )
 * @method mixed addSourceSegmentsInBatchToMasterProject( array $input )
 * @method mixed addTargetLanguageToChildProject( array $input )
 * @method mixed addTargetLanguageToMasterProject( array $input )
 * @method mixed createMasterProject( array $input )
 * @method mixed createChildProject( array $input )
 * @method mixed deleteChildProject( array $input )
 * @method mixed deleteMasterProject( array $input )
 * @method mixed deleteMasterProjectFile( array $input )
 * @method mixed deleteTargetLanguageForChildProject( array $input )
 * @method mixed deleteTargetLanguageForMasterProject( array $input )
 * @method mixed getChildProject( array $input )
 * @method mixed getChildProjectFile( array $input )
 * @method mixed getChildProjectFiles( array $input )
 * @method mixed getMasterProject( array $input )
 * @method mixed getMasterProjectFile( array $input )
 * @method mixed getTargetLanguageForChildProject( array $input )
 * @method mixed getTargetLanguageForMasterProject( array $input )
 * @method mixed getTargetLanguageForChildProjectByLang( array $input )
 * @method mixed getTargetLanguageForMasterProjectByLang( array $input )
 * @method mixed getUser( array $input )
 * @method mixed login( array $input )
 * @method mixed logout( array $input )
 * @method mixed updateChildProject( array $input )
 * @method mixed updateMasterProject( array $input )
 * @method mixed updateMasterProjectFile( array $input )
 *
 * @package Matecat\Dqf
 */
class Client
{
    /**
     * @var array
     */
    private $clientParams;

    /**
     * @var HttpClient
     */
    private $httpClient;

    /**
     * Client constructor.
     *
     * @param array $params
     */
    public function __construct(array $params)
    {
        if (false === isset($params[ 'apiKey' ])) {
            throw new \InvalidArgumentException('apiKey MUST be provided.');
        }

        if (false === isset($params[ 'idPrefix' ])) {
            throw new \InvalidArgumentException('idPrefix MUST be provided.');
        }

        if (false === isset($params[ 'encryptionKey' ])) {
            throw new \InvalidArgumentException('encryptionKey MUST be provided.');
        }

        if (false === isset($params[ 'encryptionIV' ])) {
            throw new \InvalidArgumentException('encryptionIV MUST be provided.');
        }

        $this->clientParams[ 'apiKey' ]         = $params[ 'apiKey' ];
        $this->clientParams[ 'idPrefix' ]       = $params[ 'idPrefix' ];
        $this->clientParams[ 'encryptionKey' ]  = $params[ 'encryptionKey' ];
        $this->clientParams[ 'encryptionIV' ]   = $params[ 'encryptionIV' ];
        $this->clientParams[ 'debug' ]          = (isset($params[ 'debug' ]) and $params[ 'debug' ] === true) ? true : false;
        $this->clientParams[ 'logStoragePath' ] = (isset($params[ 'logStoragePath' ])) ? $params[ 'logStoragePath' ] : null;

        $this->httpClient = $this->createHttpClientInstance($this->clientParams[ 'debug' ], $this->clientParams[ 'logStoragePath' ]);
    }

    /**
     * @param bool $debug
     * @param null $logStoragePath
     *
     * @return HttpClient
     */
    private function createHttpClientInstance($debug = false, $logStoragePath = null)
    {
        $stack = HandlerStack::create();
        $stack->push(
            Middleware::log(
                $this->getLogger($logStoragePath),
                new MessageFormatter('{req_body} - {res_body}')
            )
        );

        return new HttpClient([
                'base_uri' => ($debug) ? Constants::API_STAGING_URI : Constants::API_PRODUCTION_URI,
                'headers'  => [
                        'apiKey' => $this->clientParams[ 'apiKey' ]
                ],
                'handler'  => $stack,
        ]);
    }

    /**
     * @param $logStoragePath
     *
     * @return Logger
     */
    private function getLogger($logStoragePath = null)
    {
        $logger = new Logger('dqf-api-consumer');
        $streamHandler = new RotatingFileHandler(($logStoragePath) ? $logStoragePath : __DIR__ . '/../log');
        $streamHandler->setFormatter(new JsonFormatter());
        $logger->pushHandler($streamHandler);

        return $logger;
    }

    /**
     * Executes the called method
     *
     * @param $name
     * @param $args
     *
     * @return mixed|void
     * @throws ParamsValidatorException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function __call($name, $args)
    {
        $params         = isset($args[ 0 ]) ? $args[ 0 ] : [];
        $commandHandler = 'Matecat\\Dqf\\Commands\\Handlers\\' . ucfirst($name);

        if (false === class_exists($commandHandler)) {
            throw new \InvalidArgumentException($commandHandler . ' is not a valid command name. Please refer to README to get the complete command list.');
        }

        /** @var CommandHandler $commandHandler */
        $commandHandler = new $commandHandler($this->httpClient, $this->clientParams);

        $validate = $commandHandler->validate($params);
        if (count($validate)) {
            throw new ParamsValidatorException($name . ' cannot be executed. '.implode(',', $validate).'.');
        }

        return $commandHandler->handle($params);
    }
}

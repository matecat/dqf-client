<?php

namespace Matecat\Dqf\Utils;

use GuzzleHttp\HandlerStack;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Middleware;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class HandlerStackFactory
{
    /**
     * @return HandlerStack
     */
    public static function create($logStoragePath)
    {
        $stack = HandlerStack::create();
        $stack->push(
            Middleware::log(
                self::getLogger($logStoragePath, Logger::INFO),
                new MessageFormatter(self::getMessageFormatterTemplate()),
                Logger::INFO
            )
        );

        return $stack;
    }

    /**
     * @param null $logStoragePath
     * @param int  $level
     *
     * @return Logger
     */
    private static function getLogger($logStoragePath = null, $level = Logger::INFO)
    {
        $logger        = new Logger('dqf-api-consumer');
        $streamHandler = new RotatingFileHandler(($logStoragePath) ? $logStoragePath : __DIR__ . '/../log', 0, $level);
        $streamHandler->setFormatter(new ClientLogsFormatter());
        $streamHandler->setLevel($level);
        $logger->pushHandler($streamHandler);

        return $logger;
    }

    /**
     * @return string
     */
    private static function getMessageFormatterTemplate()
    {
        return '{"date":"{ts}", "method":"{method}", "uri":"{uri}", "code":{code}, "req_headers":"{req_headers}", "req_body":"{req_body}", "res_body":{res_body}}';
    }
}

<?php

namespace Matecat\Dqf\Commands;

use GuzzleHttp\Client as HttpClient;
use Matecat\Dqf\Constants;
use Matecat\Dqf\Utils\ParamsValidator;
use Psr\Http\Message\MessageInterface;

abstract class CommandHandler implements CommandHandlerInterface
{
    /**
     * @see ParamsValidatorTest for implementation
     *
     * Parameters rules
     * Overrided by child classes
     *
     * @var array
     */
    protected $rules = [];

    /**
     * @var HttpClient
     */
    protected $httpClient;

    /**
     * @var array
     */
    protected $params;

    /**
     * CommandHandler constructor.
     *
     * @param HttpClient $httpClient
     * @param array      $clientParams
     */
    public function __construct(HttpClient $httpClient, array $clientParams)
    {
        $this->httpClient = $httpClient;
        $this->params     = $clientParams;
    }

    abstract protected function setRules();

    /**
     * @param string $path
     * @param array  $params
     *
     * @return string
     */
    protected function buildUri($path, array $params = [])
    {
        foreach ($params as $key => $param) {
            $path = str_replace('{' . $key . '}', $param, $path);
        }

        return Constants::API_VERSION . DIRECTORY_SEPARATOR . $path;
    }

    /**
     * @param MessageInterface $message
     *
     * @return mixed
     */
    protected function decodeResponse(MessageInterface $message)
    {
        return json_decode($message->getBody());
    }

    /**
     * @param array $params
     *
     * @return array
     */
    public function validate($params = [])
    {
        // set the rules
        $this->setRules();

        // allow all commands to handle generic sessions
        $genericEmail = [
                'generic_email' => [
                        'required' => false,
                        'type'     => 'string',
                ]
        ];

        return ParamsValidator::validate($params, array_merge($this->rules, $genericEmail));
    }
}

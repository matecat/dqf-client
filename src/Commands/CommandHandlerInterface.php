<?php


namespace Matecat\Dqf\Commands;

interface CommandHandlerInterface
{
    /**
     * @param array $params
     *
     * @return mixed|void
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function handle($params = []);
}

<?php

namespace Matecat\Dqf\Repository\Api;

use Matecat\Dqf\Cache\BasicAttributes;
use Matecat\Dqf\Client;
use Matecat\Dqf\Model\Entity\Language;
use Matecat\Dqf\Model\Repository\CrudApiRepositoryInterface;

abstract class AbstractApiRepository
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @var string
     */
    protected $sessionId;

    /**
     * @var null
     */
    protected $genericEmail;

    /**
     * AbstractApiRepository constructor.
     *
     * @param Client $client
     * @param string $sessionId
     * @param null   $genericEmail
     */
    public function __construct(Client $client, $sessionId, $genericEmail = null)
    {
        $this->client = $client;
        $this->sessionId = $sessionId;
        $this->genericEmail = $genericEmail;
    }

    /**
     * @param Language $lang
     */
    protected function hydrateLanguage(Language $lang)
    {
        foreach (BasicAttributes::get('language') as $language) {
            if ($language->localeCode === $lang->getLocaleCode()) {
                $lang->setName($language->name);
                $lang->setDqfId($language->id);
                break;
            }
        }
    }
}

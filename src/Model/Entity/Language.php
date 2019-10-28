<?php

namespace Matecat\Dqf\Model\Entity;

use Matecat\Dqf\Cache\BasicAttributes;

class Language extends BaseApiEntity
{
    /**
     * @var string
     */
    private $localeCode;

    /**
     * @var string
     */
    private $name;

    /**
     * Language constructor.
     *
     * @param string $localeCode
     */
    public function __construct($localeCode)
    {
        if (false === $this->checkLocaleCode($localeCode)) {
            throw new \DomainException($localeCode . ' is not a valid locale code');
        }

        $this->localeCode = $localeCode;
    }

    /**
     * Check it on:
     * https://dqf-api.stag.taus.net/v3/language
     *
     * @param string $localeCode
     *
     * @return bool
     */
    private function checkLocaleCode($localeCode)
    {
        $languages = BasicAttributes::get('language');
        foreach ($languages as $language){
            if ($localeCode === $language->localeCode) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return string
     */
    public function getLocaleCode()
    {
        return $this->localeCode;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }
}

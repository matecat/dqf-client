<?php

namespace Matecat\Dqf\Repository\Api;

use Matecat\Dqf\Cache\BasicAttributes;
use Matecat\Dqf\Model\Entity\FileTargetLang;

abstract class AbstractProjectRepository extends AbstractApiRepository
{
    /**
     * @param FileTargetLang $fileTargetLang
     */
    protected function hydrateFileTargetLang(FileTargetLang $fileTargetLang)
    {
        foreach (BasicAttributes::get('language') as $language) {
            if ($language->localeCode === $fileTargetLang->getLanguage()->getLocaleCode()) {
                $fileTargetLang->getLanguage()->setName($language->name);
                $fileTargetLang->getLanguage()->setDqfId($language->id);
                break;
            }
        }
    }
}

<?php

namespace Matecat\Dqf\Model\Entity;

class FileTargetLang extends BaseApiEntity
{
    /**
     * @var Language
     */
    private $language;

    /**
     * @var File
     */
    private $file;

    /**
     * FileTargetLang constructor.
     *
     * @param string $languageCode
     * @param File     $file
     */
    public function __construct($languageCode, File $file)
    {
        $this->language = new Language($languageCode);
        $this->file     = $file;
    }

    /**
     * @return Language
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @return File
     */
    public function getFile()
    {
        return $this->file;
    }
}

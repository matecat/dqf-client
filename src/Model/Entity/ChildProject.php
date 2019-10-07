<?php

namespace Matecat\Dqf\Model\Entity;

class ChildProject extends BaseApiEntity
{
    /**
     * @var MasterProject
     */
    private $masterProject;

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $assignee;

    /**
     * @var string
     */
    private $assigner;

    /**
     * @var bool
     */
    private $isDummy;

    /**
     * @var ReviewSettings
     */
    private $reviewSettings;

    /**
     * @var Template
     */
    private $template;

    /**
     * @var array
     */
    private $targetLanguageAssoc;

    /**
     * ChildProject constructor.
     *
     * @param MasterProject $masterProject
     * @param string $type
     */
    public function __construct(MasterProject $masterProject, $type)
    {
        $this->masterProject = $masterProject;
        $this->setType($type);
    }

    /**
     * @return MasterProject
     */
    public function getMasterProject()
    {
        return $this->masterProject;
    }

    /**
     * @param MasterProject $masterProject
     */
    public function setMasterProject($masterProject)
    {
        $this->masterProject = $masterProject;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    private function setType($type)
    {
        $allowed = ['translation', 'review'];

        if (false === in_array($type, $allowed)) {
            throw new \DomainException($type . 'is not a valid type. [Allowed: '.implode(',', $allowed).']');
        }

        $this->type = $type;
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

    /**
     * @return string
     */
    public function getAssignee()
    {
        return $this->assignee;
    }

    /**
     * @param string $assignee
     */
    public function setAssignee($assignee)
    {
        $this->assignee = $assignee;
    }

    /**
     * @return string
     */
    public function getAssigner()
    {
        return $this->assigner;
    }

    /**
     * @param string $assigner
     */
    public function setAssigner($assigner)
    {
        $this->assigner = $assigner;
    }

    /**
     * @return bool
     */
    public function isDummy()
    {
        return $this->isDummy;
    }

    /**
     * @param bool $isDummy
     */
    public function setIsDummy($isDummy)
    {
        if ( true === $isDummy and $this->type === 'review' ) {
            throw new \DomainException('\'isDummy\' MUST be set to false if project tpye is \'review\'');
        }

        $this->isDummy = $isDummy;
    }

    /**
     * @return ReviewSettings
     */
    public function getReviewSettings()
    {
        return $this->reviewSettings;
    }

    /**
     * @param ReviewSettings $reviewSettings
     */
    public function setReviewSettings($reviewSettings)
    {
        $this->reviewSettings = $reviewSettings;
    }

    /**
     * @return Template
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * @param Template $template
     */
    public function setTemplate($template)
    {
        $this->template = $template;
    }

    /**
     * @param string $languageCode
     * @param File     $file
     */
    public function assocTargetLanguageToFile($languageCode, File $file)
    {
        if(false === $this->masterProject->hasFile($file)){
            throw new \DomainException($file->getName() . ' does not belong to the master project');
        }

        $this->targetLanguageAssoc[$languageCode][] = $file;
    }

    /**
     * @return Language[]
     */
    public function getTargetLanguages()
    {
        $targetLanguages = [];

        foreach (array_keys($this->targetLanguageAssoc) as $targetLanguage){
            $targetLanguages[]  = new Language($targetLanguage);
        }

        return $targetLanguages;
    }
}

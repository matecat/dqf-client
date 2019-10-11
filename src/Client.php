<?php

namespace Matecat\Dqf;

use GuzzleHttp\Client as HttpClient;
use Matecat\Dqf\Commands\CommandHandler;
use Matecat\Dqf\Exceptions\ParamsValidatorException;
use Matecat\Dqf\Utils\HandlerStackFactory;

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
 * @method mixed addChildProjectTargetLanguage( array $input ) - Add a target language for a file of a child project
 * @method mixed addCompleteTranslationOfASegment( array $input ) - Add a complete translation of a segment
 * @method mixed addMasterProjectFile( array $input ) - Add files to a master project
 * @method mixed addMasterProjectTargetLanguage( array $input ) - Add source segments on a file of a project
 * @method mixed addProjectReviewCycle( array $input ) - Convenient method to automatically create review children
 * @method mixed addProjectReviewSettings( array $input ) - Add review preferences on a project
 * @method mixed addRemainingTargetSegmentsInBatch( array $input ) - Add remaining target segments
 * @method mixed addReviewTemplate( array $input ) - Add a review template
 * @method mixed addSourceSegmentsInBatchToMasterProject( array $input ) - Add target languages for the translation of a file
 * @method mixed addTemplate( array $input ) - Add project templates
 * @method mixed addTranslationOfASourceSegment( array $input ) - Add the translation of a source segment
 * @method mixed addTranslationsForSourceSegmentsInBatch( array $input ) - Add the translation of a source segment
 * @method mixed checkLanguageCode( array $input ) - Check language code
 * @method mixed checkUserExistence( array $input ) - Check the existence of a TAUS user
 * @method mixed createChildProject( array $input ) - Add a new child Project to DQF
 * @method mixed createMasterProject( array $input ) - Add a new master project to DQF
 * @method mixed deleteChildProject( array $input ) - Delete an initialized child Project
 * @method mixed deleteChildProjectTargetLanguage( array $input ) - Delete a target language of a child project's file
 * @method mixed deleteMasterProject( array $input ) - Delete an initialized master Project
 * @method mixed deleteMasterProjectFile( array $input ) - Delete a file of an initialized master Project
 * @method mixed deleteMasterProjectTargetLanguage( array $input ) - Delete a target language of a master project
 * @method mixed deleteProjectReviewSettings( array $input ) - Delete the review preferences of an initialized project
 * @method mixed deleteReviewTemplate( array $input ) - Remove the review template of the user
 * @method mixed deleteTemplate( array $input ) - Remove the project template of the user
 * @method mixed getBasicAttributesAggregate( array $input ) - Return an aggregate of DQF basic attributes
 * @method mixed getChildProject( array $input ) - Find the properties of a child Project
 * @method mixed getChildProjectFile( array $input ) - Find the details of a file
 * @method mixed getChildProjectFiles( array $input ) - Find the files of a child Project
 * @method mixed getChildProjectStatus( array $input ) - Get the project status
 * @method mixed getFileId( array $input ) - Return the DQF file id
 * @method mixed getChildProjectTargetLanguage( array $input ) - Find a target language of a child project
 * @method mixed getChildProjectTargetLanguages( array $input ) - Find the target languages of a child Project
 * @method mixed getMasterProject( array $input ) - Find the properties of a master Project
 * @method mixed getMasterProjectFile( array $input ) - Find a file of a master Project
 * @method mixed getMasterProjectFiles( array $input ) - Find the files of a master Project
 * @method mixed getMasterProjectTargetLanguage( array $input ) - Find a target language of a master project
 * @method mixed getMasterProjectTargetLanguages( array $input ) - Find the target languages of a master Project
 * @method mixed getProjectFileTargetLang( array $input ) - Find the target languages of a master Project
 * @method mixed getProjectId( array $input ) - Return the DQF project id
 * @method mixed getProjectReviewCycle( array $input ) - Get review children projects
 * @method mixed getProjectReviewSettings( array $input ) - Return the review preferences of a child project
 * @method mixed getReviewTemplate( array $input ) - Return the selected review template of the user
 * @method mixed getReviewTemplates( array $input ) - Return the review templates of a user
 * @method mixed getSegmentId( array $input ) - Return the DQF segment id
 * @method mixed getSourceSegmentIdsForAFile( array $input ) - Get all the source segment ids of a file
 * @method mixed getTemplate( array $input ) - Return the selected project template of the user
 * @method mixed getTemplates( array $input ) - Return the project templates of the user
 * @method mixed getTranslationForASegment( array $input ) - Get the translation of a source segment
 * @method mixed getTranslationId( array $input ) - Return the DQF translation id
 * @method mixed getTranslationsForSourceSegmentsInBatch( array $input ) - Get the multiple translation content
 * @method mixed getUser( array $input ) - Get an existing TAUS user
 * @method mixed login( array $input ) - Login to the DQF APIv3 service
 * @method mixed logout( array $input ) - Logout of the DQF APIv3 service
 * @method mixed updateChildProject( array $input ) - Update the properties of a child project
 * @method mixed updateChildProjectStatus( array $input ) - Update project status
 * @method mixed updateCompleteTranslatedSegment( array $input ) - Update a complete translated segment
 * @method mixed updateMasterProject( array $input ) - Update the master project
 * @method mixed updateMasterProjectFile( array $input ) - Update the file of a master project
 * @method mixed updateMasterProjectTargetLanguage( array $input ) - Update the project's review preferences
 * @method mixed updateProjectReviewSettings( array $input ) - Update the project's review preferences
 * @method mixed updateReviewInBatch( array $input ) - Add a review for a segment
 * @method mixed updateReviewTemplate( array $input ) - Update a review template of the user
 * @method mixed updateTemplate( array $input ) - Update a project template of the user
 * @method mixed updateTranslationForASegment( array $input ) - Update the translation of a source segment
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
        return new HttpClient([
            'base_uri' => ($debug) ? Constants::API_STAGING_URI : Constants::API_PRODUCTION_URI,
            'headers'  => [
                    'apiKey' => $this->clientParams[ 'apiKey' ]
            ],
            'handler'  => HandlerStackFactory::create($logStoragePath),
        ]);
    }

    /**
     * @return array
     */
    public function getClientParams()
    {
        return $this->clientParams;
    }

    /**
     * @return HttpClient
     */
    public function getHttpClient()
    {
        return $this->httpClient;
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
            throw new ParamsValidatorException($name . ' cannot be executed. ' . implode(',', $validate) . '.');
        }

        try {
            return $commandHandler->handle($params);
        } catch (\Exception $e) {
            throw new \Exception($e->getResponse()->getBody()->getContents());
        }
    }
}

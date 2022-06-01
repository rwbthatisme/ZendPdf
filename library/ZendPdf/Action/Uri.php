<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Pdf
 */

namespace ZendPdf\Action;

use SplObjectStorage;
use ZendPdf\Exception;
use ZendPdf\Exception\ExceptionInterface;
use ZendPdf\InternalType;
use ZendPdf\InternalType\DictionaryObject;

/**
 * PDF 'Resolve a uniform resource identifier' action
 *
 * A URI action causes a URI to be resolved.
 *
 * @package    Zend_PDF
 * @subpackage Zend_PDF_Action
 */
class Uri extends AbstractAction
{
    /**
     * Object constructor
     *
     * @param DictionaryObject $dictionary
     * @param SplObjectStorage $processedActions list of already processed action dictionaries,
     *                                                 used to avoid cyclic references
     * @throws ExceptionInterface
     */
    public function __construct(InternalType\AbstractTypeObject $dictionary, SplObjectStorage $processedActions)
    {
        parent::__construct($dictionary, $processedActions);

        if ($dictionary->URI === null) {
            throw new Exception\CorruptedPdfException('URI action dictionary entry is required');
        }
    }

    /**
     * Create new \ZendPdf\Action\Uri object using specified uri
     *
     * @param string $uri The URI to resolve, encoded in 7-bit ASCII
     * @param boolean $isMap A flag specifying whether to track the mouse position when the URI is resolved
     * @return Uri
     */
    public static function create($uri, $isMap = false)
    {
        self::_validateUri($uri);

        $dictionary = new DictionaryObject();
        $dictionary->Type = new InternalType\NameObject('Action');
        $dictionary->S = new InternalType\NameObject('URI');
        $dictionary->Next = null;
        $dictionary->URI = new InternalType\StringObject($uri);
        if ($isMap) {
            $dictionary->IsMap = new InternalType\BooleanObject(true);
        }

        return new self($dictionary, new SplObjectStorage());
    }

    /**
     * Validate URI
     *
     * @param string $uri
     * @return true
     * @throws ExceptionInterface
     */
    protected static function _validateUri($uri)
    {
        $scheme = parse_url((string)$uri, PHP_URL_SCHEME);
        if ($scheme === false || $scheme === null) {
            throw new Exception\InvalidArgumentException('Invalid URI');
        }
    }

    /**
     * Set URI to resolve
     *
     * @param string $uri The uri to resolve, encoded in 7-bit ASCII.
     * @return Uri
     */
    public function setUri($uri)
    {
        $this->_validateUri($uri);

        $this->_actionDictionary->touch();
        $this->_actionDictionary->URI = new InternalType\StringObject($uri);

        return $this;
    }

    /**
     * Get URI to resolve
     *
     * @return string
     */
    public function getUri()
    {
        return $this->_actionDictionary->URI->value;
    }

    /**
     * Set IsMap property
     *
     * If the IsMap flag is true and the user has triggered the URI action by clicking
     * an annotation, the coordinates of the mouse position at the time the action is
     * performed should be transformed from device space to user space and then offset
     * relative to the upper-left corner of the annotation rectangle.
     *
     * @param boolean $isMap A flag specifying whether to track the mouse position when the URI is resolved
     * @return Uri
     */
    public function setIsMap($isMap)
    {
        $this->_actionDictionary->touch();

        if ($isMap) {
            $this->_actionDictionary->IsMap = new InternalType\BooleanObject(true);
        } else {
            $this->_actionDictionary->IsMap = null;
        }

        return $this;
    }

    /**
     * Get IsMap property
     *
     * If the IsMap flag is true and the user has triggered the URI action by clicking
     * an annotation, the coordinates of the mouse position at the time the action is
     * performed should be transformed from device space to user space and then offset
     * relative to the upper-left corner of the annotation rectangle.
     *
     * @return boolean
     */
    public function getIsMap()
    {
        return $this->_actionDictionary->IsMap !== null &&
            $this->_actionDictionary->IsMap->value;
    }
}

<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Pdf
 */

namespace ZendPdf\Annotation;

use ZendPdf\Exception;
use ZendPdf\Exception\ExceptionInterface;
use ZendPdf\InternalType;
use ZendPdf\InternalType\AbstractTypeObject;
use ZendPdf\InternalType\DictionaryObject;
use ZendPdf\InternalType\IndirectObject;
use ZendPdf\InternalType\IndirectObjectReference;

/**
 * Abstract PDF annotation representation class
 *
 * An annotation associates an object such as a note, sound, or movie with a location
 * on a page of a PDF document, or provides a way to interact with the user by
 * means of the mouse and keyboard.
 *
 * @package    Zend_PDF
 * @subpackage Zend_PDF_Annotation
 */
abstract class AbstractAnnotation
{
    /**
     * Annotation dictionary
     *
     * @var DictionaryObject|IndirectObject|IndirectObjectReference
     */
    protected $_annotationDictionary;

    /**
     * Annotation object constructor
     *
     * @throws ExceptionInterface
     */
    public function __construct(AbstractTypeObject $annotationDictionary)
    {
        if ($annotationDictionary->getType() != AbstractTypeObject::TYPE_DICTIONARY) {
            throw new Exception\CorruptedPdfException('Annotation dictionary resource has to be a dictionary.');
        }

        $this->_annotationDictionary = $annotationDictionary;

        if ($this->_annotationDictionary->Type !== null &&
            $this->_annotationDictionary->Type->value != 'Annot') {
            throw new Exception\CorruptedPdfException('Wrong resource type. \'Annot\' expected.');
        }

        if ($this->_annotationDictionary->Rect === null) {
            throw new Exception\CorruptedPdfException('\'Rect\' dictionary entry is required.');
        }

        if (count($this->_annotationDictionary->Rect->items) != 4 ||
            $this->_annotationDictionary->Rect->items[0]->getType() != AbstractTypeObject::TYPE_NUMERIC ||
            $this->_annotationDictionary->Rect->items[1]->getType() != AbstractTypeObject::TYPE_NUMERIC ||
            $this->_annotationDictionary->Rect->items[2]->getType() != AbstractTypeObject::TYPE_NUMERIC ||
            $this->_annotationDictionary->Rect->items[3]->getType() != AbstractTypeObject::TYPE_NUMERIC) {
            throw new Exception\CorruptedPdfException('\'Rect\' dictionary entry must be an array of four numeric elements.');
        }
    }

    /**
     * Load Annotation object from a specified resource
     *
     * @param $destinationArray
     * @return AbstractAnnotation
     * @internal
     */
    public static function load(AbstractTypeObject $resource)
    {
        /** @todo implementation */
    }

    /**
     * Get annotation dictionary
     *
     * @return AbstractTypeObject
     * @internal
     */
    public function getResource()
    {
        return $this->_annotationDictionary;
    }

    /**
     * Set bottom edge of the annotation rectangle.
     *
     * @param float $bottom
     * @return AbstractAnnotation
     */
    public function setBottom($bottom)
    {
        $this->_annotationDictionary->Rect->items[1]->touch();
        $this->_annotationDictionary->Rect->items[1]->value = $bottom;

        return $this;
    }

    /**
     * Get bottom edge of the annotation rectangle.
     *
     * @return float
     */
    public function getBottom()
    {
        return $this->_annotationDictionary->Rect->items[1]->value;
    }

    /**
     * Set top edge of the annotation rectangle.
     *
     * @param float $top
     * @return AbstractAnnotation
     */
    public function setTop($top)
    {
        $this->_annotationDictionary->Rect->items[3]->touch();
        $this->_annotationDictionary->Rect->items[3]->value = $top;

        return $this;
    }

    /**
     * Get top edge of the annotation rectangle.
     *
     * @return float
     */
    public function getTop()
    {
        return $this->_annotationDictionary->Rect->items[3]->value;
    }

    /**
     * Set right edge of the annotation rectangle.
     *
     * @param float $right
     * @return AbstractAnnotation
     */
    public function setRight($right)
    {
        $this->_annotationDictionary->Rect->items[2]->touch();
        $this->_annotationDictionary->Rect->items[2]->value = $right;

        return $this;
    }

    /**
     * Get right edge of the annotation rectangle.
     *
     * @return float
     */
    public function getRight()
    {
        return $this->_annotationDictionary->Rect->items[2]->value;
    }

    /**
     * Set left edge of the annotation rectangle.
     *
     * @param float $left
     * @return AbstractAnnotation
     */
    public function setLeft($left)
    {
        $this->_annotationDictionary->Rect->items[0]->touch();
        $this->_annotationDictionary->Rect->items[0]->value = $left;

        return $this;
    }

    /**
     * Get left edge of the annotation rectangle.
     *
     * @return float
     */
    public function getLeft()
    {
        return $this->_annotationDictionary->Rect->items[0]->value;
    }

    /**
     * Return text to be displayed for the annotation or, if this type of annotation
     * does not display text, an alternate description of the annotation’s contents
     * in human-readable form.
     *
     * @return string
     */
    public function getText()
    {
        if ($this->_annotationDictionary->Contents === null) {
            return '';
        }

        return $this->_annotationDictionary->Contents->value;
    }

    /**
     * Set text to be displayed for the annotation or, if this type of annotation
     * does not display text, an alternate description of the annotation’s contents
     * in human-readable form.
     *
     * @param string $text
     * @return AbstractAnnotation
     */
    public function setText($text)
    {
        if ($this->_annotationDictionary->Contents === null) {
            $this->_annotationDictionary->touch();
            $this->_annotationDictionary->Contents = new InternalType\StringObject($text);
        } else {
            $this->_annotationDictionary->Contents->touch();
            $this->_annotationDictionary->Contents->value = new InternalType\StringObject($text);
        }

        return $this;
    }
}

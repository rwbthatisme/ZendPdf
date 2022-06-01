<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Pdf
 */

namespace ZendPdf\InternalType;

use ZendPdf\Exception;
use ZendPdf\Exception\ExceptionInterface;
use ZendPdf\ObjectFactory;

/**
 * PDF file 'dictionary' element implementation
 *
 * @category   Zend
 * @package    Zend_PDF
 * @subpackage Zend_PDF_Internal
 */
class DictionaryObject extends AbstractTypeObject
{
    /**
     * Dictionary elements
     * Array of \ZendPdf\InternalType objects ('name' => \ZendPdf\InternalType\AbstractTypeObject)
     *
     * @var array
     */
    private array $_items = [];

    public object $Type;
    public object $S;
    public object $Next;
    public object $URI;
    public object $IsMap;


    /**
     * Object constructor
     *
     * @param array|null $val - array of \ZendPdf\InternalType\AbstractTypeObject objects
     * @throws ExceptionInterface
     */
    public function __construct(array $val = null)
    {
        if ($val === null) {
            return;
        } elseif (!is_array($val)) {
            throw new Exception\RuntimeException('Argument must be an array');
        }

        foreach ($val as $name => $element) {
            if (!$element instanceof AbstractTypeObject) {
                throw new Exception\RuntimeException('Array elements must be \ZendPdf\InternalType\AbstractTypeObject objects');
            }
            if (!is_string($name)) {
                throw new Exception\RuntimeException('Array keys must be strings');
            }
            $this->_items[$name] = $element;
        }
    }


    /**
     * Add element to an array
     *
     * @name NameObject $name
     * @param AbstractTypeObject $val - \ZendPdf\InternalType\AbstractTypeObject object
     * @throws ExceptionInterface
     */
    public function add(NameObject $name, AbstractTypeObject $val)
    {
        $this->_items[$name->value] = $val;
    }

    /**
     * Return dictionary keys
     *
     * @return array
     */
    public function getKeys(): array
    {
        return array_keys($this->_items);
    }


    /**
     * Get handler
     *
     * @param $item
     * @return AbstractTypeObject | null
     */
    public function __get($item)
    {
        return $this->_items[$item] ?? null;
    }

    /**
     * Set handler
     *
     * @param string $item
     * @param mixed $value
     */
    public function __set(string $item, mixed $value)
    {
        if ($value === null) {
            unset($this->_items[$item]);
        } else {
            $this->_items[$item] = $value;
        }
    }

    /**
     * Return type of the element.
     *
     * @return integer
     */
    public function getType(): int
    {
        return AbstractTypeObject::TYPE_DICTIONARY;
    }

    /**
     * Return object as string
     *
     * @param ObjectFactory|null $factory
     * @return string
     */
    public function toString(ObjectFactory $factory = null): string
    {
        $outStr = '<<';
        $lastNL = 0;

        foreach ($this->_items as $name => $element) {
            if (!is_object($element)) {
                throw new Exception\RuntimeException('Wrong data');
            }

            if (strlen($outStr) - $lastNL > 128) {
                $outStr .= "\n";
                $lastNL = strlen($outStr);
            }

            $nameObj = new NameObject($name);
            $outStr .= $nameObj->toString($factory) . ' ' . $element->toString($factory) . ' ';
        }
        $outStr .= '>>';

        return $outStr;
    }

    /**
     * Detach PDF object from the factory (if applicable), clone it and attach to new factory.
     *
     * @param ObjectFactory $factory The factory to attach
     * @param array &$processed List of already processed indirect objects, used to avoid objects duplication
     * @param integer $mode Cloning mode (defines filter for objects cloning)
     * @returns AbstractTypeObject
     * @throws ExceptionInterface
     */
    public function makeClone(ObjectFactory $factory, array &$processed, $mode): DictionaryObject|NullObject
    {
        if (isset($this->_items['Type'])) {
            if ($this->_items['Type']->value == 'Pages') {
                // It's a page tree node
                // skip it and its children
                return new NullObject();
            }

            if ($this->_items['Type']->value == 'Page' &&
                $mode == AbstractTypeObject::CLONE_MODE_SKIP_PAGES
            ) {
                // It's a page node, skip it
                return new NullObject();
            }
        }

        $newDictionary = new self();
        foreach ($this->_items as $key => $value) {
            $newDictionary->_items[$key] = $value->makeClone($factory, $processed, $mode);
        }

        return $newDictionary;
    }

    /**
     * Set top level parent indirect object.
     *
     * @param IndirectObject $parent
     */
    public function setParentObject(IndirectObject $parent)
    {
        parent::setParentObject($parent);

        foreach ($this->_items as $item) {
            $item->setParentObject($parent);
        }
    }

    /**
     * Convert PDF element to PHP type.
     *
     * Dictionary is returned as an associative array
     *
     * @return array
     */
    public function toPhp(): array
    {
        $phpArray = [];

        foreach ($this->_items as $itemName => $item) {
            $phpArray[$itemName] = $item->toPhp();
        }

        return $phpArray;
    }
}

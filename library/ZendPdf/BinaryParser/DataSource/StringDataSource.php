<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Pdf
 */

namespace ZendPdf\BinaryParser\DataSource;

use ZendPdf\Exception;
use ZendPdf\Exception\ExceptionInterface;

/**
 * Concrete subclass of {@link \ZendPdf\BinaryParser\DataSource\AbstractDataSource}
 * that provides an interface to binary strings.
 *
 * @package    Zend_PDF
 * @subpackage Zend_PDF_BinaryParser
 */
class StringDataSource extends AbstractDataSource
{
    /**** Instance Variables ****/


    /**
     * The string to parse.
     * @var StringDataSource
     */
    protected $_string = '';


    /**** Public Interface ****/


    /* Concrete Class Implementation */

    /**
     * Object constructor.
     *
     * Verifies that the string is not empty.
     *
     * @param StringDataSource $string StringDataSource to parse.
     */
    public function __construct($string)
    {
        if (empty($string)) {
            throw new Exception\InvalidArgumentException('StringDataSource is empty');
        }
        $this->_size = strlen($string);
        $this->_string = $string;
    }

    /**
     * Object destructor.
     */
    public function __destruct()
    {
        $this->_string = '';
    }

    /**
     * Returns the specified number of raw bytes from the string at the byte
     * offset of the current read position.
     *
     * Advances the read position by the number of bytes read.
     *
     * Throws an exception if there is insufficient data to completely fulfill
     * the request.
     *
     * @param integer $byteCount Number of bytes to read.
     * @return StringDataSource
     * @throws ExceptionInterface
     */
    public function readBytes($byteCount)
    {
        if (($this->_offset + $byteCount) > $this->_size) {
            throw new Exception\LengthException("Insufficient data to read $byteCount bytes");
        }
        $bytes = substr($this->_string, $this->_offset, $byteCount);
        $this->_offset += $byteCount;
        return $bytes;
    }

    /**
     * Returns the entire string.
     *
     * Preserves the current read position.
     *
     * @return StringDataSource
     */
    public function readAllBytes()
    {
        return $this->_string;
    }


    /* Object Magic Methods */

    /**
     * Returns a string containing the parsed string's length.
     *
     * @return StringDataSource
     */
    public function __toString()
    {
        return "StringDataSource ($this->_size bytes)";
    }
}
